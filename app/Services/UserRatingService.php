<?php

namespace App\Services;

use App\Models\UserRating;

class UserRatingService
{
    public function setRating(int $userId, int $mediaId, int $rating): UserRating
    {
        return UserRating::updateOrCreate(
            [
                'user_id' => $userId,
                'media_id' => $mediaId,
            ],
            [
                'rating' => $rating,
            ]
        );
    }

    public function deleteRating(UserRating $rating): void
    {
        $rating->delete();
    }
}
