<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Resources\Json\JsonResource;

class TimelineStatResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'date' => $this->date,
            'count' => (int) $this->count,
        ];
    }
}
