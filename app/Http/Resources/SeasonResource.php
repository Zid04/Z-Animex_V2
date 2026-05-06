<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'season_id' => $this->season_id,
            'number' => $this->number,
            'title' => $this->title,
            'duration' => $this->duration,
            'video_url' => $this->video_url,

            'watched' => auth()->check()
                ? $this->isWatchedBy(auth()->id())
                : false,
        ];
    }
}