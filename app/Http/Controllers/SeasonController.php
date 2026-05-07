<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSeasonRequest;
use App\Http\Requests\UpdateSeasonRequest;
use App\Http\Resources\SeasonResource;
use App\Models\Media;
use App\Models\Season;
use App\Services\SeasonService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SeasonController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private SeasonService $service
    ) {}

    /*
    |--------------------------------
    | INDEX
    |--------------------------------
    */

    public function index(Media $media)
    {
        return SeasonResource::collection(
            $this->service->list($media)
        );
    }

    /*
    |--------------------------------
    | STORE
    |--------------------------------
    */

    public function store(StoreSeasonRequest $request, Media $media)
    {
        $this->authorize('update', $media);

        $season = $this->service->create(
            $media,
            $request->validated()
        );

        return new SeasonResource($season);
    }

    /*
    |--------------------------------
    | UPDATE
    |--------------------------------
    */

    public function update(UpdateSeasonRequest $request, Media $media, Season $season)
    {
        $this->authorize('update', $media);

        $this->service->ensureBelongsToMedia($season, $media);

        $season = $this->service->update(
            $season,
            $request->validated()
        );

        return new SeasonResource($season);
    }

    /*
    |--------------------------------
    | DELETE
    |--------------------------------
    */

    public function destroy(Media $media, Season $season)
    {
        $this->authorize('update', $media);

        $this->service->ensureBelongsToMedia($season, $media);

        $this->service->delete($season);

        return response()->json([
            'message' => 'Season deleted'
        ]);
    }
}