<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserMedia;

class UserMediaService
{
    /*
    |--------------------------------
    | CREATE OR UPDATE USER MEDIA
    |--------------------------------
    */
    public function create(User $user, array $data): UserMedia
    {
        return UserMedia::updateOrCreate(
            [
                'user_id'  => $user->id,
                'media_id' => $data['media_id'],
            ],
            [
                'status' => $data['status'] ?? 'planned',
            ]
        );
    }

    /*
    |--------------------------------
    | UPDATE USER MEDIA
    |--------------------------------
    */
    public function update(UserMedia $userMedia, array $data): UserMedia
    {
        $userMedia->update($data);

        return $userMedia;
    }

    /*
    |--------------------------------
    | DELETE USER MEDIA
    |--------------------------------
    */
    public function delete(UserMedia $userMedia): void
    {
        $userMedia->delete();
    }

    /*
    |--------------------------------
    | MARK AS COMPLETED
    |--------------------------------
    */
    public function markAsCompleted(UserMedia $userMedia): UserMedia
    {
        $userMedia->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return $userMedia;
    }
}
