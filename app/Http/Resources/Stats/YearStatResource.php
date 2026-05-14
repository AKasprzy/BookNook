<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Resources\Json\JsonResource;

class YearStatResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'year' => $this->year,
            'count' => (int) $this->count,
        ];
    }
}
