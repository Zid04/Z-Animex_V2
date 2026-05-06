<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /*
    |--------------------------------
    | RELATIONS
    |--------------------------------
    */

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }

    /*
    |--------------------------------
    | SCOPES
    |--------------------------------
    */

    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', "%{$value}%");
    }
}