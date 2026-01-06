@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
  <div class="col-md-10">
    <div class="card">
      <div class="card-header card-title">
        <strong>Edit Movie: {{ $movie->title }}</strong>
      </div>           
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if(session('success'))
            @include('partials.alert', [
              'type' => 'success',
              'message' => session('success')
            ])
          @endif

          @if(session('error'))
            @include('partials.alert', [
              'type' => 'error',
              'message' => session('error')
            ])
          @endif

          <!-- Refresh from TMDB Button (only for TMDB movies) -->
          @if($movie->tmdb_id > 0)
            <div class="alert alert-info d-flex align-items-center justify-content-between" role="alert">
              <div>
                <i class="fa fa-info-circle"></i> This movie was imported from TMDB. You can refresh it with the latest data.
              </div>
              <form action="{{ route('admin.movies.refresh', $movie->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('This will overwrite all current data with the latest from TMDB. Continue?')">
                  <i class="fa fa-refresh"></i> Refresh from TMDB
                </button>
              </form>
            </div>
          @endif

          <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
              <div class="col-md-12">
                
                <!-- TMDB ID Display (Read-only) -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label">TMDB ID</label>
                  <div class="col-md-9">
                    @if($movie->tmdb_id > 0)
                      <input type="text" class="form-control" value="{{ $movie->tmdb_id }}" readonly>
                      <small class="form-text text-muted">TMDB ID cannot be changed</small>
                    @else
                      <span class="badge badge-secondary">Manual Entry</span>
                      <small class="form-text text-muted">This movie was added manually</small>
                    @endif
                  </div>
                </div>

                <hr>

                <!-- Title -->
                <div class="form-group row">
                  <label for="title" class="col-md-3 col-form-label">Title <span class="text-danger">*</span></label>
                  <div class="col-md-9">
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $movie->title) }}" required>
                  </div>
                </div>

                <!-- Description -->
                <div class="form-group row">
                  <label for="description" class="col-md-3 col-form-label">Description</label>
                  <div class="col-md-9">
                    <textarea name="description" id="description" rows="4" class="form-control">{{ old('description', $movie->description) }}</textarea>
                  </div>
                </div>

                <!-- Release Date -->
                <div class="form-group row">
                  <label for="release_date" class="col-md-3 col-form-label">Release Date</label>
                  <div class="col-md-9">
                    <input type="date" name="release_date" id="release_date" class="form-control" value="{{ old('release_date', $movie->release_date ? $movie->release_date->format('Y-m-d') : '') }}">
                  </div>
                </div>

                <!-- Runtime -->
                <div class="form-group row">
                  <label for="runtime" class="col-md-3 col-form-label">Runtime (minutes)</label>
                  <div class="col-md-9">
                    <input type="number" name="runtime" id="runtime" class="form-control" value="{{ old('runtime', $movie->runtime) }}">
                  </div>
                </div>

                <!-- Language -->
                <div class="form-group row">
                  <label for="language" class="col-md-3 col-form-label">Language</label>
                  <div class="col-md-9">
                    <input type="text" name="language" id="language" class="form-control" value="{{ old('language', $movie->language) }}">
                  </div>
                </div>

                <!-- Poster URL -->
                <div class="form-group row">
                  <label for="poster_url" class="col-md-3 col-form-label">Poster URL</label>
                  <div class="col-md-9">
                    <input type="url" name="poster_url" id="poster_url" class="form-control" value="{{ old('poster_url', $movie->poster_url) }}">
                    @if($movie->poster_url)
                      <div class="mt-2">
                        <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}" style="max-width: 150px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                      </div>
                    @endif
                  </div>
                </div>

                <!-- Trailer Link -->
                <div class="form-group row">
                  <label for="trailer_link" class="col-md-3 col-form-label">Trailer Link</label>
                  <div class="col-md-9">
                    <input type="url" name="trailer_link" id="trailer_link" class="form-control" value="{{ old('trailer_link', $movie->trailer_link) }}">
                  </div>
                </div>

                <!-- Genres -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label">Genres</label>
                  <div class="col-md-9">
                    <div class="d-flex flex-wrap">
                      @php
                        $selectedGenres = old('genres', $movie->genres->pluck('id')->toArray());
                      @endphp
                      @forelse($genres as $genre)
                        <label class="btn btn-sm m-1 genre-badge {{ in_array($genre->id, $selectedGenres) ? 'selected' : '' }}" style="cursor: pointer;">
                          <input type="checkbox" name="genres[]" value="{{ $genre->id }}" style="display: none;" {{ in_array($genre->id, $selectedGenres) ? 'checked' : '' }}>
                          {{ $genre->name }}
                        </label>
                      @empty
                        <p class="text-muted">No genres available</p>
                      @endforelse
                    </div>
                    <div class="mt-2">
                      <small class="form-text text-muted">Click to select multiple genres</small>
                      <div class="mt-1">
                        <small class="text-muted">
                          <span class="badge badge-warning">Orange</span> → Not selected &nbsp;&nbsp;
                          <span class="badge badge-primary">Blue</span> → Selected
                        </small>
                      </div>
                    </div>
                  </div>
                </div>

                <hr>

                <!-- Action Buttons -->
                <div class="form-group row mb-0">
                  <div class="col-md-9 offset-md-3">
                    <button type="submit" class="btn btn-primary">
                      <i class="fa fa-save"></i> Update Movie
                    </button>
                    <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">
                      <i class="fa fa-arrow-left"></i> Cancel
                    </a>
                  </div>
                </div>

              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/admin.js') }}"></script>
@endpush
