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

// Home Page refers to home.blade.php
Route::get('/', function () 
{
    $movies = Movie::inRandomOrder()->take(3)->get();
    return view('home', compact('movies'));
});


// Movie Routes (public)
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');

// Admin Routes (only admins)
Route::get('/admin/movies/create', [MovieController::class, 'create'])->name('admin.movies.create');
Route::post('/admin/movies', [MovieController::class, 'store'])->name('admin.movies.store');