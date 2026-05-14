<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'book_edition_id' => $this->book_edition_id,
            'rating' => $this->rating,
            'review_text' => $this->review_text,
            'spoiler' => $this->spoiler,
            'reread' => $this->reread,
            'reviewed_at' => $this->reviewed_at,
            'created_at' => $this->created_at,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
            'edition' => [
                'id' => $this->bookEdition->id,
                'title' => $this->bookEdition->edition_title,
                'isbn' => $this->bookEdition->isbn,
                'format' => $this->bookEdition->format,
                'language' => $this->bookEdition->edition_language,
            ],
            'book' => [
                'id' => $this->bookEdition->book->id,
                'title' => $this->bookEdition->book->title,
                'author' => $this->bookEdition->book->author,
            ],
        ];
    }
}
