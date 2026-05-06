<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\UserRating;
use App\Services\UserRatingService;
use App\Http\Requests\StoreUserRatingRequest;
use App\Http\Resources\UserRatingResource;

class UserRatingController extends Controller
{
    public function __construct(
        private UserRatingService $service
    ) {}

    public function store(StoreUserRatingRequest $request, Media $media)
    {
        $this->authorize('create', [UserRating::class, $media]);

        $rating = $this->service->setRating(
            auth()->id(),
            $media->id,
            $request->validated()['rating']
        );

        return new UserRatingResource($rating);
    }

    public function destroy(Media $media)
    {
        $rating = UserRating::where('user_id', auth()->id())
            ->where('media_id', $media->id)
            ->firstOrFail();

        $this->authorize('delete', $rating);
        $this->service->deleteRating($rating);

        return response()->json([
            'message' => 'Rating deleted'
        ]);
    }
}