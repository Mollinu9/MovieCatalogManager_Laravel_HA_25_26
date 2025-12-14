<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;

class MovieController extends Controller
{
    public function index()
    {
        return view('movies.index'); // Main movies catalog page
    }

    public function create()
    {
        $genres = Genre::all(); // Fetch all genres for the dropdown
        return view('admin.add', compact('genres')); // Form to add a new movie
    }
}
