<?php
namespace App\Services;

use App\Models\Episode;
use App\Models\Season;

class EpisodeService
{
    /*
    |--------------------------------
    | CREATE EPISODE
    |--------------------------------
    */

    public function create(Season $season, array $data): Episode
    {
        return $season->episodes()->create([
            'number' => $data['number'],
            'title' => $data['title'] ?? null,
            'duration' => $data['duration'] ?? null,
            'video_url' => $data['video_url'] ?? null,
        ]);
    }

    /*
    |--------------------------------
    | UPDATE EPISODE
    |--------------------------------
    */

    public function update(Episode $episode, array $data): Episode
    {
        $episode->update([
            'number' => $data['number'] ?? $episode->number,
            'title' => $data['title'] ?? $episode->title,
            'duration' => $data['duration'] ?? $episode->duration,
            'video_url' => $data['video_url'] ?? $episode->video_url,
        ]);

        return $episode;
    }

    /*
    |--------------------------------
    | DELETE EPISODE
    |--------------------------------
    */

    public function delete(Episode $episode): bool
    {
        return $episode->delete();
    }
}