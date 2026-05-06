<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_id',
        'number',
    ];

    protected $casts = [
        'number' => 'integer',
    ];

    /*
    |--------------------------------
    | RELATIONS
    |--------------------------------
    */

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    /*
    |--------------------------------
    | HELPERS (SIMPLIFIÉS)
    |--------------------------------
    */

    public function episodesCount(): int
    {
        return $this->episodes()->count();
    }
}