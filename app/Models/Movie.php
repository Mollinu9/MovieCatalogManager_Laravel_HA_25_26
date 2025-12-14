<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

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
}