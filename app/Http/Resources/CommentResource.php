<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,

            'user' => $this->whenLoaded('user', fn () => [
    'id'     => $this->user->id,
    'name'   => $this->user->name,
    'avatar' => $this->user->avatar,
]),

            'media_id' => $this->media_id,

            'created_at' => $this->created_at,
        ];
    }
}