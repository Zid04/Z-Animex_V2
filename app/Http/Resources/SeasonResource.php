<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'media_id' => $this->media_id,
            'number' => $this->number,
'episodes_count' => $this->whenCounted('episodes'),

            'episodes' => EpisodeResource::collection(
                $this->whenLoaded('episodes')
            ),
        ];
    }
}
    