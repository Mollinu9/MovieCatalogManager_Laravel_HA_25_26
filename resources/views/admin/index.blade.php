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
                {{ $movies->links('pagination::bootstrap-4') }}
              </div>
            @else
              @include('partials.empty-state', [
                'icon' => 'fa-film',
                'title' => 'No movies found',
                'description' => 'Start by adding your first movie!'
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
