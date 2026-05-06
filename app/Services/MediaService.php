<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaService
{
    /*
    |--------------------------------
    | LIST MEDIA WITH FILTERS
    |--------------------------------
    */
    public function index(Request $request)
    {
        $query = Media::query()
            ->with('tags');

        // Search
        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Type filter
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Year filter
        if ($year = $request->get('year')) {
            $query->where('year', $year);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Airing filter
        if ($request->has('airing')) {
            $query->where('airing', $request->boolean('airing'));
        }

        // Sorting
        if ($sort = $request->get('sort')) {
            match ($sort) {
                'score'       => $query->orderByDesc('score'),
                'rank'        => $query->orderBy('rank'),
                'popularity'  => $query->orderBy('popularity'),
                'newest'      => $query->latest(),
                'oldest'      => $query->oldest(),
                default       => $query->latest(),
            };
        } else {
            $query->latest();
        }

        return $query->paginate(20);
    }

    /*
    |--------------------------------
    | SHOW MEDIA DETAILS
    |--------------------------------
    */
    public function show(Media $media): Media
    {
        return $media->load([
            'seasons.episodes',
            'tags',
            'comments.user',
            'ratings',
        ]);
    }

    /*
    |--------------------------------
    | CREATE MEDIA
    |--------------------------------
    */
    public function create(array $data): Media
    {
        return Media::create($data);
    }

    /*
    |--------------------------------
    | UPDATE MEDIA
    |--------------------------------
    */
    public function update(Media $media, array $data): Media
    {
        $media->update($data);

        return $media;
    }

    /*
    |--------------------------------
    | DELETE MEDIA
    |--------------------------------
    */
    public function delete(Media $media): void
    {
        $media->delete();
    }
}
