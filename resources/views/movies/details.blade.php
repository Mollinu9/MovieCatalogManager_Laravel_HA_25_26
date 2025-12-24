@extends('layouts.app')

@section('content')
<div class="row">
  <!-- Movie Poster -->
  <div class="col-lg-3 col-md-4 mb-4">
    <div class="movie-details-poster">
      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" class="img-fluid" alt="{{ $movie->title }} Poster">
    </div>
  </div>

          <!-- Movie Details -->
          <div class="col-lg-9 col-md-8">
            <div class="card movie-details-card">
              <div class="card-body">
                <h2 class="card-title">{{ $movie->title }}</h2>
                
                <div class="genre-badge-container">
                  @foreach($movie->genres as $genre)
                    <span class="genre-badge">{{ $genre->name }}</span>
                  @endforeach
                </div>

                <!-- Watchlist Button -->
                @auth
                <div class="mb-3">
                  <button class="btn btn-primary btn-lg" onclick="if(confirm('Add {{ $movie->title }} to your watchlist?')) { alert('Movie added to your watchlist!'); }">
                    <i class="fa fa-heart"></i> Add to Watchlist
                  </button>
                  <a href="{{ route('movies.watchlist') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fa fa-list"></i> View Watchlist
                  </a>
                </div>
                @endauth

                <div class="movie-meta-info">
                  <p>
                    <i class="fa fa-calendar"></i>
                    <strong>Release Date:</strong> 
                    {{ $movie->release_date ? $movie->release_date->format('F j, Y') : 'N/A' }}
                  </p>
                  
                  <p>
                    <i class="fa fa-clock"></i>
                    <strong>Runtime:</strong> 
                    {{ $movie->runtime ?? 'N/A' }} minutes
                  </p>
                  
                  <p>
                    <i class="fa fa-language"></i>
                    <strong>Language:</strong> 
                    {{ strtoupper($movie->language ?? 'N/A') }}
                  </p>

                  <hr>

                  <div class="synopsis-content">
                    <p>
                      <i class="fa fa-file-text"></i>
                      <strong>Synopsis:</strong>
                    </p>
                    <p style="margin-left: 2rem; text-align: justify; line-height: 1.8;">{{ $movie->description }}</p>
                  </div>
                </div>
                
                @if($movie->trailer_link)
                <hr>
                <div class="trailer-section">
                  <h5>Trailer</h5>
                  <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="{{ $movie->embedUrl ?? ''}}" allowfullscreen></iframe>
                  </div>
                </div>
                @endif
              </div>
            </div>

            <!--
            Reviews Section 
            <div class="card mt-4">
              <div class="card-header">
                <h5 class="mb-0">User Reviews</h5>
              </div>
              <div class="card-body">
                Add Review Form (Only for logged in users who watched)
                <div class="alert alert-info">
                  <i class="fa fa-info-circle"></i> Mark this movie as watched to submit a review.
                </div>

                 Review Form (shown when user has watched) 
                <form class="mb-4 review-form-hidden">
                  <div class="form-group">
                    <label>Your Rating</label>
                    <select class="form-control" name="rating">
                      <option value="1">1 - Poor</option>
                      <option value="2">2 - Fair</option>
                      <option value="3">3 - Good</option>
                      <option value="4">4 - Very Good</option>
                      <option value="5" selected>5 - Excellent</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Your Review</label>
                    <textarea class="form-control" name="comment" rows="3" placeholder="Share your thoughts..."></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>

                <hr>

                 Existing Reviews
                <div class="media mb-3">
                  <div class="media-body">
                    <h6 class="mt-0">John Doe <small class="text-muted">• 2 days ago</small></h6>
                    <p class="mb-1"><i class="fa fa-star text-warning"></i> 5/5</p>
                    <p class="text-muted">
                      Absolutely phenomenal! Heath Ledger's performance as the Joker is unforgettable. 
                      This movie redefined superhero films.
                    </p>
                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Delete</button>
                  </div>
                </div>

                <hr>

                <div class="media mb-3">
                  <div class="media-body">
                    <h6 class="mt-0">Jane Smith <small class="text-muted">• 1 week ago</small></h6>
                    <p class="mb-1"><i class="fa fa-star text-warning"></i> 4.5/5</p>
                    <p class="text-muted">
                      A masterpiece of modern cinema. The action sequences are incredible and the story is gripping.
                    </p>
                  </div>
                </div>

              </div>
            </div>
            -->

          </div>
        </div>
      </div>
    </main>
@endsection