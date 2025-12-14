@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-md-center">
    <div class="col-md-10">
      <div class="card">
        <div class="card-header card-title">
          <strong>Add New Movie</strong>
        </div>           
        <div class="card-body">
          <form action="#" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-12">
                <!-- Input Method Selection -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label">Input Method</label>
                  <div class="col-md-9">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                      <label class="btn btn-outline-primary active" id="tmdb-option">
                        <input type="radio" name="input_method" value="tmdb" checked> TMDB Import
                      </label>
                      <label class="btn btn-outline-primary" id="manual-option">
                        <input type="radio" name="input_method" value="manual"> Manual Entry
                      </label>
                    </div>
                    <small class="form-text text-muted">Choose TMDB for automatic data or Manual for local movies</small>
                  </div>
                </div>

                <!-- TMDB ID Field (shown only for TMDB import) -->
                <div class="form-group row" id="tmdb-section">
                  <label for="tmdb_id" class="col-md-3 col-form-label">TMDb ID</label>
                  <div class="col-md-9">
                    <input type="number" name="tmdb_id" id="tmdb_id" class="form-control" placeholder="e.g., 155">
                    <small class="form-text text-muted">Enter TMDb ID to auto-fill movie details</small>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="title" class="col-md-3 col-form-label">Title</label>
                  <div class="col-md-9">
                    <input type="text" name="title" id="title" class="form-control" required>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="description" class="col-md-3 col-form-label">Description</label>
                  <div class="col-md-9">
                    <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                  </div>
                </div>

                <div class="form-group row">
                  <label for="release_date" class="col-md-3 col-form-label">Release Date</label>
                  <div class="col-md-9">
                    <input type="date" name="release_date" id="release_date" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="runtime" class="col-md-3 col-form-label">Runtime (minutes)</label>
                  <div class="col-md-9">
                    <input type="number" name="runtime" id="runtime" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="language" class="col-md-3 col-form-label">Language</label>
                  <div class="col-md-9">
                    <input type="text" name="language" id="language" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="poster_url" class="col-md-3 col-form-label">Poster URL</label>
                  <div class="col-md-9">
                    <input type="url" name="poster_url" id="poster_url" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label for="trailer_link" class="col-md-3 col-form-label">Trailer Link</label>
                  <div class="col-md-9">
                    <input type="url" name="trailer_link" id="trailer_link" class="form-control">
                  </div>
                </div>

                <div class="form-group row">
                  <label class="col-md-3 col-form-label">Genres</label>
                  <div class="col-md-9">
                    <div class="d-flex flex-wrap">
                      @forelse($genres as $genre)
                        <label class="btn btn-outline-warning btn-sm m-1 genre-badge" style="cursor: pointer;">
                          <input type="checkbox" name="genres[]" value="{{ $genre->id }}" style="display: none;">
                          {{ $genre->name }}
                        </label>
                      @empty
                        <p class="text-muted">No genres available</p>
                      @endforelse
                    </div>
                    <small class="form-text text-muted">Click to select multiple genres</small>
                  </div>
                </div>

                <hr>
                <div class="form-group row mb-0">
                  <div class="col-md-9 offset-md-3">
                      <button type="submit" class="btn btn-primary">Save Movie</button>
                      <a href="{{ route('movies.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
