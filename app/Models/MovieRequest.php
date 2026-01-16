<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_title',
        'tmdb_id',
        'status'
    ];

    /**
     * Many-to-One: A movie request belongs to one user
     * Inverse: User can have many movie requests
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
