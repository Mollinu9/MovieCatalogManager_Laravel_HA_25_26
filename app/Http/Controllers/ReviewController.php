<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Review;

class ReviewController extends Controller
{
    // Store a new review
    public function store(Request $request, $slug)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000'
        ]);

        // Find movie by slug
        $movie = \App\Models\Movie::where('slug', $slug)->firstOrFail();

        // Check if user already reviewed this movie
        $existingReview = Review::where('user_id', Auth::id())
                                ->where('movie_id', $movie->id)
                                ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this movie.');
        }

        Review::create([
            'user_id' => Auth::id(),
            'movie_id' => $movie->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return redirect()->back()->with('success', 'Your review has been submitted successfully!');
    }
}
