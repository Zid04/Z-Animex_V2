<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEpisodeRequest;
use App\Http\Requests\UpdateEpisodeRequest;
use App\Http\Resources\EpisodeResource;
use App\Models\Media;
use App\Models\Season;
use App\Models\Episode;
use App\Services\EpisodeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EpisodeController extends Controller
{
    use AuthorizesRequests;
    
    public function __construct(
        private EpisodeService $service
    ) {}

    public function store(StoreEpisodeRequest $request, Media $media, Season $season)
    {
        $this->authorize('create', Episode::class);

        abort_unless($season->media_id === $media->id, 404);

        $episode = $this->service->create(
            $season,
            $request->validated()
        );

        return new EpisodeResource($episode);
    }

    public function update(UpdateEpisodeRequest $request, Media $media, Season $season, Episode $episode)
    {
        $this->authorize('update', $episode);

        $this->service->ensureBelongsToSeason($episode, $season);

        $episode = $this->service->update(
            $episode,
            $request->validated()
        );

        return new EpisodeResource($episode);
    }

    public function destroy(Media $media, Season $season, Episode $episode)
    {
        $this->authorize('delete', $episode);

        $this->service->ensureBelongsToSeason($episode, $season);

        $this->service->delete($episode);

        return response()->json([
            'message' => 'Episode deleted'
        ]);
    }
}