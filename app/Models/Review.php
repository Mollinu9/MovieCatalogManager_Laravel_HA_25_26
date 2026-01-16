<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'comment',
    ];

    /**
     * Many-to-One: A review belongs to one user
     * Inverse: User can have many reviews
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Many-to-One: A review belongs to one movie
     * Inverse: Movie can have many reviews
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
