{{-- Genre Filter Bar Partial --}}
{{-- Usage: @include('partials.genre-filter', ['genres' => $allGenres, 'selectedGenre' => $selectedGenreId]) --}}

@php
    $selectedGenre = $selectedGenre ?? null;
    $route = $route ?? 'movies.index';
@endphp

<div class="genre-filter-bar">
  <div class="container">
    <div class="d-flex flex-wrap align-items-center py-3">
      <span class="mr-3 font-weight-bold">Filter by Genre:</span>
      
      <a href="{{ route($route) }}" class="btn btn-sm m-1 @if(!$selectedGenre) btn-primary @else btn-outline-primary @endif">
        <i class="fa fa-th"></i> All Movies
      </a>
      
      @foreach($genres as $genre)
        @if(!isset($genre->movies_count) || $genre->movies_count > 0)
          <a href="{{ route($route, ['genre' => $genre->id]) }}" class="btn btn-sm m-1 @if($selectedGenre == $genre->id) btn-primary @else btn-outline-primary @endif">
            {{ $genre->name }}
          </a>
        @else
          <button class="btn btn-sm m-1 btn-outline-secondary genre-btn-disabled" disabled>
            {{ $genre->name }}
          </button>
        @endif
      @endforeach
    </div>
  </div>
</div>
