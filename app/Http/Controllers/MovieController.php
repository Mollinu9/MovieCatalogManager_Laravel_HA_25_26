<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Handles HTTP requests (form data, query params, filters)
use App\Models\Movie; // Movie database model for querying movies
use App\Models\Genre; // Genre database model for filtering by genre

class MovieController extends Controller
{
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
        
        // Check if movie is in user's watchlist (if authenticated)
        $inWatchlist = false;
        if (auth()->check()) 
        {
            $inWatchlist = auth()->user()->watchlistMovies()->where('movie_id', $id)->exists();
        }
        
        return view('movies.details', compact('movie', 'inWatchlist'));
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
     * Display watchlist page with user's movies only
     */
    public function watchlist()
    {
        // Get only the authenticated user's watchlist movies with genres
        $movies = auth()->user()->watchlistMovies()->with('genres')->orderBy('title', 'asc')->get();
        return view('movies.watchlist', compact('movies'));
    }

    /**
     * Toggle movie in user's watchlist (add or remove)
     */
    public function toggleWatchlist($id)
    {
        $user = auth()->user();
        $movie = Movie::findOrFail($id);

        // Check if movie is already in watchlist
        if ($user->watchlistMovies()->where('movie_id', $id)->exists()) 
        {
            // Remove from watchlist
            $user->watchlistMovies()->detach($id);
            return back()->with('success', 'Movie removed from your watchlist!');
        } 
        else 
        {
            // Add to watchlist with default status
            $user->watchlistMovies()->attach($id, ['status' => 'to_watch']);
            return back()->with('success', 'Movie added to your watchlist!');
        }
    }

    /**
     * Remove movie from user's watchlist
     */
    public function removeFromWatchlist($id)
    {
        $user = auth()->user();
        $user->watchlistMovies()->detach($id);
        return back()->with('success', 'Movie removed from your watchlist!');
    }

    /**
     * Update the status of a movie in user's watchlist
     */
    public function updateWatchlistStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:watched,watching,to_watch,not_watched'
        ]);

        $user = auth()->user();
        
        // Update the pivot table status
        $user->watchlistMovies()->updateExistingPivot($id, [
            'status' => $request->status
        ]);

        return back()->with('success', 'Status updated successfully!');
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
}
