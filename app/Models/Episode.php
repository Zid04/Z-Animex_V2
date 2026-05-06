<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'season_id',
        'number',
        'title',
        'duration',
        'video_url',
    ];

    protected $casts = [
        'number' => 'integer',
        'duration' => 'integer',
    ];

    /*
    |--------------------------------
    | RELATIONS
    |--------------------------------
    */

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function media()
{
    return $this->season->media();
}


    /*
    |--------------------------------
    | USER WATCH (via episode_user pivot)
    |--------------------------------
    */


public function users()
{
    return $this->belongsToMany(User::class, 'episode_user')
                ->withPivot('watched_at')
                ->withTimestamps();
}

public function isWatchedBy(int $userId): bool
{
    return $this->users()->where('user_id', $userId)->exists();
}

}