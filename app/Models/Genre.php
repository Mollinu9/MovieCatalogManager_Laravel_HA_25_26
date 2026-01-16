<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Many-to-Many: A genre can have many movies
     * Inverse: Movie can belong to many genres
     */
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'genre_movie');
    }
}
