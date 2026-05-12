<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'external_id',
        'source',
        'type',
        'title',
        'description',
        'cover',
        'images',
        'status',
        'airing',
        'is_public',
        'approved',
        'episodes',
        'duration',
        'year',
        'score',
        'scored_by',
        'rank',
        'popularity',
        'members',
        'favorites',
        'studios',
        'genres',
    ];

    protected $casts = [
        'images'    => 'array',
        'studios'   => 'array',
        'genres'    => 'array',
        'airing'    => 'boolean',
        'approved'  => 'boolean',
        'is_public' => 'boolean',
        'score'     => 'float',
        'year'      => 'integer',
    ];

    /*
    |--------------------------------
    | RELATIONS
    |--------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

  public function episodes()
{
    return $this->hasManyThrough(
        Episode::class,
        Season::class,
        'media_id',  
        'season_id',  
        'id',         
        'id'          
    )->select('episodes.*'); 
}

    public function ratings()
    {
        return $this->hasMany(UserRating::class);
    }

    public function favorites()
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class);
    }

    /*
    |--------------------------------
    | HELPERS
    |--------------------------------
    */

    public function averageRating(): ?float
    {
        return $this->ratings()->avg('rating');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}