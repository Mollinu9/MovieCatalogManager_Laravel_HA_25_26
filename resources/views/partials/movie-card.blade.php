{{-- Movie Card Partial --}}
{{-- Usage: @include('partials.movie-card', ['movie' => $movie, 'layout' => 'grid']) --}}

@php
    $layout = $layout ?? 'grid';
    $showActions = $showActions ?? true;
    $index = $index ?? null;
@endphp

@switch($layout)
  {{-- ADMIN LAYOUT - Used in: resources/views/admin/index.blade.php --}}
  {{-- Displays movies in admin panel with edit/delete actions --}}
  @case('admin')
    <div class="card h-100 shadow-sm">
      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/200x300?text=No+Image' }}" 
           alt="{{ $movie->title }}" 
           class="card-img-top"
           onclick="openPosterModal('{{ $movie->poster_url ?? 'https://via.placeholder.com/200x300?text=No+Image' }}', '{{ addslashes($movie->title) }}')"
           title="Click to view full poster">
      <div class="card-body d-flex flex-column p-2">
        <h6 class="card-title mb-2">{{ $movie->title }}</h6>
        
        @if($index)
          <div class="mb-1">
            <small class="text-muted"><strong>ID:</strong> {{ $index }}</small>
          </div>
        @endif
        
        <div class="mb-1">
          @if($movie->tmdb_id > 0)
            <span class="badge badge-tmdb">{{ $movie->tmdb_id }}</span>
          @else
            <span class="badge badge-manual">Manual</span>
          @endif
        </div>
        
        <div class="mb-1">
          <small class="text-muted">
            <strong>Year:</strong> {{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }}
          </small>
        </div>
        
        <div class="mb-2">
          @if($movie->genres->count() > 0)
            @foreach($movie->genres->take(2) as $genre)
              <span class="badge badge-primary">{{ $genre->name }}</span>
            @endforeach
            @if($movie->genres->count() > 2)
              <span class="badge badge-secondary">+{{ $movie->genres->count() - 2 }}</span>
            @endif
          @else
            <small class="text-muted">N/A</small>
          @endif
        </div>
        
        @if($movie->description)
          <div class="mb-2">
            <small class="text-muted d-block">{{ $movie->description }}</small>
          </div>
        @else
          <div class="mb-2"></div>
        @endif
        
        <div class="mt-auto">
          <a href="{{ route('movies.details', $movie->id) }}" 
             class="btn btn-sm btn-outline-info w-100 mb-1">
            <i class="fa fa-eye"></i> View
          </a>
          <a href="{{ route('admin.movies.edit', $movie->id) }}" 
             class="btn btn-sm btn-outline-secondary w-100 mb-1">
            <i class="fa fa-edit"></i> Edit
          </a>
          <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" class="mb-0">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="btn btn-sm btn-outline-danger w-100"
                    onclick="return confirm('Are you sure you want to delete \'{{ $movie->title }}\'? This action cannot be undone.')">
              <i class="fa fa-trash"></i> Delete
            </button>
          </form>
        </div>
      </div>
    </div>
    @break

  {{-- WATCHLIST LAYOUT - Used in: resources/views/movies/watchlist.blade.php --}}
  {{-- Displays movies in user's watchlist with status dropdown and remove action --}}
  @case('watchlist')
    <div class="card shadow-sm mb-2">
      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/200x300?text=No+Image' }}" 
           alt="{{ $movie->title }}" 
           class="card-img-top"
           onclick="openPosterModal('{{ $movie->poster_url ?? 'https://via.placeholder.com/200x300?text=No+Image' }}', '{{ addslashes($movie->title) }}')"
           title="Click to view full poster">
      <div class="card-body p-2">
        <p class="mb-2">{{ $movie->title }}</p>
        
        <form action="{{ route('movies.watchlist.updateStatus', $movie->id) }}" method="POST" class="mb-2">
          @csrf
          @method('PATCH')
          <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="to_watch" {{ $movie->pivot->status == 'to_watch' ? 'selected' : '' }}>To Watch</option>
            <option value="watching" {{ $movie->pivot->status == 'watching' ? 'selected' : '' }}>Watching</option>
            <option value="watched" {{ $movie->pivot->status == 'watched' ? 'selected' : '' }}>Watched</option>
          </select>
        </form>
        
        <div class="d-flex">
          <a href="{{ route('movies.details', $movie->id) }}" 
             class="btn btn-sm btn-outline-info flex-fill mr-1">
            <i class="fa fa-eye"></i>
          </a>
          <form action="{{ route('movies.watchlist.remove', $movie->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Remove from watchlist?');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="btn btn-sm btn-outline-danger w-100">
              <i class="fa fa-times"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
    @break

  {{-- GRID LAYOUT - Used in: resources/views/movies/index.blade.php, search results --}}
  {{-- Displays movies in a grid format with basic info and view details button --}}
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
          @if($movie->language)
            • <i class="fa fa-language"></i> {{ strtoupper($movie->language) }}
          @endif
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
          <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/150x225' }}" class="card-img movie-card-img-list" alt="{{ $movie->title }}">
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
              @if($movie->language)
                • {{ strtoupper($movie->language) }}
              @endif
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
      <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/80x120' }}" class="mr-3 movie-card-img-compact" alt="{{ $movie->title }}">
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
