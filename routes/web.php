<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MovieController;
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
Route::get('/', function () {
    return view('home');
});


// Movie Routes (public)
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');

// Admin Routes (only admins)
Route::get('/admin/movies/create', [MovieController::class, 'create'])->name('admin.movies.create');