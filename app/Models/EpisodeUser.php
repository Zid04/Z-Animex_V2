<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EpisodeUser extends Model
{
    use HasFactory;

    protected $table = 'episode_user';

    protected $fillable = [
        'user_id',
        'episode_id',
        'watched_at',
        'progress_seconds',
    ];

    protected $casts = [
        'watched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
