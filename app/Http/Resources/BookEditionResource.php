<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookEditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $averageRating = Review::where('book_id', $this->book_id)->avg('rating');
        $averageRating = $averageRating !== null ? (float) round($averageRating, 1) : null;

        return [
            'id' => $this->id,
            'edition_title' => $this->edition_title,
            'edition_publication_date' => $this->edition_publication_date,
            'format' => $this->format->value ?? $this->format,
            'edition_language' => $this->edition_language,
            'description' => $this->description,
            'isbn' => $this->isbn,
            'page_count' => $this->page_count,
            'length_minutes' => $this->length_minutes,
            'cover_url' => $this->cover_url,
            'publisher' => $this->publisher,
            'average_rating' => $averageRating,
            'book' => [
                'id' => $this->book->id,
                'title' => $this->book->title,
                'author' => $this->book->author,
                'series' => $this->book->series,
                'original_publication_date' => $this->book->original_publication_date,
                'original_language' => $this->book->original_language,
                'created_at' => $this->book->created_at,
                'updated_at' => $this->book->updated_at,
            ],
            'genres' => GenreResource::collection($this->book->genres),
            'motifs' => MotifResource::collection($this->book->motifs),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
