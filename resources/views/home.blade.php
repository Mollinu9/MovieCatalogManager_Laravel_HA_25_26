@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5">
  <div class="row align-items-center py-5">
    <div class="col-md-6">
      <h1 class="display-4 font-weight-bold mb-4" style="color: #ffffff !important;">Discover & Track Your Favorite Movies</h1>
      <p class="lead mb-4" style="color: #ffffff !important;">Build your personal movie collection, create watchlists, and never miss a great film again.</p>
      <div>
        <a href="{{ route('auth.register') }}" class="btn btn-primary btn-lg mr-2">
          <i class="fa fa-user-plus"></i> Sign Up Free
        </a>
        <a href="{{ route('movies.index') }}" class="btn btn-outline-light btn-lg">
          <i class="fa fa-film"></i> Browse Movies
        </a>
      </div>
    </div>
    <div class="col-md-6 text-center">
      <i class="fa fa-film hero-icon"></i>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5">
  <div class="text-center mb-5">
    <h2 class="display-5 font-weight-bold">Why Choose Movie Catalog?</h2>
    <p class="lead text-muted">Everything you need to manage your movie experience</p>
  </div>
  
  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card h-100 text-center border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="mb-3">
            <i class="fa fa-search fa-3x feature-icon"></i>
          </div>
          <h4 class="card-title">Discover Movies</h4>
          <p class="card-text text-muted">Browse through thousands of movies with advanced filters by genre, year, and rating.</p>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card h-100 text-center border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="mb-3">
            <i class="fa fa-heart fa-3x text-danger"></i>
          </div>
          <h4 class="card-title">Personal Watchlist</h4>
          <p class="card-text text-muted">Create and manage your personal watchlist. Track what you've watched and what's next.</p>
        </div>
      </div>
    </div>

    <div class="col-md-4 mb-4">
      <div class="card h-100 text-center border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="mb-3">
            <i class="fa fa-comments fa-3x feature-icon"></i>
          </div>
          <h4 class="card-title">Request Movies</h4>
          <p class="card-text text-muted">Can't find a movie? Request it and we'll add it to our growing collection.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Movies Section -->
<section class="py-5 bg-light">
  <div class="d-flex justify-content-between align-items-center mb-5">
    <h2 class="font-weight-bold mb-0">Movies</h2>
    <a href="{{ route('movies.index') }}" class="btn btn-outline-primary">View All <i class="fa fa-arrow-right"></i></a>
  </div>
  
  <div class="row justify-content-center">
    @forelse($movies as $movie)
      <div class="col-md-4 mb-4">
        @include('partials.movie-card', [
          'movie' => $movie,
          'layout' => 'grid'
        ])
      </div>
    @empty
      <div class="col-md-12">
        @include('partials.empty-state', [
          'icon' => 'fa-film',
          'title' => 'No movies available yet',
          'description' => 'Add some movies to get started!',
          'actionUrl' => route('admin.movies.create'),
          'actionText' => 'Add Your First Movie'
        ])
      </div>
    @endforelse
  </div>
</section>
@endsection
