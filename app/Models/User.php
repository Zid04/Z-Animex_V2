<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;

#[Fillable(['name', 'email', 'password', 'pseudo', 'avatar'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------
    | RELATIONS
    |--------------------------------
    */

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class);
    }

    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function ratings()
    {
        return $this->hasMany(UserRating::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function episodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_user')
                    ->withPivot('watched_at', 'progress_seconds')
                    ->withTimestamps();
    }

    /*
    |--------------------------------
    | HELPERS
    |--------------------------------
    */

    public function watchedMedia()
    {
        return $this->userMedia()->where('status', 'completed');
    }

    public function watchingMedia()
    {
        return $this->userMedia()->where('status', 'watching');
    }

    public function plannedMedia()
    {
        return $this->userMedia()->where('status', 'planned');
    }
}
