@extends('layouts.app')

@section('content')
<!-- Success Message -->
@if(session('success'))
  @include('partials.alert', [
    'type' => 'success',
    'message' => session('success')
  ])
@endif

<div class="row">
  <!-- Left: Movie Poster -->
  <div class="col-md-2">
    <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" 
         class="img-fluid rounded" 
         alt="{{ $movie->title }} Poster">
  </div>

  <!-- Middle: Movie Details + Trailer -->
  <div class="col-md-7">
    <!-- Movie Details Card -->
    <div class="card">
      <div class="card-body p-3">
        <!-- Title and Year -->
        <h4 class="mb-1">
          {{ $movie->title }}
          @if($movie->release_date)
            <span class="text-muted">({{ $movie->release_date->format('Y') }})</span>
          @endif
        </h4>

        <!-- Meta Info -->
        <p class="text-muted mb-2 small">
          @if($movie->release_date)
            {{ $movie->release_date->format('m/d/Y') }} (US)
          @endif
          • 
          @if($movie->genres->count() > 0)
            {{ $movie->genres->pluck('name')->join(', ') }}
          @endif
          @if($movie->runtime)
            • {{ $movie->runtime }}m
          @endif
        </p>

        <!-- Genres -->
        <div class="mb-2">
          @foreach($movie->genres as $genre)
            @include('partials.genre-badge', [
              'genre' => $genre,
              'size' => 'small'
            ])
          @endforeach
        </div>

        <!-- Action Buttons -->
        <div class="mb-2">
          @auth
            <form action="{{ route('movies.watchlist.toggle', $movie->id) }}" method="POST" class="d-inline">
              @csrf
              @if($inWatchlist)
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="fa fa-heart"></i> In Watchlist
                </button>
              @else
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="fa fa-heart-o"></i> Add to Watchlist
                </button>
              @endif
            </form>
          @endauth
        </div>

        <!-- Synopsis -->
        <div class="mb-2">
          <h6 class="mb-1">Overview</h6>
          <p class="small mb-0" style="line-height: 1.5;">{{ $movie->description }}</p>
        </div>

        <hr class="my-2">

        <!-- Additional Info -->
        <div class="row small">
          <div class="col-md-4">
            <p class="mb-0">
              <strong><i class="fa fa-calendar"></i> Release</strong><br>
              <span class="text-muted">{{ $movie->release_date ? $movie->release_date->format('M d, Y') : 'N/A' }}</span>
            </p>
          </div>
          <div class="col-md-4">
            <p class="mb-0">
              <strong><i class="fa fa-clock"></i> Runtime</strong><br>
              <span class="text-muted">{{ $movie->runtime ?? 'N/A' }} min</span>
            </p>
          </div>
          <div class="col-md-4">
            <p class="mb-0">
              <strong><i class="fa fa-language"></i> Language</strong><br>
              <span class="text-muted">{{ strtoupper($movie->language ?? 'N/A') }}</span>
            </p>
          </div>
        </div>

        <!-- Trailer Section -->
        @if($movie->trailer_link)
          <hr class="my-2">
          <h6 class="mb-2">Trailer</h6>
          <div class="embed-responsive embed-responsive-16by9" style="max-width: 50%;">
            <iframe class="embed-responsive-item" 
                    src="{{ $movie->embedUrl ?? ''}}" 
                    allowfullscreen></iframe>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Right: Reviews Section -->
  <div class="col-md-3">
    <!-- Write Review Form (Only for users who watched) -->
    @auth
      @if($hasWatched)
        <div class="card mb-3">
          <div class="card-header p-2">
            <h6 class="mb-0"><i class="fa fa-star text-warning"></i> Write a Review</h6>
          </div>
          <div class="card-body p-2">
            <form>
              <div class="form-group">
                <label for="rating" class="small">Your Rating</label>
                <select class="form-control form-control-sm" name="rating" id="rating" required>
                  <option value="5" selected>5 - Excellent</option>
                  <option value="4">4 - Very Good</option>
                  <option value="3">3 - Good</option>
                  <option value="2">2 - Fair</option>
                  <option value="1">1 - Poor</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="comment" class="small">Your Review</label>
                <textarea class="form-control form-control-sm" 
                          name="comment" 
                          id="comment" 
                          rows="4" 
                          placeholder="Share your thoughts..." 
                          required></textarea>
              </div>
              
              <button type="submit" class="btn btn-primary btn-sm btn-block">
                <i class="fa fa-paper-plane"></i> Submit
              </button>
            </form>
          </div>
        </div>
      @else
        <div class="card mb-3">
          <div class="card-body p-3 text-center text-muted">
            <i class="fa fa-star-o fa-2x mb-2"></i>
            <p class="small mb-0">Mark this movie as "watched" to write a review</p>
          </div>
        </div>
      @endif
    @else
      <div class="card mb-3">
        <div class="card-body p-3 text-center text-muted">
          <i class="fa fa-lock fa-2x mb-2"></i>
          <p class="small mb-0">Login to write a review</p>
        </div>
      </div>
    @endauth

    <!-- All Reviews (Visible to everyone) -->
    <div class="card">
      <div class="card-header p-2">
        <h6 class="mb-0"><i class="fa fa-comments"></i> Reviews</h6>
      </div>
      <div class="card-body p-2">
        @if($movie->reviews && $movie->reviews->count() > 0)
          @foreach($movie->reviews as $review)
            <div class="mb-3 pb-2 border-bottom">
              <div class="d-flex justify-content-between align-items-start">
                <strong class="small">{{ $review->user->name }}</strong>
                <span class="text-warning small">
                  @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                      <i class="fa fa-star"></i>
                    @else
                      <i class="fa fa-star-o"></i>
                    @endif
                  @endfor
                </span>
              </div>
              <p class="small text-muted mb-1">{{ $review->created_at->diffForHumans() }}</p>
              <p class="small mb-0">{{ $review->comment }}</p>
            </div>
          @endforeach
        @else
          <p class="text-center text-muted small mb-0">No reviews yet. Be the first!</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection