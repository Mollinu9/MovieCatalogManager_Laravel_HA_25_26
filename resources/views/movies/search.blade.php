@extends('layouts.app')

@section('genre-filter')
        <!-- Search Filters Card -->
        <div class="row mb-4">
          <div class="col-md-12">
            <div class="card shadow-sm">
              <div class="card-body p-4">
                <form method="GET" action="{{ route('movies.search') }}">
                  <!-- Search Input -->
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <div class="input-group input-group-lg">
                        <input type="text" name="search" class="form-control" placeholder="Search by title..." value="{{ request('search') }}" aria-label="Search movies">
                        <div class="input-group-append">
                          <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i> Search
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Advanced Filters -->
                  <div class="row">
                    <div class="col-md-3 mb-3">
                      <label class="col-form-label">Genre</label>
                      <select name="genre" class="custom-select" onchange="this.form.submit()">
                        <option value="">All Genres</option>
                        @foreach($genres as $genre)
                          <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                            {{ $genre->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <div class="col-md-3 mb-3">
                      <label class="col-form-label">Year</label>
                      <select name="year" class="custom-select" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <option value="2020s" {{ request('year') == '2020s' ? 'selected' : '' }}>2020s</option>
                        <option value="2010s" {{ request('year') == '2010s' ? 'selected' : '' }}>2010s</option>
                        <option value="2000s" {{ request('year') == '2000s' ? 'selected' : '' }}>2000s</option>
                        <option value="1990s" {{ request('year') == '1990s' ? 'selected' : '' }}>1990s</option>
                        <option value="1980s" {{ request('year') == '1980s' ? 'selected' : '' }}>1980s</option>
                        <option value="older" {{ request('year') == 'older' ? 'selected' : '' }}>Before 1980</option>
                      </select>
                    </div>

                    <div class="col-md-3 mb-3">
                      <label class="col-form-label">Sort By</label>
                      <select name="sort" class="custom-select" onchange="this.form.submit()">
                        <option value="title-asc" {{ request('sort') == 'title-asc' ? 'selected' : '' }}>Title (A-Z)</option>
                        <option value="title-desc" {{ request('sort') == 'title-desc' ? 'selected' : '' }}>Title (Z-A)</option>
                        <option value="year-desc" {{ request('sort') == 'year-desc' ? 'selected' : '' }}>Year (Newest First)</option>
                        <option value="year-asc" {{ request('sort') == 'year-asc' ? 'selected' : '' }}>Year (Oldest First)</option>
                      </select>
                    </div>

                    <div class="col-md-3 mb-3">
                      <label class="col-form-label">Language</label>
                      <select name="language" class="custom-select" onchange="this.form.submit()">
                        <option value="">All Languages</option>
                        <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>English</option>
                        <option value="es" {{ request('language') == 'es' ? 'selected' : '' }}>Spanish</option>
                        <option value="fr" {{ request('language') == 'fr' ? 'selected' : '' }}>French</option>
                        <option value="de" {{ request('language') == 'de' ? 'selected' : '' }}>German</option>
                        <option value="it" {{ request('language') == 'it' ? 'selected' : '' }}>Italian</option>
                        <option value="ja" {{ request('language') == 'ja' ? 'selected' : '' }}>Japanese</option>
                        <option value="ko" {{ request('language') == 'ko' ? 'selected' : '' }}>Korean</option>
                        <option value="zh" {{ request('language') == 'zh' ? 'selected' : '' }}>Chinese</option>
                      </select>
                    </div>
                  </div>

                  <!-- Additional Filters -->
                  <div class="row">
                    <div class="col-md-3 mb-3">
                      <label class="col-form-label">Runtime</label>
                      <select name="runtime" class="custom-select" onchange="this.form.submit()">
                        <option value="">Any Length</option>
                        <option value="short" {{ request('runtime') == 'short' ? 'selected' : '' }}>Under 90 min</option>
                        <option value="medium" {{ request('runtime') == 'medium' ? 'selected' : '' }}>90-120 min</option>
                        <option value="long" {{ request('runtime') == 'long' ? 'selected' : '' }}>120-150 min</option>
                        <option value="epic" {{ request('runtime') == 'epic' ? 'selected' : '' }}>Over 150 min</option>
                      </select>
                    </div>

                    <div class="col-md-3 mb-3">
                      <label class="col-form-label">&nbsp;</label>
                      <a href="{{ route('movies.search') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fa fa-refresh"></i> Reset Filters
                      </a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
@endsection

@section('content')
    <main class="py-5">
      <div class="container-fluid px-4">

        <!-- Results Header -->
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Search Results <span class="text-muted">({{ $movies->total() }} movies found)</span></h5>
            </div>
          </div>
        </div>

        <!-- Search Results Grid -->
        <div class="row">
          @forelse($movies as $movie)
            <div class="col-md-3 col-sm-6 mb-4">
              @include('partials.movie-card', [
                'movie' => $movie,
                'layout' => 'grid'
              ])
            </div>
          @empty
            <div class="col-md-12">
              @include('partials.alert', [
                'type' => 'info',
                'message' => 'No movies found matching your search criteria. Try adjusting your filters.'
              ])
            </div>
          @endforelse
        </div>

        <!-- Pagination -->
        @if($movies->hasPages())
          <div class="row mt-4">
            <div class="col-md-12 d-flex justify-content-center">
              {{ $movies->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
          </div>
        @endif

      </div>
    </main>
@endsection