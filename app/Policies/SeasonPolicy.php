<?php
namespace App\Policies;

use App\Models\Season;
use App\Models\User;

class SeasonPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Season $season): bool
    {
        return $season->media->user_id === $user->id;
    }

    public function delete(User $user, Season $season): bool
    {
        return $season->media->user_id === $user->id;
    }
}