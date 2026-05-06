<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFavoriteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'media' => new MediaResource($this->whenLoaded('media')),
        ];
    }
}
