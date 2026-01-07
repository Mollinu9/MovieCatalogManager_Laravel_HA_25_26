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
          @include('partials.alert', [
            'type' => 'success',
            'message' => session('success')
          ])
        @endif

        @if($movies->isEmpty())
          @include('partials.empty-state', [
            'icon' => 'fa-film',
            'title' => 'Your watchlist is empty',
            'description' => 'Start adding movies to your watchlist!',
            'actionUrl' => route('movies.index'),
            'actionText' => 'Browse Movies'
          ])
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
                
                <div class="row">
                  @foreach($groupedMovies[$status] as $movie)
                    <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-3">
                      @include('partials.movie-card', [
                        'movie' => $movie,
                        'layout' => 'watchlist'
                      ])
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>

@include('partials.poster-modal')
@endsection
