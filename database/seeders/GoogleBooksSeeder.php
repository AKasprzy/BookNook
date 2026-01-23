<?php

namespace Database\Seeders;

use App\Enums\BookFormat;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class GoogleBooksSeeder extends Seeder
{
    public function run(): void
    {
        $queries = [
            'fantasy',
            'science fiction',
            'mystery',
            'thriller',
            'romance',
            'horror',
            'young adult',
            'historical fiction',
            'adventure',
            'nonfiction',
        ];

        $scored = [];

        foreach ($queries as $query) {
            for ($start = 0; $start < 120; $start += 40) {
                $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
                    'q' => 'subject:'.$query,
                    'printType' => 'books',
                    'orderBy' => 'relevance',
                    'startIndex' => $start,
                    'maxResults' => 40,
                ]);

                if (! $response->successful()) {
                    continue;
                }

                $items = $response->json('items') ?? [];

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
            }
        }

        usort($scored, fn ($a, $b) => $b['rating_count'] <=> $a['rating_count']);

        $top = array_slice($scored, 0, 100);

        foreach ($top as $entry) {
            $data = $entry['data'];

            $book = Book::query()->firstOrCreate(
                [
                    'title' => $data['title'],
                    'author' => $data['author'],
                ],
                [
                    'original_language' => $data['language'],
                    'original_publication_date' => $data['published'],
                ]
            );

            $book->editions()->firstOrCreate(
                [
                    'isbn' => $data['isbn'],
                ],
                [
                    'edition_title' => $data['title'],
                    'edition_language' => $data['language'],
                    'format' => BookFormat::Print->value,
                    'description' => $data['description'],
                    'page_count' => $data['page_count'],
                    'cover_url' => $data['cover'],
                    'publisher' => $data['publisher'],
                    'edition_publication_date' => $data['published'],
                ]
            );

            foreach ($data['categories'] as $cat) {
                $parts = explode('/', $cat);
                foreach ($parts as $name) {
                    $name = trim($name);
                    if ($name === '') {
                        continue;
                    }
                    $genre = Genre::query()->firstOrCreate(['name' => $name]);
                    $book->genres()->syncWithoutDetaching($genre->id);
                }
            }
        }
    }

    private function normalize(array $item): ?array
    {
        $info = $item['volumeInfo'] ?? [];

        if (! isset($info['title'])) {
            return null;
        }
        if (! isset($info['authors'])) {
            return null;
        }

        return [
            'title' => $info['title'],
            'author' => implode(', ', $info['authors']),
            'language' => $info['language'] ?? 'en',
            'published' => $this->parseDate($info['publishedDate'] ?? null),
            'description' => $info['description'] ?? null,
            'isbn' => $this->extractIsbn($info['industryIdentifiers'] ?? []),
            'page_count' => $info['pageCount'] ?? null,
            'cover' => $info['imageLinks']['thumbnail'] ?? null,
            'publisher' => $info['publisher'] ?? null,
            'categories' => $info['categories'] ?? [],
        ];
    }

    private function parseDate(?string $date): ?string
    {
        if (! $date) {
            return null;
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
