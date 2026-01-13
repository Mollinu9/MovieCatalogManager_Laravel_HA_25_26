@extends('layouts.app')

@section('genre-filter')
  @include('partials.genre-filter', [
    'genres' => $allGenres,
    'selectedGenre' => request('genre'),
    'route' => 'movies.index'
  ])
@endsection

@section('content')
<!-- Success Message -->
@if(session('success'))
  @include('partials.alert', [
    'type' => 'success',
    'message' => session('success')
  ])
@endif

<!-- Header -->
<div class="row mb-4">
  <div class="col-md-12">
    <h2 class="mb-0">Browse Movies</h2>
  </div>
</div>

<!-- Movies by Genre -->
@forelse($genres as $genre)
  @if($genre->movies->count() > 2)
    <div class="mb-5" id="genre-{{ $genre->slug }}">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">{{ $genre->name }}</h3>
        @if($genre->movies->count() > 4)
          <a href="{{ route('movies.search', ['genre' => $genre->id]) }}" class="btn btn-sm btn-outline-primary">
            View All ({{ $genre->movies->count() }})
          </a>
        @endif
      </div>
      <div class="row">
        @foreach($genre->movies->take(4) as $movie)
          <div class="col-md-3 col-sm-6 mb-4">
            @include('partials.movie-card', [
              'movie' => $movie,
              'layout' => 'grid'
            ])
          </div>
        @endforeach
      </div>
    </div>
  @endif
@empty
  @include('partials.empty-state', [
    'icon' => 'fa-film',
    'title' => 'No movies available yet',
    'description' => 'Add some movies to get started!'
  ])
@endforelse
@endsection