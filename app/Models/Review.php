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
     * Get the user that wrote the review
     */
    public function user_author()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie that is being reviewed
     */
    public function movie_reviewed()
    {
        return $this->belongsTo(Movie::class);
    }
}
