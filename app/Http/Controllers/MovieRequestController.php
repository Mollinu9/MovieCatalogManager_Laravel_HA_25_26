<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MovieRequest;
use App\Models\Movie;

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

        // Check if movie already exists in database
        if (Movie::where('tmdb_id', $validated['tmdb_id'])->exists()) {
            return response()->json([
                'error' => 'This movie already exists in the database',
                'existing' => true
            ], 409);
        }

        // Check if user already requested this movie
        $existingRequest = MovieRequest::where('user_id', auth()->id())
            ->where('tmdb_id', $validated['tmdb_id'])
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'error' => 'You have already requested this movie',
                'duplicate' => true
            ], 409);
        }

        // Create the request
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

    public function index()
    {
        return view('admin.requests');
    }
}
