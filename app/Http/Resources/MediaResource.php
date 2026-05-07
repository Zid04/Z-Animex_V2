<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray($request): array
{
    return [
        'id'          => $this->id,
        'external_id' => $this->external_id,
        'type'        => $this->type,
        'source'      => $this->source,
        'title'       => $this->title,
        'description' => $this->description,
        'images'      => $this->images ?? [],
        'status'      => $this->status,
        'airing'      => $this->airing,
        'episodes_count' => $this->episodes()->count(),
        'duration'    => $this->duration,
        'year'        => $this->year,
        'cover'       => $this->cover,
        'score'       => $this->score,
        'scored_by'   => $this->scored_by,
        'rank'        => $this->rank,
        'popularity'  => $this->popularity,
        'members'     => $this->members,
        'favorites'   => $this->favorites,
        'approved'    => $this->approved,
        'is_public'   => $this->is_public,
        'studios'     => $this->studios ?? [],
        'genres'      => $this->genres ?? [],

        'average_rating' => $this->averageRating(),

        'tags'     => TagResource::collection($this->tags ?? []),
        'seasons'  => SeasonResource::collection($this->seasons ?? []),
        'comments' => CommentResource::collection($this->comments ?? []),

        'user_data' => $this->when(auth()->check(), fn () => [
            'is_favorite' => $this->favorites()
                ->where('user_id', auth()->id())
                ->exists(),
            'rating' => $this->ratings()
                ->where('user_id', auth()->id())
                ->value('rating'),
            'user_media' => $this->userMedia()
                ->where('user_id', auth()->id())
                ->first()?->only(['status', 'progress', 'started_at', 'completed_at']),
        ]),
    ];
}

}
