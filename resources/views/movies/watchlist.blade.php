@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card watchlist-card">
      <div class="card-header card-title">
        <div class="d-flex align-items-center justify-content-between">
          <h2 class="mb-0"><i class="fa fa-heart mr-2"></i>My Watchlist</h2>
          <span class="badge badge-light">{{ $movies->count() }} Movies</span>
        </div>
      </div>
      <div class="card-body">
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif

        @if($movies->isEmpty())
          <div class="text-center py-5">
            <i class="fa fa-film" style="font-size: 80px; color: #dee2e6;"></i>
            <h4 class="mt-4 text-muted">Your watchlist is empty</h4>
            <p class="text-muted">Start adding movies to your watchlist!</p>
            <a href="{{ route('movies.index') }}" class="btn btn-primary mt-3">
              <i class="fa fa-search mr-2"></i>Browse Movies
            </a>
          </div>
        @else
          @php
            // Group movies by status
            $groupedMovies = $movies->groupBy('pivot.status');
            $statusOrder = ['to_watch', 'watching', 'watched'];
            $statusConfig = [
              'to_watch' => ['label' => 'To Watch', 'icon' => 'fa-bookmark', 'color' => 'primary'],
              'watching' => ['label' => 'Currently Watching', 'icon' => 'fa-play-circle', 'color' => 'info'],
              'watched' => ['label' => 'Watched', 'icon' => 'fa-check-circle', 'color' => 'success']
            ];
          @endphp

          @foreach($statusOrder as $status)
            @if($groupedMovies->has($status) && $groupedMovies[$status]->count() > 0)
              <div class="mb-4">
                <h4 class="text-{{ $statusConfig[$status]['color'] }} mb-3">
                  <i class="fa {{ $statusConfig[$status]['icon'] }}"></i> 
                  {{ $statusConfig[$status]['label'] }} 
                  <span class="badge badge-{{ $statusConfig[$status]['color'] }}">{{ $groupedMovies[$status]->count() }}</span>
                </h4>
                
                <div class="table-responsive">
                  <table class="table table-hover watchlist-table">
                    <thead>
                      <tr>
                        <th scope="col" width="120">Poster</th>
                        <th scope="col" width="250">Title</th>
                        <th scope="col" width="100">Year</th>
                        <th scope="col" width="200">Genres</th>
                        <th scope="col" width="100">Runtime</th>
                        <th scope="col" width="150">Status</th>
                        <th scope="col" width="150" class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($groupedMovies[$status] as $movie)
                      <tr>
                        <td>
                          @if($movie->poster_url)
                            <img src="{{ $movie->poster_url }}" 
                                 alt="{{ $movie->title }}" 
                                 class="watchlist-poster">
                          @else
                            <div class="watchlist-poster-placeholder">
                              <i class="fa fa-film"></i>
                            </div>
                          @endif
                        </td>
                        <td>
                          <strong class="watchlist-movie-title">{{ $movie->title }}</strong>
                        </td>
                        <td>
                          @if($movie->release_date)
                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                          @else
                            <span class="text-muted">N/A</span>
                          @endif
                        </td>
                        <td>
                          @if($movie->genres->isNotEmpty())
                            <span class="watchlist-genres">
                              {{ $movie->genres->pluck('name')->join(', ') }}
                            </span>
                          @else
                            <span class="text-muted">No genres</span>
                          @endif
                        </td>
                        <td>
                          @if($movie->runtime)
                            {{ $movie->runtime }} min
                          @else
                            <span class="text-muted">N/A</span>
                          @endif
                        </td>
                        <td>
                          <form action="{{ route('movies.watchlist.updateStatus', $movie->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                              <option value="to_watch" {{ $movie->pivot->status == 'to_watch' ? 'selected' : '' }}>To Watch</option>
                              <option value="watching" {{ $movie->pivot->status == 'watching' ? 'selected' : '' }}>Watching</option>
                              <option value="watched" {{ $movie->pivot->status == 'watched' ? 'selected' : '' }}>Watched</option>
                            </select>
                          </form>
                        </td>
                        <td class="text-center">
                          <a href="{{ route('movies.details', $movie->id) }}" 
                             class="btn btn-sm btn-circle btn-outline-info" 
                             title="View Details">
                            <i class="fa fa-eye"></i>
                          </a>
                          <form action="{{ route('movies.watchlist.remove', $movie->id) }}" 
                                method="POST" 
                                style="display: inline;"
                                onsubmit="return confirm('Remove {{ $movie->title }} from your watchlist?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-circle btn-outline-danger" 
                                    title="Remove from Watchlist">
                              <i class="fa fa-times"></i>
                            </button>
                          </form>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            @endif
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
