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
                <div class="form-group row">
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
                  <label for="genres" class="col-md-3 col-form-label">Genres</label>
                  <div class="col-md-9">
                    <select name="genres[]" id="genres" class="form-control" multiple size="5">
                      <option value="1">Action</option>
                      <option value="2">Comedy</option>
                      <option value="3">Drama</option>
                      <option value="4">Horror</option>
                      <option value="5">Sci-Fi</option>
                      <option value="6">Thriller</option>
                      <option value="7">Romance</option>
                      <option value="8">Crime</option>
                    </select>
                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
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
