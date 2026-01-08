<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Illuminate\Database\Eloquent\Casts\Attribute; // Needed for embedUrl attribute (to get youtube_url)

class Movie extends Model
{
    use HasFactory;

    // Mass assignable attributes
    protected $fillable = [
        'tmdb_id',
        'title',
        'slug',
        'description',
        'release_date',
        'runtime',
        'language',
        'poster_url',
        'trailer_link',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    // Many-to-many relationship with Genre
    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movie');
    }

    // Many-to-many relationship with User (watchlist)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_movie', 'movie_id', 'user_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Get all reviews for the current movie.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get average rating for this movie
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating');
    }

    /**
     * Get total number of reviews for this movie
     */
    public function reviewCount()
    {
        return $this->reviews()->count();
    }

    // Function to get the YouTube embed URL
    protected function embedUrl(): Attribute 
    {
        return Attribute::make(
            get: function(mixed $value, array $attributes)
            {
                $youtube_url = $attributes['trailer_link'] ?? ''; // Get the original YouTube URL from db

                if (preg_match("/[?&]v=([^&]+)/", $youtube_url, $matches)) // Check that it's a valid YouTube link
                {
                    // Extract the video ID from the URL
                    $video_id = $matches[1];
                    // Return the correctly formatted embed source link
                    return "https://www.youtube.com/embed/{$video_id}";
                }

                return null;
            }
        );
    }
}