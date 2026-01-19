<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Http\Controllers\TmdbController;

use App\Models\MovieRequest;
use App\Models\Movie;
use App\Models\Genre;

class MovieRequestController extends Controller
{
    /**
     * Display the movie request page
     */
    public function request()
    {
        // Get the authenticated user's movie requests
        $requests = MovieRequest::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('movies.request', compact('requests'));
    }

    /**
     * Store a new movie request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_title' => 'required|string|max:255',
            'tmdb_id' => 'required|integer'
        ]);

        // Step 1: Check if movie already exists in movie_requests table (by TMDB ID or title)
        $existingRequest = MovieRequest::where('status', 'pending')
            ->where(function($query) use ($validated) {
                $query->where('tmdb_id', $validated['tmdb_id'])
                      ->orWhere('movie_title', $validated['movie_title']);
            })
            ->first();

        if ($existingRequest) {
            return response()->json([
                'error' => 'This movie has already been requested and is awaiting approval',
                'duplicate' => true
            ], 409);
        }

        // Step 2: Check if movie already exists in movies table (by TMDB ID or title)
        $existingMovie = Movie::where('tmdb_id', $validated['tmdb_id'])
            ->orWhere('title', $validated['movie_title'])
            ->first();

        if ($existingMovie) {
            return response()->json([
                'error' => 'This movie already exists in the database',
                'existing' => true
            ], 409);
        }

        // Step 3: Create the request if not found in either table
        MovieRequest::create([
            'user_id' => auth()->id(),
            'movie_title' => $validated['movie_title'],
            'tmdb_id' => $validated['tmdb_id'],
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Movie request submitted successfully!'
        ]);
    }

    /**
     * Display all movie requests for admin (only pending)
     */
    public function index()
    {
        // Only show pending requests to admin
        $requests = MovieRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = MovieRequest::where('status', 'pending')->count();

        return view('admin.requests', compact('requests', 'pendingCount'));
    }

    /**
     * Approve a movie request and add it to the database
     */
    public function approve($id)
    {
        $movieRequest = MovieRequest::findOrFail($id);

        // Check if movie already exists in database
        if (Movie::where('tmdb_id', $movieRequest->tmdb_id)->exists()) 
        {
            return back()->with('error', 'This movie already exists in the database');
        }

        // Fetch movie data from TMDB and save to database
        try 
        {
            $tmdbController = new TmdbController();
            $movieData = $tmdbController->fetchMovieData($movieRequest->tmdb_id);

            // Double-check movie doesn't exist (in case it was added while processing)
            if (Movie::where('tmdb_id', $movieData['tmdb_id'])->exists()) {
                $movieRequest->delete();
                return back()->with('error', 'This movie was already added to the database');
            }

            // Generate slug from title
            $slug = Str::slug($movieData['title']);
            
            // Ensure slug is unique
            $originalSlug = $slug;
            $counter = 1;
            while (Movie::where('slug', $slug)->exists()) 
            {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create the movie in database
            $movie = Movie::create([
                'tmdb_id' => $movieData['tmdb_id'],
                'title' => $movieData['title'],
                'slug' => $slug,
                'description' => $movieData['description'],
                'release_date' => $movieData['release_date'],
                'runtime' => $movieData['runtime'],
                'language' => $movieData['language'],
                'poster_url' => $movieData['poster_url'],
                'trailer_link' => $movieData['trailer_link']
            ]);

            // Attach genres
            if (!empty($movieData['genres']))
            {
                $movie->genres()->attach($movieData['genres']);
            }

            // Delete the request after successful approval
            $movieRequest->delete();

            return back()->with('success', 'Movie "' . $movie->title . '" has been added to the database!');
        } 
        catch (\Exception $e) 
        {
            return back()->with('error', 'Failed to fetch movie from TMDB: ' . $e->getMessage());
        }
    }

    /**
     * Reject a movie request (deletes it permanently)
     */
    public function reject($id)
    {
        $movieRequest = MovieRequest::findOrFail($id);
        $movieRequest->delete();

        return back()->with('success', 'Movie request rejected and deleted');
    }
}
