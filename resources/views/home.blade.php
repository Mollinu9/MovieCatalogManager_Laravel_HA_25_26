@extends('layouts.app')

@section('title', 'Movie Catalog - Your Ultimate Movie Collection')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5">
  <div class="container">
    <div class="row align-items-center py-5">
      <div class="col-md-6">
        <h1 class="display-4 font-weight-bold mb-4">Discover & Track Your Favorite Movies</h1>
        <p class="lead mb-4">Build your personal movie collection, create watchlists, and never miss a great film again.</p>
        <div>
          <a href="#" class="btn btn-primary btn-lg mr-2">
            <i class="fa fa-user-plus"></i> Sign Up Free
          </a>
          <a href="#" class="btn btn-outline-light btn-lg">
            <i class="fa fa-film"></i> Browse Movies
          </a>
        </div>
      </div>
      <div class="col-md-6 text-center">
        <i class="fa fa-film hero-icon"></i>
      </div>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="py-5">
  <div class="container">
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
  </div>
</section>

<!-- Featured Movies Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-5">
      <h2 class="font-weight-bold mb-0">Featured Movies</h2>
      <a href="#" class="btn btn-outline-primary">View All <i class="fa fa-arrow-right"></i></a>
    </div>
    
    <div class="row justify-content-center">
      <!-- Movie Card 1 -->
      <div class="col-md-4 mb-4">
        <div class="card featured-movie-card shadow-lg">
          <div class="featured-movie-overlay">
            <div class="featured-movie-info">
              <h4 class="movie-title">The Dark Knight</h4>
              <p class="movie-meta"><i class="fa fa-calendar"></i> 2008</p>
              <p class="movie-meta"><i class="fa fa-film"></i> Action, Drama, Crime</p>
              <p class="movie-meta"><i class="fa fa-clock-o"></i> 152 minutes</p>
              <p class="movie-meta"><i class="fa fa-language"></i> English</p>
              <p class="movie-rating"><i class="fa fa-star"></i> 9.0/10</p>
              <a href="#" class="btn btn-primary btn-block mt-3">View Details</a>
            </div>
          </div>
          <img src="https://via.placeholder.com/400x600" class="card-img" alt="The Dark Knight Poster">
        </div>
      </div>

      <!-- Movie Card 2 -->
      <div class="col-md-4 mb-4">
        <div class="card featured-movie-card shadow-lg">
          <div class="featured-movie-overlay">
            <div class="featured-movie-info">
              <h4 class="movie-title">Inception</h4>
              <p class="movie-meta"><i class="fa fa-calendar"></i> 2010</p>
              <p class="movie-meta"><i class="fa fa-film"></i> Sci-Fi, Thriller</p>
              <p class="movie-meta"><i class="fa fa-clock-o"></i> 148 minutes</p>
              <p class="movie-meta"><i class="fa fa-language"></i> English</p>
              <p class="movie-rating"><i class="fa fa-star"></i> 8.8/10</p>
              <a href="#" class="btn btn-primary btn-block mt-3">View Details</a>
            </div>
          </div>
          <img src="https://via.placeholder.com/400x600" class="card-img" alt="Inception Poster">
        </div>
      </div>

      <!-- Movie Card 3 -->
      <div class="col-md-4 mb-4">
        <div class="card featured-movie-card shadow-lg">
          <div class="featured-movie-overlay">
            <div class="featured-movie-info">
              <h4 class="movie-title">The Shawshank Redemption</h4>
              <p class="movie-meta"><i class="fa fa-calendar"></i> 1994</p>
              <p class="movie-meta"><i class="fa fa-film"></i> Drama</p>
              <p class="movie-meta"><i class="fa fa-clock-o"></i> 142 minutes</p>
              <p class="movie-meta"><i class="fa fa-language"></i> English</p>
              <p class="movie-rating"><i class="fa fa-star"></i> 9.3/10</p>
              <a href="#" class="btn btn-primary btn-block mt-3">View Details</a>
            </div>
          </div>
          <img src="https://via.placeholder.com/400x600" class="card-img" alt="The Shawshank Redemption Poster">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="py-5">
  <div class="container">
    <div class="card bg-primary text-white text-center shadow-lg">
      <div class="card-body py-5">
        <h2 class="display-5 font-weight-bold mb-3">Ready to Start Your Movie Journey?</h2>
        <p class="lead mb-4">Join thousands of movie enthusiasts today</p>
        <a href="#" class="btn btn-primary btn-lg">
          <i class="fa fa-user-plus"></i> Create Free Account
        </a>
      </div>
    </div>
  </div>
</section>
@endsection
