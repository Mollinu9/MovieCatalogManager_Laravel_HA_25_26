<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Handles HTTP requests (form data, validation)
use Illuminate\Support\Str; // String helpers (e.g., Str::slug() for creating URL-friendly slugs)
use Illuminate\Support\Facades\Auth; // Authentication helper

use App\Models\Movie; // Movie database model for CRUD operations
use App\Models\Genre; // Genre database model for managing movie genres
use App\Models\User; // User database model for user management

class AdminController extends Controller
{
    /**
     * Display admin dashboard with all movies
     */
    public function index()
    {
        $movies = Movie::with('genres')->orderBy('created_at', 'asc')->paginate(20);
        return view('admin.index', compact('movies'));
    }

    /**
     * Show the create movie form
     */
    public function create()
    {
        $genres = Genre::all();
        return view('admin.add', compact('genres'));
    }

    /**
     * Store a new movie in the database
     */
    public function store(Request $request)
    {
        $validated = $this->validateMovieRequest($request);

        // Check for duplicates and return early if found
        $duplicateCheck = $this->checkForDuplicates($validated);
        if ($duplicateCheck) {
            return $duplicateCheck;
        }

        $validated['slug'] = Str::slug($validated['title']);

        // Handle TMDB ID based on input method
        if ($validated['input_method'] === 'manual')
        {
            $validated['tmdb_id'] = $this->generateManualTmdbId();
        }
        elseif ($validated['input_method'] === 'tmdb' && empty($validated['tmdb_id']))
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['tmdb_id' => 'TMDB ID is required when using TMDB import method.']);
        }

        unset($validated['input_method']);

        $movie = Movie::create($validated);

        if ($request->has('genres'))
        {
            $movie->genres()->attach($request->genres);
        }

        return redirect()->route('admin.movies.index')->with('success', 'Movie added successfully!');
    }

    /**
     * Show the edit movie form
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

        $validated['slug'] = Str::slug($validated['title']);

        $movie->update($validated);

        // Sync genres
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
     * Delete a movie from the database
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movieTitle = $movie->title;

        // Detach all genre relationships
        $movie->genres()->detach();

        // Delete the movie
        $movie->delete();

        return redirect()->route('admin.movies.index')
            ->with('success', "Movie '{$movieTitle}' has been deleted successfully!");
    }

    /**
     * Refresh movie data from TMDB
     */
    public function refreshFromTmdb($id)
    {
        $movie = Movie::findOrFail($id);

        // Only refresh if movie has a valid TMDB ID
        if (!$movie->tmdb_id || $movie->tmdb_id <= 0)
        {
            return redirect()->back()
                ->with('error', 'This movie was added manually and cannot be refreshed from TMDB.');
        }

        try
        {
            // Use TmdbController to fetch fresh data
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

            // Sync genres
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
     * Validate movie request data
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
     * Check for duplicate movies
     * Returns redirect response if duplicate found, null otherwise
     */
    private function checkForDuplicates($validated)
    {
        // Only check TMDB ID if it's provided and not empty
        if ($validated['input_method'] === 'tmdb' && !empty($validated['tmdb_id']))
        {
            $existingMovie = Movie::where('tmdb_id', $validated['tmdb_id'])->first();
            if ($existingMovie)
            {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tmdb_id' => 'A movie with this TMDB ID already exists: ' . $existingMovie->title]);
            }
        }

        // Always check for duplicate titles
        $existingTitle = Movie::where('title', $validated['title'])->first();
        if ($existingTitle)
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'A movie with this title already exists in the database.']);
        }

        return null;
    }

    /**
     * Generate unique negative TMDB ID for manual entries
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
     * Display a listing of users (Admin only)
     */
    public function usersIndex()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    /**
     * Toggle user admin status (Admin only)
     */
    public function toggleUserAdmin($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from removing their own admin status
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot change your own admin status.');
        }

        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'granted' : 'revoked';
        return redirect()->back()->with('success', "Admin privileges {$status} for {$user->name}.");
    }

    /**
     * Delete a user account (Admin only)
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting their own account
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;

        // Delete user's watchlist entries
        $user->watchlistMovies()->detach();

        // Delete user's movie requests
        $user->movieRequests()->delete();

        // Delete the user
        $user->delete();

        return redirect()->back()->with('success', "User '{$userName}' has been deleted successfully.");
    }
}
