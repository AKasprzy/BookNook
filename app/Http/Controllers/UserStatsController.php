<?php

namespace App\Http\Controllers;

use App\Http\Resources\Stats\AuthorStatResource;
use App\Http\Resources\Stats\TimelineStatResource;
use App\Http\Resources\Stats\YearStatResource;
use App\Models\Shelve;
use Illuminate\Http\Request;

class UserStatsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        return response()->json([
            'status_distribution' => $this->statusDistribution($userId),
            'formats' => $this->formats($userId),
            'top_authors' => AuthorStatResource::collection($this->topAuthors($userId)),
            'reading_over_time' => TimelineStatResource::collection($this->readingOverTime($userId)),
            'favourites_ratio' => $this->favouritesRatio($userId),
            'times_read_distribution' => $this->timesRead($userId),
            'books_per_year' => YearStatResource::collection($this->booksPerYear($userId)),
            'languages' => $this->languages($userId),
            'pages_distribution' => $this->pages($userId),
        ]);
    }

    private function baseQuery($userId)
    {
        return Shelve::query()
            ->where('shelves.user_id', $userId);
    }

    private function statusDistribution($userId)
    {
        return $this->baseQuery($userId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    private function formats($userId)
    {
        return $this->baseQuery($userId)
            ->join('book_editions', 'shelves.book_edition_id', '=', 'book_editions.id')
            ->selectRaw('book_editions.format as format, COUNT(*) as count')
            ->groupBy('book_editions.format')
            ->pluck('count', 'format');
    }

    private function topAuthors($userId)
    {
        return $this->baseQuery($userId)
            ->join('book_editions', 'shelves.book_edition_id', '=', 'book_editions.id')
            ->join('books', 'book_editions.book_id', '=', 'books.id')
            ->selectRaw('books.author as author, COUNT(*) as count')
            ->groupBy('books.author')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
    }

    private function readingOverTime($userId)
    {
        return $this->baseQuery($userId)
            ->where('status', 'read')
            ->selectRaw('DATE(shelves.created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function favouritesRatio($userId)
    {
        return [
            'favourite' => $this->baseQuery($userId)->where('favourite', true)->count(),
            'not_favourite' => $this->baseQuery($userId)->where('favourite', false)->count(),
        ];
    }

    private function timesRead($userId)
    {
        return $this->baseQuery($userId)
            ->selectRaw('times_read, COUNT(*) as count')
            ->groupBy('times_read')
            ->orderBy('times_read')
            ->get();
    }

    private function booksPerYear($userId)
    {
        return $this->baseQuery($userId)
            ->join('book_editions', 'shelves.book_edition_id', '=', 'book_editions.id')
            ->selectRaw('EXTRACT(YEAR FROM book_editions.edition_publication_date) as year, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get();
    }

    private function languages($userId)
    {
        return $this->baseQuery($userId)
            ->join('book_editions', 'shelves.book_edition_id', '=', 'book_editions.id')
            ->selectRaw('book_editions.edition_language as edition_language, COUNT(*) as count')
            ->groupBy('edition_language')
            ->pluck('count', 'edition_language');
    }

    private function pages($userId)
    {
        return $this->baseQuery($userId)
            ->join('book_editions', 'shelves.book_edition_id', '=', 'book_editions.id')
            ->selectRaw("
                CASE
                    WHEN book_editions.page_count < 200 THEN 'short'
                    WHEN book_editions.page_count < 400 THEN 'medium'
                    ELSE 'long'
                END as bucket,
                COUNT(*) as count
            ")
            ->groupBy('bucket')
            ->pluck('count', 'bucket');
    }
}
