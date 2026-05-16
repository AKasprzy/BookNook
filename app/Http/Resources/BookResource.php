<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'original_language' => $this->original_language,
            'author' => $this->author,
            'original_publication_date' => $this->original_publication_date,
            'series' => $this->series,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'genres' => GenreResource::collection($this->whenLoaded('genres')),
            'motifs' => MotifResource::collection($this->whenLoaded('motifs')),
            'editions' => BookEditionResource::collection($this->whenLoaded('editions')),
        ];
    }
}
