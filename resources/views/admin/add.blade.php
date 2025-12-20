@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
  <div class="row justify-content-md-center">
    <div class="col-md-10">
      <div class="card">
        <div class="card-header card-title">
          <strong>Add New Movie</strong>
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

          <form action="{{ route('admin.movies.store') }}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-12">
                <!-- Input Method Selection -->
                <div class="form-group row">
                  <label class="col-md-3 col-form-label">Input Method</label>
                  <div class="col-md-9">
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                      <label class="btn input-method-badge text-white selected" id="tmdb-option" style="cursor: pointer;">
                        <input type="radio" name="input_method" value="tmdb" checked> TMDB Import
                      </label>
                      <label class="btn input-method-badge text-white" id="manual-option" style="cursor: pointer;">
                        <input type="radio" name="input_method" value="manual"> Manual Entry
                      </label>
                    </div>
                    <div class="mt-2">
                      <small class="form-text text-muted">Choose TMDB for automatic data or Manual for local movies</small>
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

                <!-- TMDB Search Section (shown only for TMDB import) -->
                <div id="tmdb-section">
                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Search Method</label>
                    <div class="col-md-9">
                      <div class="btn-group btn-group-toggle mb-3" data-toggle="buttons">
                        <label class="btn btn-outline-primary active" id="search-by-title-option">
                          <input type="radio" name="tmdb_search_method" value="title" checked> Search by Title
                        </label>
                        <label class="btn btn-outline-primary" id="search-by-id-option">
                          <input type="radio" name="tmdb_search_method" value="id"> Search by TMDB ID
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- Search by Title -->
                  <div class="form-group row" id="tmdb-title-search">
                    <label for="tmdb_search_title" class="col-md-3 col-form-label">Movie Title</label>
                    <div class="col-md-9">
                      <div class="input-group">
                        <input type="text" name="tmdb_search_title" id="tmdb_search_title" class="form-control" placeholder="e.g., The Dark Knight">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="search-tmdb-btn">
                            <i class="fa fa-search"></i> Search
                          </button>
                        </div>
                      </div>
                      <small class="form-text text-muted">Search for a movie on TMDB by its title</small>
                    </div>
                  </div>

                  <!-- Search by TMDB ID -->
                  <div class="form-group row" id="tmdb-id-search" style="display: none;">
                    <label for="tmdb_id" class="col-md-3 col-form-label">TMDB ID</label>
                    <div class="col-md-9">
                      <div class="input-group">
                        <input type="number" name="tmdb_id" id="tmdb_id" class="form-control" placeholder="e.g., 155">
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="fetch-tmdb-btn">
                            <i class="fa fa-download"></i> Fetch Data
                          </button>
                        </div>
                      </div>
                      <small class="form-text text-muted">Enter the TMDB ID from <a href="https://www.themoviedb.org/" target="_blank">themoviedb.org</a></small>
                    </div>
                  </div>

                  <!-- Search Results (will be populated by JavaScript) -->
                  <div id="tmdb-search-results" class="mb-3"></div>
                </div>

                <!-- Manual Entry Section (hidden by default) -->
                <div id="manual-section" style="display: none;">
                  <div class="form-group row">
                    <label for="title" class="col-md-3 col-form-label">Title</label>
                    <div class="col-md-9">
                      <input type="text" name="title" id="title" class="form-control">
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
                          <label class="btn btn-sm m-1 genre-badge text-white" style="cursor: pointer;">
                            <input type="checkbox" name="genres[]" value="{{ $genre->id }}" style="display: none;">
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
                  <div class="form-group row mb-0">
                    <div class="col-md-9 offset-md-3">
                        <button type="button" class="btn btn-primary" id="preview-btn">
                          <i class="fa fa-eye"></i> Preview Movie
                        </button>
                        <a href="{{ route('movies.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
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
<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Movie Preview</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <img id="preview-poster" src="https://via.placeholder.com/300x450" class="img-fluid" alt="Movie Poster">
          </div>
          <div class="col-md-8">
            <h3 id="preview-title">Movie Title</h3>
            <p class="text-muted" id="preview-meta">Year • Genre</p>
            <p id="preview-description">Description will appear here...</p>
            <p><strong>Runtime:</strong> <span id="preview-runtime">N/A</span></p>
            <p><strong>Language:</strong> <span id="preview-language">N/A</span></p>
            <p><strong>Genres:</strong> <span id="preview-genres">N/A</span></p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
          <i class="fa fa-arrow-left"></i> Go Back
        </button>
        <button type="button" class="btn btn-success" id="save-from-preview">
          <i class="fa fa-check"></i> Confirm & Save
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/admin.js') }}"></script>
@endpush
