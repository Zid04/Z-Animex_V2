<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Season;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SeasonService
{
    /*
    |--------------------------------
    | CREATE SEASON
    |--------------------------------
    */
    public function create(Media $media, array $data): Season
    {
        return $media->seasons()->create([
            'number' => $data['number'],
        ]);
    }

    /*
    |--------------------------------
    | UPDATE SEASON
    |--------------------------------
    */
    public function update(Season $season, array $data): Season
    {
        $season->update([
            'number' => $data['number'],
        ]);

        return $season;
    }

    /*
    |--------------------------------
    | DELETE SEASON
    |--------------------------------
    */
    public function delete(Season $season): bool
    {
        return $season->delete();
    }

    /*
    |--------------------------------
    | ENSURE SEASON BELONGS TO MEDIA
    |--------------------------------
    */
    public function ensureBelongsToMedia(Season $season, Media $media): void
    {
        if ($season->media_id !== $media->id) {
            throw new NotFoundHttpException();
        }
    }

    /*
    |--------------------------------
    | LIST SEASONS FOR MEDIA
    |--------------------------------
    */
    public function list(Media $media): Collection
    {
        return $media->seasons()
            ->with('episodes')
            ->withCount('episodes')
            ->get();
    }
}
