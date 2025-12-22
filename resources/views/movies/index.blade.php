@extends('layouts.app')

@section('genre-filter')
<!-- Genre Filter Bar -->
<div class="genre-filter-bar">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center py-3">
      <span class="mr-3 font-weight-bold">Filter by Genre:</span>
      <a href="{{ route('movies.index') }}" class="btn btn-sm m-1 {{ !request('genre') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="fa fa-th"></i> All Movies
      </a>
      @forelse($allGenres as $genre)
        @if($genre->movies_count > 0)
          <a href="{{ route('movies.index', ['genre' => $genre->id]) }}" class="btn btn-sm m-1 {{ request('genre') == $genre->id ? 'btn-primary' : 'btn-outline-primary' }}">
            {{ $genre->name }}
          </a>
        @else
          <button class="btn btn-sm m-1 btn-outline-secondary" disabled style="cursor: not-allowed; opacity: 0.5;">
            {{ $genre->name }}
          </button>
        @endif
      @empty
        <span class="text-muted">No genres available</span>
      @endforelse
    </div>
  </div>
</div>
@endsection

@section('content')
<!-- content -->
    <main class="py-5">
      <div class="container-fluid px-4">
        <!-- Success Message -->
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        @endif

        <!-- Header -->
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">Browse Movies</h2>
          </div>
        </div>

        <!-- Movies by Genre -->
        @forelse($genres as $genre)
          @if($genre->movies->count() > 0)
            <div class="mb-5" id="genre-{{ $genre->slug }}">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">{{ $genre->name }}</h3>
                @if($genre->movies->count() > 6)
                  <a href="{{ route('movies.search', ['genre' => $genre->id]) }}" class="btn btn-sm btn-outline-primary">
                    View All ({{ $genre->movies->count() }})
                  </a>
                @endif
              </div>
              <div class="row">
                @foreach($genre->movies->take(6) as $movie)
                  <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card search-movie-card h-100">
                      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" class="card-img-top" alt="{{ $movie->title }}">
                      <div class="card-body">
                        <h5 class="card-title">{{ $movie->title }}</h5>
                        <p class="text-muted small mb-2">
                          {{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }} • 
                          {{ $movie->genres->pluck('name')->join(', ') }}
                        </p>
                        <p class="mb-2">
                          <i class="fa fa-clock"></i> {{ $movie->runtime ?? 'N/A' }} min
                        </p>
                        <p class="card-text small">{{ Str::limit($movie->description, 100) }}</p>
                        <a href="{{ route('movies.details', $movie->id) }}" class="btn btn-sm btn-primary btn-block">View Details</a>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        @empty
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> No movies available yet. Add some movies to get started!
          </div>
        @endforelse

      </div>
    </main>
@endsection