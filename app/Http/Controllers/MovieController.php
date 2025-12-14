<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;
use \Illuminate\Support\Str;

class MovieController extends Controller
{
    public function index()
    {
        $genres = Genre::with('movies')->get();
        return view('movies.index', compact('genres'));
    }

    public function create()
    {
        $genres = Genre::all(); // Fetch all genres for the dropdown
        return view('admin.add', compact('genres')); // Form to add a new movie
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate(
            [
            'input_method' => 'required|in:tmdb,manual',
            'tmdb_id' => 'required_if:input_method,tmdb|nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:10',
            'poster_url' => 'nullable|url',
            'trailer_link' => 'nullable|url',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
            ]
        );

        // Check if TMDB ID already exists (for TMDB import)
        if ($validated['input_method'] === 'tmdb' && $validated['tmdb_id']) 
        {
            $existingMovie = Movie::where('tmdb_id', $validated['tmdb_id'])->first();
            if ($existingMovie) 
            {// If found, prevent duplicate
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tmdb_id' => 'A movie with this TMDB ID already exists: ' . $existingMovie->title]);
            }
        }

        // Check if movie title already exists
        $existingTitle = Movie::where('title', $validated['title'])->first();
        if ($existingTitle) 
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'A movie with this title already exists in the database.']);
        }

        // Generate slug from title
        $validated['slug'] = Str::slug($validated['title']);

        // If manual entry, set tmdb_id to a unique negative number
        // Example: Local movies get tmdb_id = -1, -2, -3, etc. (not on TMDB server)
        // TMDB movies keep their positive ID (e.g., 155 for "The Dark Knight")
        if ($validated['input_method'] === 'manual') {
            $lowestId = Movie::min('tmdb_id');
            $validated['tmdb_id'] = ($lowestId && $lowestId < 0) ? $lowestId - 1 : -1;  
        }

        // Remove input_method from validated data (not in database)
        unset($validated['input_method']);

        // Create the movie record in database
        // Example result in database:
        // | id (auto) | tmdb_id | title              | slug                |
        // |-----------|---------|-------------------|---------------------|
        // | 1         | 155     | The Dark Knight   | the-dark-knight     | ← TMDB movie
        // | 2         | -1      | Local Movie       | local-movie         | ← Manual entry
        $movie = Movie::create($validated);

        // Attach genres if provided
        if ($request->has('genres')) 
        {
            $movie->genres()->attach($request->genres);
        }

        return redirect()->route('movies.index')->with('success', 'Movie added successfully!');
    }
}
