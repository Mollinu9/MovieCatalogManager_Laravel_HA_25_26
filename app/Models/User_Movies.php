<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Movies extends Model
{
    use HasFactory;

    protected $table = 'user_movie';

    protected $fillable = [
        'user_id',
        'movie_id',
        'status'
    ];
}
