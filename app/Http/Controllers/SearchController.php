<?php

namespace App\Http\Controllers;

use App\Enums\BookFormat;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function search(Request $request)
    {
        $query = trim((string) $request->input('q'));

        if ($query === '') {
            return response()->json([]);
        }

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'printType' => 'books',
            'maxResults' => 40,
        ]);

        if (! $response->successful()) {
            return response()->json([]);
        }

        $items = $response->json('items') ?? [];
        $scored = [];

        foreach ($items as $item) {
            $info = $item['volumeInfo'] ?? [];

            if (! isset($info['ratingsCount'])) {
                continue;
            }

            $data = $this->normalize($item);

            if (! $data) {
                continue;
            }

            $scored[] = [
                'rating_count' => $info['ratingsCount'],
                'data' => $data,
            ];
        }

        usort($scored, fn ($a, $b) => $b['rating_count'] <=> $a['rating_count']);

        $top = array_slice($scored, 0, 5);
        $results = [];

        foreach ($top as $entry) {
            $data = $entry['data'];

            if (! $data['title'] || ! $data['author'] || ! $data['isbn']) {
                continue;
            }

            $book = Book::query()->firstOrCreate(
                [
                    'title' => $data['title'],
                    'author' => $data['author'],
                ],
                [
                    'original_language' => $data['language'] ?? 'en',
                    'original_publication_date' => $data['published'] ?? now()->toDateString(),
                ]
            );

            $book->editions()->firstOrCreate(
                [
                    'isbn' => $data['isbn'],
                ],
                [
                    'edition_title' => $data['title'],
                    'edition_language' => $data['language'] ?? 'en',
                    'format' => BookFormat::Print->value,
                    'description' => $data['description'] ?? '',
                    'page_count' => $data['page_count'] ?? 0,
                    'cover_url' => $data['cover'] ?? '',
                    'publisher' => $data['publisher'] ?? '',
                    'edition_publication_date' => $data['published'] ?? now()->toDateString(),
                ]
            );

            if (! empty($data['categories'])) {
                $firstCategory = $data['categories'][0];
                $parts = explode('/', $firstCategory);

                $genreNames = [];
                foreach ($parts as $p) {
                    $name = trim($p);
                    if ($name !== '') {
                        $genreNames[] = $name;
                    }
                }

                foreach ($genreNames as $name) {
                    Genre::query()->firstOrCreate(['name' => $name]);
                }

                if (isset($genreNames[0])) {
                    $genre = Genre::where('name', $genreNames[0])->first();
                    if ($genre) {
                        $book->genres()->syncWithoutDetaching($genre->id);
                    }
                }
            }

            $results[] = $book->load('editions', 'genres');
        }

        return response()->json($results);
    }

    private function normalize(array $item): ?array
    {
        $info = $item['volumeInfo'] ?? [];

        if (empty($info['title']) || empty($info['authors'])) {
            return null;
        }

        $isbn = $this->extractIsbn($info['industryIdentifiers'] ?? []);
        if (! $isbn) {
            return null;
        }

        return [
            'title' => $info['title'],
            'author' => implode(', ', $info['authors']),
            'language' => $info['language'] ?? 'en',
            'published' => $this->parseDate($info['publishedDate'] ?? null),
            'description' => $info['description'] ?? '',
            'isbn' => $isbn,
            'page_count' => $info['pageCount'] ?? 0,
            'cover' => $info['imageLinks']['thumbnail'] ?? '',
            'publisher' => $info['publisher'] ?? '',
            'categories' => $info['categories'] ?? [],
        ];
    }

    private function parseDate(?string $date): ?string
    {
        if (! $date) {
            return now()->toDateString();
        }
        if (strlen($date) === 4) {
            return $date.'-01-01';
        }
        if (strlen($date) === 7) {
            return $date.'-01';
        }

        return $date;
    }

    private function extractIsbn(array $ids): ?string
    {
        foreach ($ids as $id) {
            if ($id['type'] === 'ISBN_13') {
                return $id['identifier'];
            }
        }

        return null;
    }
}
