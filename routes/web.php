<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\MovieController; // Handles public movie browsing
use App\Http\Controllers\AuthController; // Handles user authentication (login, register, logout)
use App\Http\Controllers\AdminController; // Handles admin movie management (CRUD operations)
use App\Http\Controllers\TmdbController; // Handles TMDB API integration (search, fetch movie data)
use App\Http\Controllers\ReviewController; 
use App\Http\Controllers\MovieRequestController; 

// Models
use App\Models\Movie; // Movie model for fetching random movies on home page

// ========================================
// HOME PAGE
// ========================================

Route::get('/', function () 
{
    $movies = Movie::inRandomOrder()->take(3)->get();
    return view('home', compact('movies'));
});

// ========================================
// AUTHENTICATION ROUTES
// ========================================

Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login'); // views/auth/login.blade.php
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register'); // views/auth/register.blade.php

Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit'); // Handle login form submission
Route::post('/register', [AuthController::class, 'register'])->name('auth.register.submit'); // Handle registration form submission
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout'); // Handle logout

Route::get('/movies/request', [MovieRequestController::class, 'request'])->name('movies.request');

// ========================================
// PUBLIC MOVIE ROUTES
// ========================================

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index'); // views/movies/index.blade.php
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search'); // views/movies/search.blade.php

// Watchlist management routes (requires authentication)
Route::middleware('auth')->group(function () 
{
    Route::get('/movies/watchlist', [MovieController::class, 'watchlist'])->name('movies.watchlist'); // views/movies/watchlist.blade.php
    Route::post('/movies/{id}/watchlist/toggle', [MovieController::class, 'toggleWatchlist'])->name('movies.watchlist.toggle');
    Route::delete('/movies/{id}/watchlist', [MovieController::class, 'removeFromWatchlist'])->name('movies.watchlist.remove');
    Route::patch('/movies/{id}/watchlist/status', [MovieController::class, 'updateWatchlistStatus'])->name('movies.watchlist.updateStatus');
});

Route::get('/movies/{id}', [MovieController::class, 'details'])->name('movies.details'); // views/movies/details.blade.php



// ========================================
// ADMIN ROUTES (Requires authentication + admin role)
// ========================================

Route::middleware(['auth', 'admin'])->group(function () 
{
    Route::get('/admin/movies', [AdminController::class, 'index'])->name('admin.movies.index'); // views/admin/index.blade.php
    Route::get('/admin/movies/create', [AdminController::class, 'create'])->name('admin.movies.create'); // views/admin/add.blade.php
    Route::post('/admin/movies', [AdminController::class, 'store'])->name('admin.movies.store'); // Handle form submission for creating a new movie
    Route::get('/admin/movies/{id}/edit', [AdminController::class, 'edit'])->name('admin.movies.edit'); // views/admin/edit.blade.php
    Route::put('/admin/movies/{id}', [AdminController::class, 'update'])->name('admin.movies.update'); // Handle form submission for updating a movie
    Route::delete('/admin/movies/{id}', [AdminController::class, 'destroy'])->name('admin.movies.destroy'); // Delete a movie
    Route::post('/admin/movies/{id}/refresh', [AdminController::class, 'refreshFromTmdb'])->name('admin.movies.refresh'); // Refresh movie data from TMDB
    
    // TMDB API Routes
    Route::post('/admin/tmdb/search', [TmdbController::class, 'search'])->name('admin.tmdb.search'); // Handles searching movie by id/name from TMDB
    Route::post('/admin/tmdb/fetch', [TmdbController::class, 'fetch'])->name('admin.tmdb.fetch'); // Handles getting movie details from TMDB
});
