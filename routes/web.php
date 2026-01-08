<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\{
    MovieController,        // Public movie browsing, watchlist management
    AuthController,         // User authentication (login, register, logout)
    AdminController,        // Admin movie management (CRUD operations)
    TmdbController,         // TMDB API integration (search, fetch movie data)
    ReviewController,       // Movie reviews and ratings
    MovieRequestController  // User movie requests for admin approval
};

// ========================================
// AUTHENTICATION ROUTES
// ========================================

Route::controller(AuthController::class)->group(function () 
{
    // Display authentication forms
    Route::get('/login', 'showLogin')->name('auth.login');
    Route::get('/register', 'showRegister')->name('auth.register');
    Route::get('/forgot-password', 'showForgotPassword')->name('auth.forgot');
    
    // Process authentication actions
    Route::post('/login', 'login')->name('auth.login.submit');
    Route::post('/register', 'register')->name('auth.register.submit');
    Route::post('/logout', 'logout')->name('auth.logout');
    Route::post('/reset-password', 'resetPassword')->name('auth.reset');
});

// ========================================
// PUBLIC MOVIE ROUTES
// ========================================

Route::controller(MovieController::class)->group(function () 
{
    // Home Page
    Route::get('/', 'home')->name('home');
    
    // Browse and search movies (accessible to all users)
    Route::get('/movies', 'index')->name('movies.index');
    Route::get('/movies/search', 'search')->name('movies.search');
    
    // Movie requests (users can request movies to be added by admin)
    Route::middleware('auth')->group(function () 
    {
        Route::get('/movies/request', [MovieRequestController::class, 'request'])->name('movies.request');
        Route::post('/movies/request', [MovieRequestController::class, 'store'])->name('movies.request.store');
    });
    
    // Authenticated user watchlist features
    Route::middleware('auth')->group(function () 
    {
        Route::get('/movies/watchlist', 'watchlist')->name('movies.watchlist');
        Route::post('/movies/{id}/watchlist/toggle', 'toggleWatchlist')->name('movies.watchlist.toggle');
        Route::delete('/movies/{id}/watchlist', 'removeFromWatchlist')->name('movies.watchlist.remove');
        Route::patch('/movies/{id}/watchlist/status', 'updateWatchlistStatus')->name('movies.watchlist.updateStatus');
    });
    
    Route::get('/movies/{id}', 'details')->name('movies.details');
});

// ========================================
// REVIEW ROUTES (Requires authentication)
// ========================================

Route::middleware('auth')->group(function () 
{
    Route::post('/movies/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

// ========================================
// ADMIN ROUTES (Requires authentication + admin role)
// ========================================

Route::middleware(['auth', 'admin'])->group(function () 
{
    // Admin movie management
    Route::controller(AdminController::class)->group(function () 
    {
        Route::get('/admin/movies', 'index')->name('admin.movies.index');
        Route::get('/admin/movies/create', 'create')->name('admin.movies.create');
        Route::post('/admin/movies', 'store')->name('admin.movies.store');
        Route::get('/admin/movies/{id}/edit', 'edit')->name('admin.movies.edit');
        Route::put('/admin/movies/{id}', 'update')->name('admin.movies.update');
        Route::delete('/admin/movies/{id}', 'destroy')->name('admin.movies.destroy');
        Route::post('/admin/movies/{id}/refresh', 'refreshFromTmdb')->name('admin.movies.refresh');
    });
    
    // TMDB API integration (search and fetch movie data)
    Route::controller(TmdbController::class)->group(function () 
    {
        Route::post('/admin/tmdb/search', 'search')->name('admin.tmdb.search');
        Route::post('/admin/tmdb/fetch', 'fetch')->name('admin.tmdb.fetch');
    });

    // Admin movie request management
    Route::controller(MovieRequestController::class)->group(function ()
    {
        Route::get('/admin/requests', 'index')->name('admin.requests.index');
        Route::post('/admin/requests/{id}/approve', 'approve')->name('admin.requests.approve');
        Route::post('/admin/requests/{id}/reject', 'reject')->name('admin.requests.reject');
        Route::delete('/admin/requests/{id}', 'destroy')->name('admin.requests.destroy');
    });

    // Admin user management
    Route::get('/admin/users', [AdminController::class, 'usersIndex'])->name('admin.users.index');
    Route::post('/admin/users/{id}/toggle-admin', [AdminController::class, 'toggleUserAdmin'])->name('admin.users.toggleAdmin');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
});
