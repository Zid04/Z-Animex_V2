<?php
namespace App\Services;

use App\Models\User;
use App\Models\Episode;

class UserProgressService
{
    public function markAsWatched(User $user, Episode $episode): void
    {
        $user->episodes()->syncWithoutDetaching([
            $episode->id => ['watched_at' => now()],
        ]);
    }

    public function unmarkAsWatched(User $user, Episode $episode): void
    {
        $user->episodes()->detach($episode->id);
    }
}