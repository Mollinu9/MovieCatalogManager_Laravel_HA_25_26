<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MovieController; // MovieController for handling movie-related routes

use App\Models\Movie; // Movie model to fetch 3 rnadom movies for the home page
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home Page refers to home.blade.php in views folder
Route::get('/', function () 
{
    $movies = Movie::inRandomOrder()->take(3)->get(); //Get 3 random movies to display in views/home.blade.php
    return view('home', compact('movies'));
});


// Movie Routes (public)

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index'); // views/movies/index.blade.php
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search'); // views/movies/search.blade.php
Route::get('/movies/{id}', [MovieController::class, 'details'])->name('movies.details'); // views/movies/details.blade.php

// Admin Routes (only admins)
Route::get('/admin/movies', [MovieController::class, 'adminIndex'])->name('admin.movies.index'); // views/admin/index.blade.php
Route::get('/admin/movies/create', [MovieController::class, 'create'])->name('admin.movies.create'); // views/admin/add.blade.php
Route::post('/admin/movies', [MovieController::class, 'store'])->name('admin.movies.store'); // Handle form submission for creating a new movie

// TMDB API Routes
Route::post('/admin/tmdb/search', [MovieController::class, 'searchTmdb'])->name('admin.tmdb.search'); // Handles searching movie by the id/name
Route::post('/admin/tmdb/fetch', [MovieController::class, 'fetchTmdb'])->name('admin.tmdb.fetch'); // Handles getting movie details
