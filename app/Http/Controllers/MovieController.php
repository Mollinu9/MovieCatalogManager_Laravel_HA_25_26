<?php

namespace App\Http\Controllers;

// Laravel Core Classes
use Illuminate\Http\Request;// Handles HTTP requests (form data, query params)
use Illuminate\Support\Facades\Http;// Makes HTTP requests to external APIs (TMDB)
use Illuminate\Support\Str;// String helpers (e.g., Str::slug() for URLs)

// Application Models
use App\Models\Movie;// Movie database model
use App\Models\Genre;// Genre database model
use App\Models\User;// User database model

class MovieController extends Controller
{
    // ========================================
    // PUBLIC MOVIE BROWSING
    // ========================================

    /**
     * Display movies grouped by genre
     */
    public function index(Request $request)
    {
        $selectedGenreId = $request->query('genre');
        
        // Always get all genres for the filter bar with movie counts
        $allGenres = Genre::withCount('movies')->get();
        
        if ($selectedGenreId)
        {
            // Filter to show only the selected genre with movies
            $genres = Genre::with('movies')
                ->where('id', $selectedGenreId)
                ->get();
        }
        else
        {
            // Show all genres with movies
            $genres = Genre::with('movies')->get();
        }
        
        return view('movies.index', compact('genres', 'allGenres', 'selectedGenreId'));
    }

    /**
     * Display movie details
     */
    public function details($id)
    {
        $movie = Movie::with('genres')->findOrFail($id);
        return view('movies.details', compact('movie'));
    }

    /**
     * Search and filter movies
     */
    public function search(Request $request)
    {
        $query = Movie::with('genres');

        $this->applySearchFilters($query, $request);
        $this->applySorting($query, $request);

        $movies = $query->paginate(12);
        $genres = Genre::all();

        return view('movies.search', compact('movies', 'genres'));
    }

    /**
     * Display watchlist page with all movies
     */
    public function watchlist()
    {
        $movies = Movie::with('genres')->orderBy('title', 'asc')->get();
        return view('movies.watchlist', compact('movies'));
    }

    // ========================================
    // ADMIN MOVIE MANAGEMENT
    // ========================================

    /**
     * Display admin dashboard with all movies
     */
    public function adminIndex()
    {
        $movies = Movie::with('genres')->orderBy('created_at', 'desc')->paginate(15);
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

        $this->checkForDuplicates($validated);

        $validated['slug'] = Str::slug($validated['title']);

        if ($validated['input_method'] === 'manual')
        {
            $validated['tmdb_id'] = $this->generateManualTmdbId();
        }

        unset($validated['input_method']);

        $movie = Movie::create($validated);

        if ($request->has('genres'))
        {
            $movie->genres()->attach($request->genres);
        }

        return redirect()->route('movies.index')->with('success', 'Movie added successfully!');
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
            $url = "https://api.themoviedb.org/3/movie/{$movie->tmdb_id}?api_key={$this->getTmdbApiKey()}&language=en-US&append_to_response=videos";
            $movieData = $this->makeTmdbRequest($url);

            $formattedMovie = $this->formatTmdbMovieData($movieData);

            // Update movie with fresh TMDB data
            $movie->update([
                'title' => $formattedMovie['title'],
                'description' => $formattedMovie['description'],
                'release_date' => $formattedMovie['release_date'],
                'runtime' => $formattedMovie['runtime'],
                'language' => $formattedMovie['language'],
                'poster_url' => $formattedMovie['poster_url'],
                'trailer_link' => $formattedMovie['trailer_link'],
                'slug' => Str::slug($formattedMovie['title']),
            ]);

            // Sync genres
            if (!empty($formattedMovie['genres']))
            {
                $movie->genres()->sync($formattedMovie['genres']);
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

    // ========================================
    // TMDB API INTEGRATION
    // ========================================

    /**
     * Search TMDB by movie title
     */
    public function searchTmdb(Request $request)
    {
        $request->validate(['query' => 'required|string|min:1']);

        $query = urlencode($request->input('query'));
        $url = "https://api.themoviedb.org/3/search/movie?api_key={$this->getTmdbApiKey()}&query={$query}&language=en-US&page=1";

        $data = $this->makeTmdbRequest($url);

        if (isset($data['results']))
        {
            return response()->json([
                'success' => true,
                'results' => array_slice($data['results'], 0, 10)
            ]);
        }

        return response()->json(['error' => 'No results found'], 404);
    }

    /**
     * Fetch TMDB movie details by ID
     */
    public function fetchTmdb(Request $request)
    {
        $request->validate(['tmdb_id' => 'required|integer']);

        $tmdbId = $request->tmdb_id;

        if ($this->movieExistsInDatabase($tmdbId))
        {
            $existingMovie = Movie::where('tmdb_id', $tmdbId)->first();
            return response()->json([
                'error' => 'This movie already exists in your database',
                'existing' => true,
                'movie_title' => $existingMovie->title,
                'movie_id' => $existingMovie->id
            ], 409);
        }

        $url = "https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$this->getTmdbApiKey()}&language=en-US&append_to_response=videos";
        $movieData = $this->makeTmdbRequest($url);

        $formattedMovie = $this->formatTmdbMovieData($movieData);

        return response()->json([
            'success' => true,
            'movie' => $formattedMovie
        ]);
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
        ]);
    }

