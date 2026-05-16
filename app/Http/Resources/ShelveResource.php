<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShelveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'book_edition_id' => $this->book_edition_id,
            'status' => $this->status,
            'times_read' => $this->times_read,
            'favourite' => $this->favourite,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'edition' => $this->whenLoaded('bookEdition', function () {
                return [
                    'id' => $this->bookEdition->id,
                    'edition_title' => $this->bookEdition->edition_title,
                    'cover_url' => $this->bookEdition->cover_url,
                    'format' => $this->bookEdition->format,

                    'book' => [
                        'id' => $this->bookEdition->book->id,
                        'title' => $this->bookEdition->book->title,
                        'author' => $this->bookEdition->book->author,
                        'cover_url' => $this->bookEdition->book->cover_url,
                    ],
                ];
            }),
        ];
    }
}
