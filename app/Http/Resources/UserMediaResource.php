<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'media_id' => $this->media_id,
            'status' => $this->status,
            'progress' => $this->progress,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,

            'media' => new MediaResource($this->whenLoaded('media')),
        ];
    }
}
