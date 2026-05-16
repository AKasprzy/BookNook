<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorStatResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'author' => $this->author,
            'count' => (int) $this->count,
        ];
    }
}
