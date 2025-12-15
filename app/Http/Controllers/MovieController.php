<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Genre;
use \Illuminate\Support\Str;

class MovieController extends Controller
{
    // Function to redirectd to movies index page
    public function index()
    {
        $genres = Genre::with('movies')->get(); // Load genres with their movies
        return view('movies.index', compact('genres'));
    }

    // Function to show movie details
    public function details($id)
    {
        $movie = Movie::findorFail($id); // Fetch movie by ID or fail
        return view('movies.details', compact('movie'));
    }

    // Function to show search page with filtering
    public function search(Request $request)
    {
        // Start building the query
        $query = Movie::with('genres');

        // Apply filters
        $this->applySearchFilters($query, $request);
        $this->applySorting($query, $request);

        // Get results and genres
        $movies = $query->paginate(12);
        $genres = Genre::all();

        return view('movies.search', compact('movies', 'genres'));
    }

    // Apply all search filters to the query
    private function applySearchFilters($query, $request)
    {
        // Search by title
        if ($request->filled('search')) 
        {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by genre
        if ($request->filled('genre')) 
        {
            $query->whereHas('genres', function($q) use ($request) 
            {
                $q->where('genres.id', $request->genre);
            });
        }

        // Filter by year or decade
        if ($request->filled('year')) 
        {
            $this->applyYearFilter($query, $request->year);
        }

        // Filter by language
        if ($request->filled('language')) 
        {
            $query->where('language', $request->language);
        }

        // Filter by runtime
        if ($request->filled('runtime')) 
        {
            $this->applyRuntimeFilter($query, $request->runtime);
        }
    }

    // Apply year/decade filter
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

    // Apply runtime filter
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

    // Apply sorting to the query
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

    // Function to show the create movie form -> Admin only
    public function create()
    {
        $genres = Genre::all(); // Fetch all genres for the dropdown
        return view('admin.add', compact('genres')); // Form to add a new movie
    }

    public function store(Request $request){
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

        return redirect()->route('movies.index')->with('success', 'Movie added successfully!'); // Redirect to movies index with success message
    }
}
