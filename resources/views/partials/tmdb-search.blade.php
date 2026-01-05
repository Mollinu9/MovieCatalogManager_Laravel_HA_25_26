{{-- TMDB Search Partial --}}
{{-- Usage: @include('partials.tmdb-search', ['context' => 'admin' or 'user']) --}}

<!-- Search Method Selection -->
<div class="form-group {{ isset($formLayout) && $formLayout === 'horizontal' ? 'row' : '' }}">
  <label class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'col-md-3 col-form-label' : '' }}">Search Method</label>
  <div class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'col-md-9' : '' }}">
    <div class="btn-group btn-group-toggle {{ isset($formLayout) && $formLayout === 'horizontal' ? 'mb-3' : 'd-block mb-3' }}" data-toggle="buttons">
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
<div class="form-group {{ isset($formLayout) && $formLayout === 'horizontal' ? 'row' : '' }}" id="tmdb-title-search">
  <label for="tmdb_search_title" class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'col-md-3 col-form-label' : '' }}">Movie Title</label>
  <div class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'col-md-9' : '' }}">
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
<div class="form-group {{ isset($formLayout) && $formLayout === 'horizontal' ? 'row' : '' }}" id="tmdb-id-search" style="display: none;">
  <label for="tmdb_id" class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'col-md-3 col-form-label' : '' }}">TMDB ID</label>
  <div class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'col-md-9' : '' }}">
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

<!-- Search Results -->
<div id="tmdb-search-results" class="{{ isset($formLayout) && $formLayout === 'horizontal' ? 'mb-3' : 'mt-3' }}"></div>
