<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    // ========================================
    // PUBLIC MOVIE BROWSING
    // ========================================

    /**
     * Display movies grouped by genre
     */
    public function index()
    {
        $genres = Genre::with('movies')->get();
        return view('movies.index', compact('genres'));
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

        if ($validated['input_method'] === 'manual') {
            $validated['tmdb_id'] = $this->generateManualTmdbId();
        }

        unset($validated['input_method']);

        $movie = Movie::create($validated);

        if ($request->has('genres')) {
            $movie->genres()->attach($request->genres);
        }

        return redirect()->route('movies.index')->with('success', 'Movie added successfully!');
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

        if (isset($data['results'])) {
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

        if ($this->movieExistsInDatabase($tmdbId)) {
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
        if ($validated['input_method'] === 'tmdb' && $validated['tmdb_id']) {
            $existingMovie = Movie::where('tmdb_id', $validated['tmdb_id'])->first();
            if ($existingMovie) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tmdb_id' => 'A movie with this TMDB ID already exists: ' . $existingMovie->title]);
            }
        }

        $existingTitle = Movie::where('title', $validated['title'])->first();
        if ($existingTitle) {
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
        if ($request->filled('search')) {
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
    private function applyYearFilter($query, $year){
        $decades = [
            '2010s' => ['2010-01-01', '2019-12-31'],
            '2000s' => ['2000-01-01', '2009-12-31'],
            '1990s' => ['1990-01-01', '1999-12-31'],
            '1980s' => ['1980-01-01', '1989-12-31'],
        ];

        if (isset($decades[$year])) {
            $query->whereBetween('release_date', $decades[$year]);
        } elseif ($year === 'older') {
            $query->where('release_date', '<', '1980-01-01');
        } else {
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
     * Make a CURL request to TMDB API
     */
    private function makeTmdbRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception('CURL Error: ' . $error);
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('TMDB API returned status code: ' . $httpCode);
        }

        return json_decode($response, true);
    }

    /**
     * Extract YouTube trailer from TMDB video data
     */
    private function extractTrailerLink($movieData)
    {
        if (!isset($movieData['videos']['results']) || !is_array($movieData['videos']['results'])) {
            return null;
        }

        foreach ($movieData['videos']['results'] as $video) {
            if (isset($video['type'], $video['site'], $video['key']) 
                && $video['type'] === 'Trailer' 
                && $video['site'] === 'YouTube') {
                return 'https://www.youtube.com/watch?v=' . $video['key'];
            }
        }
        
        foreach ($movieData['videos']['results'] as $video) {
            if (isset($video['site'], $video['key']) && $video['site'] === 'YouTube') {
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

        if (isset($movieData['genres']) && is_array($movieData['genres'])) {
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
}
