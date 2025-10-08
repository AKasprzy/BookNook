<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookEditionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'book_id' => $this->book_id,
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
            'average_rating' => $this->average_rating,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
