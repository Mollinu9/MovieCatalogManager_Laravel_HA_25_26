<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Handles HTTP requests (form data, validation)
use Illuminate\Support\Facades\Http; // Makes HTTP requests to external APIs (TMDB)
use App\Models\Movie; // Movie database model for checking duplicates
use App\Models\Genre; // Genre database model for mapping TMDB genres to local genres

class TmdbController extends Controller
{
    /**
     * Search TMDB by movie title
     */
    public function search(Request $request)
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
    public function fetch(Request $request)
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

        $movieData = $this->fetchMovieData($tmdbId);

        return response()->json([
            'success' => true,
            'movie' => $movieData
        ]);
    }

    /**
     * Fetch movie data from TMDB (used by both API and Admin refresh)
     */
    public function fetchMovieData($tmdbId)
    {
        $url = "https://api.themoviedb.org/3/movie/{$tmdbId}?api_key={$this->getTmdbApiKey()}&language=en-US&append_to_response=videos";
        $movieData = $this->makeTmdbRequest($url);

        return $this->formatTmdbMovieData($movieData);
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
     * Check if movie exists in database by TMDB ID
     */
    private function movieExistsInDatabase($tmdbId)
    {
        return Movie::where('tmdb_id', $tmdbId)->exists();
    }
}
