@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
  <div class="col-md-10">
    <div class="card">
      <div class="card-header card-title">
        <strong>Add New Movie</strong>
      </div>           
        <div class="card-body">
          @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong><i class="fa fa-exclamation-triangle"></i> Warning:</strong> {{ session('warning') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          @endif

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
                      <label class="btn input-method-badge text-white selected cursor-pointer" id="tmdb-option">
                        <input type="radio" name="input_method" value="tmdb" checked> TMDB Import
                      </label>
                      <label class="btn input-method-badge text-white cursor-pointer" id="manual-option">
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
                  @include('partials.tmdb-search', [
                    'formLayout' => 'horizontal'
                  ])
                </div>

                <!-- Manual Entry Section (hidden by default) -->
                <div id="manual-section" class="d-none">
                  <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> <strong>Note:</strong> Fields marked with <span class="text-danger">*</span> are required.
                  </div>

                  <!-- Hidden field for tmdb_id (will be auto-generated as negative number) -->
                  <input type="hidden" name="tmdb_id_manual" id="tmdb_id_manual" value="">
                  
                  <div class="form-group row">
                    <label for="title" class="col-md-3 col-form-label">Title <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                      <input type="text" name="title" id="title" class="form-control" placeholder="Enter movie title" >
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="description" class="col-md-3 col-form-label">Description <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                      <textarea name="description" id="description" rows="4" class="form-control" placeholder="Enter movie description" ></textarea>
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="release_date" class="col-md-3 col-form-label">Release Date <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                      <input type="date" name="release_date" id="release_date" class="form-control" >
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="runtime" class="col-md-3 col-form-label">Runtime (minutes) <small class="text-muted">(Optional)</small></label>
                    <div class="col-md-9">
                      <input type="number" name="runtime" id="runtime" class="form-control" placeholder="e.g. 120" min="1">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="language" class="col-md-3 col-form-label">Language <span class="text-danger">*</span></label>
                    <div class="col-md-9">
                      <input type="text" name="language" id="language" class="form-control" placeholder="e.g. en, fr, es">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="poster_url" class="col-md-3 col-form-label">Poster URL <small class="text-muted">(Optional)</small></label>
                    <div class="col-md-9">
                      <input type="url" name="poster_url" id="poster_url" class="form-control" placeholder="https://example.com/poster.jpg">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label for="trailer_link" class="col-md-3 col-form-label">Trailer Link <small class="text-muted">(Optional)</small></label>
                    <div class="col-md-9">
                      <input type="url" name="trailer_link" id="trailer_link" class="form-control" placeholder="https://youtube.com/watch?v=...">
                    </div>
                  </div>

                  <div class="form-group row">
                    <label class="col-md-3 col-form-label">Genres <small class="text-muted">(Optional)</small></label>
                    <div class="col-md-9">
                      <div class="d-flex flex-wrap">
                        @forelse($genres as $genre)
                          <label class="btn btn-sm m-1 genre-badge cursor-pointer">
                            <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="d-none">
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
                        <button type="submit" class="btn btn-success">
                          <i class="fa fa-save"></i> Save Movie
                        </button>
                        <a href="{{ route('admin.movies.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/tmdb-search.js') }}"></script>
<script src="{{ asset('assets/js/admin.js') }}"></script>
@endpush
