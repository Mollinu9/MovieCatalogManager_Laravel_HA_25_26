<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\{
    MovieRequest,
    Movie,
    Genre,
    User
};

class AdminController extends Controller
{
    // ========================================
    // MOVIE MANAGEMENT - CRUD OPERATIONS
    // ========================================

    /**
     * Display admin dashboard with paginated list of all movies
     */
    public function index()
    {
        $movies = Movie::with('genres')->orderBy('created_at', 'asc')->paginate(20);
        return view('admin.index', compact('movies'));
    }

    /**
     * Show the create movie form with TMDB import or manual entry options
     */
    public function create()
    {
        $genres = Genre::all();
        return view('admin.add', compact('genres'));
    }

    /**
     * Store a new movie in the database (TMDB import or manual entry)
     */
    public function store(Request $request)
    {
        // Validate incoming movie data
        $validated = $this->validateMovieRequest($request);

        // Check for duplicates in database and user requests
        $duplicateCheck = $this->checkForDuplicates($validated);
        if ($duplicateCheck) {
            return $duplicateCheck;
        }

        // Generate URL-friendly slug from title
        $validated['slug'] = Str::slug($validated['title']);

        // Handle TMDB ID based on input method
        if ($validated['input_method'] === 'manual')
        {
            // Generate unique negative TMDB ID for manually added movies
            $validated['tmdb_id'] = $this->generateManualTmdbId();
        }
        elseif ($validated['input_method'] === 'tmdb' && empty($validated['tmdb_id']))
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['tmdb_id' => 'TMDB ID is required when using TMDB import method.']);
        }

        // Remove input_method (not a database column)
        unset($validated['input_method']);

        // Create movie and attach genres
        $movie = Movie::create($validated);

        if ($request->has('genres'))
        {
            $movie->genres()->attach($request->genres);
        }

        return redirect()->route('admin.movies.index')->with('success', 'Movie added successfully!');
    }

    /**
     * Show the edit movie form with pre-filled data
     */
    public function edit($id)
    {
        $movie = Movie::with('genres')->findOrFail($id);
        $genres = Genre::all();
        return view('admin.edit', compact('movie', 'genres'));
    }

    /**
     * Update an existing movie in the database
     */
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        // Validate incoming data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:10',
            'poster_url' => 'nullable|url',
            'trailer_link' => 'nullable|url',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        // Check for duplicate title (excluding current movie)
        $existingTitle = Movie::where('title', $validated['title'])
                              ->where('id', '!=', $id)
                              ->first();
        if ($existingTitle)
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'A movie with this title already exists in the database.']);
        }

        // Update slug and movie data
        $validated['slug'] = Str::slug($validated['title']);
        $movie->update($validated);

        // Sync genres (replaces all existing relationships)
        if ($request->has('genres'))
        {
            $movie->genres()->sync($request->genres);
        }
        else
        {
            $movie->genres()->detach();
        }

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully!');
    }

    /**
     * Delete a movie and all its relationships from the database
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movieTitle = $movie->title;

        // Detach genres and delete movie (cascade deletes watchlist entries and reviews)
        $movie->genres()->detach();
        $movie->delete();

        return redirect()->route('admin.movies.index')
            ->with('success', "Movie '{$movieTitle}' has been deleted successfully!");
    }

    /**
     * Refresh movie data from TMDB API (only for TMDB-imported movies)
     */
    public function refreshFromTmdb($id)
    {
        $movie = Movie::findOrFail($id);

        // Only refresh if movie has a valid TMDB ID (positive number)
        if (!$movie->tmdb_id || $movie->tmdb_id <= 0)
        {
            return redirect()->back()
                ->with('error', 'This movie was added manually and cannot be refreshed from TMDB.');
        }

        try
        {
            // Fetch fresh data from TMDB API
            $tmdbController = new TmdbController();
            $movieData = $tmdbController->fetchMovieData($movie->tmdb_id);

            // Update movie with fresh TMDB data
            $movie->update([
                'title' => $movieData['title'],
                'description' => $movieData['description'],
                'release_date' => $movieData['release_date'],
                'runtime' => $movieData['runtime'],
                'language' => $movieData['language'],
                'poster_url' => $movieData['poster_url'],
                'trailer_link' => $movieData['trailer_link'],
                'slug' => Str::slug($movieData['title']),
            ]);

            // Sync genres with fresh data
            if (!empty($movieData['genres']))
            {
                $movie->genres()->sync($movieData['genres']);
            }

            return redirect()
                ->back()->with('success', 'Movie data refreshed successfully from TMDB!');

        }
        catch (\Exception $e)
        {
            return redirect()
                ->back()->with('error', 'Failed to refresh movie data from TMDB: ' . $e->getMessage());
        }
    }

    // ========================================
    // VALIDATION & DUPLICATE CHECKING
    // ========================================

    /**
     * Validate movie request data for both TMDB import and manual entry
     */
    private function validateMovieRequest($request)
    {
        return $request->validate([
            'input_method' => 'required|in:tmdb,manual',
            'tmdb_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
            'runtime' => 'nullable|integer|min:1',
            'language' => 'nullable|string|max:10',
            'poster_url' => 'nullable|url',
            'trailer_link' => 'nullable|url',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);
    }

    /**
     * Check for duplicate movies in database and pending user requests (by TMDB ID or title)
     */
    private function checkForDuplicates($validated)
    {
        // Check TMDB ID if provided (TMDB import method)
        if ($validated['input_method'] === 'tmdb' && !empty($validated['tmdb_id']))
        {
            // Check if movie already exists in database by TMDB ID
            $existingMovie = Movie::where('tmdb_id', $validated['tmdb_id'])->first();
            if ($existingMovie)
            {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tmdb_id' => 'A movie with this TMDB ID already exists: ' . $existingMovie->title]);
            }

            // Check if movie has been requested by a user (by TMDB ID)
            $existingRequest = MovieRequest::where('tmdb_id', $validated['tmdb_id'])
                ->where('status', 'pending')
                ->with('user')
                ->first();
            
            if ($existingRequest)
            {
                return redirect()->back()
                    ->withInput()
                    ->with('warning', 'Note: This movie has already been requested by ' . $existingRequest->user->name . ' on ' . $existingRequest->created_at->format('M d, Y') . '. Adding it here will NOT automatically remove the request. Please approve the request from the Requests page instead, or manually delete the request after adding the movie here.');
            }
        }

        // Check for duplicate title in database
        $existingTitle = Movie::where('title', $validated['title'])->first();
        if ($existingTitle)
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'A movie with this title already exists in the database.']);
        }

        // Check if movie title has been requested by a user
        $existingTitleRequest = MovieRequest::where('movie_title', $validated['title'])
            ->where('status', 'pending')
            ->with('user')
            ->first();
        
        if ($existingTitleRequest)
        {
            return redirect()->back()
                ->withInput()
                ->with('warning', 'Note: A movie with this title has already been requested by ' . $existingTitleRequest->user->name . ' on ' . $existingTitleRequest->created_at->format('M d, Y') . '. Adding it here will NOT automatically remove the request. Please approve the request from the Requests page instead, or manually delete the request after adding the movie here.');
        }

        return null;
    }

    /**
     * Generate unique negative TMDB ID for manually added movies (-1, -2, -3, etc.)
     */
    private function generateManualTmdbId()
    {
        $lowestId = Movie::min('tmdb_id');
        return ($lowestId && $lowestId < 0) ? $lowestId - 1 : -1;
    }

    /**
     * Check if movie exists in database by TMDB ID
     */
    private function movieExistsInDatabase($tmdbId)
    {
        return Movie::where('tmdb_id', $tmdbId)->exists();
    }

    // ========================================
    // USER MANAGEMENT
    // ========================================

    /**
     * Display paginated list of all registered users
     */
    public function usersIndex()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Toggle user admin status (prevents admin from changing their own status)
     */
    public function toggleUserAdmin($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from removing their own admin status
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot change your own admin status.');
        }

        // Toggle admin status
        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'granted' : 'revoked';
        return redirect()->back()->with('success', "Admin privileges {$status} for {$user->name}.");
    }

    /**
     * Delete user and all associated data (watchlist, requests, reviews)
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting their own account
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;

        // Delete user's watchlist entries and movie requests
        $user->watchlistMovies()->detach();
        $user->movieRequests()->delete();

        // Delete the user (reviews cascade deleted by database)
        $user->delete();

        return redirect()->back()->with('success', "User '{$userName}' has been deleted successfully.");
    }
}
