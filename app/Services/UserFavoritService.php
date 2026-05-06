<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserFavoriteService
{
    /*
    |--------------------------------
    | LIST USER FAVORITES
    |--------------------------------
    */
    public function list(User $user): Collection
    {
        return $user->favorites()
            ->with('media')
            ->get();
    }

    /*
    |--------------------------------
    | ADD FAVORITE
    |--------------------------------
    */
    public function add(User $user, Media $media): void
    {
        $alreadyFavorite = $user->favorites()
            ->where('media_id', $media->id)
            ->exists();

        if ($alreadyFavorite) {
            abort(422, 'Ce média est déjà en favori.');
        }

        $user->favorites()->create([
            'media_id' => $media->id,
        ]);
    }

    /*
    |--------------------------------
    | REMOVE FAVORITE
    |--------------------------------
    */
    public function remove(User $user, Media $media): void
    {
        $user->favorites()
            ->where('media_id', $media->id)
            ->delete();
    }
}
