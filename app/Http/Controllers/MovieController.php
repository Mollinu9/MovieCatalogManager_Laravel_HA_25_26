<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    public function index()
    {
        return view('movies.index'); // Main movies catalog page
    }

    public function create()
    {
        return view('admin.add'); // Form to add a new movie
    }
}
