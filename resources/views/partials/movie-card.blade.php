{{-- Movie Card Partial --}}
{{-- Usage: @include('partials.movie-card', ['movie' => $movie, 'layout' => 'grid']) --}}

@php
    $layout = $layout ?? 'grid';
    $showActions = $showActions ?? true;
@endphp

@switch($layout)
  @case('grid')
    <div class="card search-movie-card h-100">
      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/300x450' }}" class="card-img-top" alt="{{ $movie->title }}">
      <div class="card-body">
        <h5 class="card-title">{{ $movie->title }}</h5>
        <p class="text-muted small mb-2">
          @if($movie->release_date)
            {{ $movie->release_date->format('Y') }}
          @else
            N/A
          @endif
          • {{ $movie->genres->pluck('name')->join(', ') }}
        </p>
        <p class="mb-2">
          <i class="fa fa-clock"></i> {{ $movie->runtime ?? 'N/A' }} min
        </p>
        <p class="card-text small">{{ Str::limit($movie->description, 100) }}</p>
        
        @if($showActions)
          <a href="{{ route('movies.details', $movie->id) }}" class="btn btn-sm btn-primary btn-block">
            View Details
          </a>
        @endif
      </div>
    </div>
    @break
  
  @case('list')
    <div class="card mb-3">
      <div class="row no-gutters">
        <div class="col-md-2">
          <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/150x225' }}" class="card-img" alt="{{ $movie->title }}" style="height: 100%; object-fit: cover;">
        </div>
        <div class="col-md-10">
          <div class="card-body">
            <h5 class="card-title">{{ $movie->title }}</h5>
            <p class="text-muted small mb-2">
              @if($movie->release_date)
                {{ $movie->release_date->format('Y') }}
              @else
                N/A
              @endif
              • {{ $movie->genres->pluck('name')->join(', ') }} • {{ $movie->runtime ?? 'N/A' }} min
            </p>
            <p class="card-text">{{ Str::limit($movie->description, 200) }}</p>
            
            @if($showActions)
              <a href="{{ route('movies.details', $movie->id) }}" class="btn btn-sm btn-primary">
                View Details
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
    @break
  
  @case('compact')
    <div class="media mb-3">
      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/80x120' }}" class="mr-3" alt="{{ $movie->title }}" style="width: 80px; height: 120px; object-fit: cover; border-radius: 4px;">
      <div class="media-body">
        <h6 class="mt-0">{{ $movie->title }}</h6>
        <p class="text-muted small mb-1">
          @if($movie->release_date)
            {{ $movie->release_date->format('Y') }}
          @else
            N/A
          @endif
          • {{ $movie->runtime ?? 'N/A' }} min
        </p>
        <p class="small mb-2">{{ Str::limit($movie->description, 80) }}</p>
        
        @if($showActions)
          <a href="{{ route('movies.details', $movie->id) }}" class="btn btn-xs btn-outline-primary">
            View
          </a>
        @endif
      </div>
    </div>
    @break
@endswitch
