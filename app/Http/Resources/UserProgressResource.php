<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProgressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'episode_id' => $this->episode_id,
            'watched_at' => $this->watched_at,
        ];
    }
}