<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRatingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'media_id' => $this->media_id,
            'rating' => $this->rating,
            'created_at' => $this->created_at,
        ];
    }
}