    /**
     * Check for duplicate movies
     */
    private function checkForDuplicates($validated)
    {
        if ($validated['input_method'] === 'tmdb' && $validated['tmdb_id'])
        {
            $existingMovie = Movie::where('tmdb_id', $validated['tmdb_id'])->first();
            if ($existingMovie)
            {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tmdb_id' => 'A movie with this TMDB ID already exists: ' . $existingMovie->title]);
            }
        }

        $existingTitle = Movie::where('title', $validated['title'])->first();
        if ($existingTitle)
        {
            return redirect()->back()
                ->withInput()
                ->withErrors(['title' => 'A movie with this title already exists in the database.']);
        }
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
    // SEARCH FILTERS
    // ========================================

    /**
     * Apply all search filters to the query
     */
    private function applySearchFilters($query, $request)
    {
        if ($request->filled('search'))
        {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('genre'))
        {
            $query->whereHas('genres', function($q) use ($request)
            {
                $q->where('genres.id', $request->genre);
            });
        }

        if ($request->filled('year'))
        {
            $this->applyYearFilter($query, $request->year);
        }

        if ($request->filled('language'))
        {
            $query->where('language', $request->language);
        }

        if ($request->filled('runtime'))
        {
            $this->applyRuntimeFilter($query, $request->runtime);
        }
    }

    /**
     * Apply year/decade filter
     */
    private function applyYearFilter($query, $year)
    {
        $decades = [
            '2010s' => ['2010-01-01', '2019-12-31'],
            '2000s' => ['2000-01-01', '2009-12-31'],
            '1990s' => ['1990-01-01', '1999-12-31'],
            '1980s' => ['1980-01-01', '1989-12-31'],
        ];

        if (isset($decades[$year]))
        {
            $query->whereBetween('release_date', $decades[$year]);
        }
        elseif ($year === 'older')
        {
            $query->where('release_date', '<', '1980-01-01');
        }
        else
        {
            $query->whereYear('release_date', $year);
        }
    }

    /**
     * Apply runtime filter
     */
    private function applyRuntimeFilter($query, $runtime)
    {
        $runtimeRanges = [
            'short'  => ['<', 90],
            'medium' => ['between', [90, 120]],
            'long'   => ['between', [120, 150]],
            'epic'   => ['>', 150],
        ];

        if (isset($runtimeRanges[$runtime]))
        {
            $range = $runtimeRanges[$runtime];
            if ($range[0] === 'between')
            {
                $query->whereBetween('runtime', $range[1]);
            }
            else
            {
                $query->where('runtime', $range[0], $range[1]);
            }
        }
    }

    /**
     * Apply sorting to the query
     */
    private function applySorting($query, $request)
    {
        $sortOptions = [
            'year-desc'  => ['release_date', 'desc'],
            'year-asc'   => ['release_date', 'asc'],
            'title-asc'  => ['title', 'asc'],
            'title-desc' => ['title', 'desc'],
        ];

        $sort = $request->get('sort', 'title-asc');
        
        if (isset($sortOptions[$sort]))
        {
            $query->orderBy($sortOptions[$sort][0], $sortOptions[$sort][1]);
        }
        else
        {
            $query->orderBy('title', 'asc');
        }
    }

    // ========================================
    // TMDB API HELPERS
    // ========================================

    /**
     * Get TMDB API key from environment
     */
    private function getTmdbApiKey()
    {
        $apiKey = env('TMDB_API_KEY');
        if (!$apiKey)
        {
            throw new \Exception('TMDB API key not configured');
        }
        return $apiKey;
    }

    /**
     * Make a request to TMDB API
     */
    private function makeTmdbRequest($url)
    {
        $response = Http::timeout(10)->get($url);

        if ($response->failed())
        {
            throw new \Exception('TMDB API returned status code: ' . $response->status());
        }

        return $response->json();
    }

    /**
     * Extract YouTube trailer from TMDB video data
     */
    private function extractTrailerLink($movieData)
    {
        if (!isset($movieData['videos']['results']) || !is_array($movieData['videos']['results']))
        {
            return null;
        }

        foreach ($movieData['videos']['results'] as $video)
        {
            if (isset($video['type'], $video['site'], $video['key']) 
                && $video['type'] === 'Trailer' 
                && $video['site'] === 'YouTube')
            {
                return 'https://www.youtube.com/watch?v=' . $video['key'];
            }
        }
        
        foreach ($movieData['videos']['results'] as $video)
        {
            if (isset($video['site'], $video['key']) && $video['site'] === 'YouTube')
            {
                return 'https://www.youtube.com/watch?v=' . $video['key'];
            }
        }

        return null;
    }

    /**
     * Map TMDB genres to local database genre IDs
     */
    private function mapGenresToLocal($movieData)
    {
        $genreIds = [];
        $genreNames = [];

        if (isset($movieData['genres']) && is_array($movieData['genres']))
        {
            $genreNames = array_column($movieData['genres'], 'name');
            $genreIds = Genre::whereIn('name', $genreNames)->pluck('id')->toArray();
        }

        return [
            'ids' => $genreIds,
            'names' => $genreNames
        ];
    }

    /**
     * Format TMDB movie data for frontend
     */
    private function formatTmdbMovieData($movieData)
    {
        $genres = $this->mapGenresToLocal($movieData);
        $trailerLink = $this->extractTrailerLink($movieData);

        return [
            'tmdb_id' => $movieData['id'] ?? null,
            'title' => $movieData['title'] ?? 'Unknown Title',
            'description' => $movieData['overview'] ?? '',
            'release_date' => $movieData['release_date'] ?? null,
            'runtime' => $movieData['runtime'] ?? null,
            'language' => $movieData['original_language'] ?? 'en',
            'poster_url' => isset($movieData['poster_path']) && $movieData['poster_path']
                ? 'https://image.tmdb.org/t/p/w500' . $movieData['poster_path'] 
                : null,
            'trailer_link' => $trailerLink,
            'genres' => $genres['ids'],
            'genre_names' => $genres['names']
        ];
    }


    /**
     * Auth redirections
     */

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password'])
        ]);

        auth()->login($user);

        return redirect()->route('movies.index')->with('success', 'Account created successfully! Welcome to Movie Catalog.');

    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request) 
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $remember = $request->has('remember');

        if (auth()->attempt($credentials, $remember))
        {
            $request->session()->regenerate();
            
            return redirect()->intended(route('movies.index'))
                ->with('success', 'Welcome back, ' . auth()->user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->onlyInput('email');
    }
}