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
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-title">
        <h2 class="mb-0">Manage Movies</h2>
      </div>
      <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('admin.movies.index') }}" method="GET" class="mb-4">
          <div class="row">
            <div class="col-md-8">
              <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search movies by title..." value="{{ request('search') }}">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                  </button>
                  @if(request('search'))
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">
                      <i class="fa fa-times"></i> Clear
                    </a>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4 text-right">
              <span class="text-muted">Total: {{ $movies->total() }} movie(s)</span>
            </div>
          </div>
        </form>

        @if($movies->count() > 0)
              <div class="row">
                @foreach($movies as $index => $movie)
                  <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                    @include('partials.movie-card', [
                      'movie' => $movie,
                      'layout' => 'admin',
                      'index' => $movies->firstItem() + $index
                    ])
                  </div>
                @endforeach
              </div>

              <!-- Pagination -->
              <div class="d-flex justify-content-center mt-4">
                {{ $movies->appends(['search' => request('search')])->links('pagination::bootstrap-4') }}
              </div>
            @else
              @include('partials.empty-state', [
                'icon' => 'fa-search',
                'title' => request('search') ? 'No movies found' : 'No movies available',
                'description' => request('search') ? 'No movies match your search "' . request('search') . '"' : 'Start by adding your first movie!'
              ])
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

@include('partials.poster-modal')
@endsection
