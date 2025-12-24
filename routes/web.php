<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MovieController; // MovieController for handling movie-related routes

use App\Models\Movie; // Movie model to fetch 3 rnadom movies for the home page

// Home Page refers to home.blade.php in views folder
Route::get('/', function () 
{
    $movies = Movie::inRandomOrder()->take(3)->get(); //Get 3 random movies to display in views/home.blade.php
    return view('home', compact('movies'));
});

Route::get('/login', [MovieController::class, 'showLogin'])->name('auth.login'); // views/auth/login.blade.php
Route::get('/register', [MovieController::class, 'showRegister'])->name('auth.register'); // views/auth/register.blade.php

Route::post('/login', [MovieController::class, 'login'])->name('auth.login.submit'); // Handle login form submission
Route::post('/register', [MovieController::class, 'register'])->name('auth.register.submit'); // Handle registration form submission  
Route::post('/logout', [MovieController::class, 'logout'])->name('auth.logout'); // Handle logout  


// Movie Routes (public)

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index'); // views/movies/index.blade.php
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search'); // views/movies/search.blade.php
Route::get('/movies/watchlist', [MovieController::class, 'watchlist'])->name('movies.watchlist'); // views/movies/watchlist.blade.php
Route::get('/movies/{id}', [MovieController::class, 'details'])->name('movies.details'); // views/movies/details.blade.php


// Admin Routes (only admins)
Route::get('/admin/movies', [MovieController::class, 'adminIndex'])->name('admin.movies.index'); // views/admin/index.blade.php
Route::get('/admin/movies/create', [MovieController::class, 'create'])->name('admin.movies.create'); // views/admin/add.blade.php
Route::post('/admin/movies', [MovieController::class, 'store'])->name('admin.movies.store'); // Handle form submission for creating a new movie
Route::get('/admin/movies/{id}/edit', [MovieController::class, 'edit'])->name('admin.movies.edit'); // views/admin/edit.blade.php
Route::put('/admin/movies/{id}', [MovieController::class, 'update'])->name('admin.movies.update'); // Handle form submission for updating a movie
Route::delete('/admin/movies/{id}', [MovieController::class, 'destroy'])->name('admin.movies.destroy'); // Delete a movie
Route::post('/admin/movies/{id}/refresh', [MovieController::class, 'refreshFromTmdb'])->name('admin.movies.refresh'); // Refresh movie data from TMDB

// TMDB API Routes
Route::post('/admin/tmdb/search', [MovieController::class, 'searchTmdb'])->name('admin.tmdb.search'); // Handles searching movie by the id/name
Route::post('/admin/tmdb/fetch', [MovieController::class, 'fetchTmdb'])->name('admin.tmdb.fetch'); // Handles getting movie details