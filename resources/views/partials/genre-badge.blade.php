{{-- Genre Badge Partial --}}
{{-- Usage: @include('partials.genre-badge', ['genre' => $genre, 'clickable' => true]) --}}

@php
    $clickable = $clickable ?? false;
    $size = $size ?? 'md';
    $genreName = is_object($genre) ? $genre->name : $genre;
    $genreId = is_object($genre) ? $genre->id : null;
    
    switch($size) {
        case 'sm':
            $sizeClass = 'badge-sm';
            break;
        case 'lg':
            $sizeClass = 'badge-lg';
            break;
        default:
            $sizeClass = '';
    }
@endphp

@if($clickable && $genreId)
  <a href="{{ route('movies.search', ['genre' => $genreId]) }}" class="genre-badge {{ $sizeClass }}">
    {{ $genreName }}
  </a>
@else
  <span class="genre-badge {{ $sizeClass }}">
    {{ $genreName }}
  </span>
@endif
