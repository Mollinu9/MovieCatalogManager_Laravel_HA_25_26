{{-- Search Filters Form Partial --}}
{{-- Usage: @include('partials.search-filters', ['genres' => $genres]) --}}

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
              <option value="{{ $genre->id }}" @if(request('genre') == $genre->id) selected @endif>
                {{ $genre->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3 mb-3">
          <label class="col-form-label">Year</label>
          <select name="year" class="custom-select" onchange="this.form.submit()">
            <option value="">All Years</option>
            <option value="2025" @if(request('year') == '2025') selected @endif>2025</option>
            <option value="2024" @if(request('year') == '2024') selected @endif>2024</option>
            <option value="2023" @if(request('year') == '2023') selected @endif>2023</option>
            <option value="2022" @if(request('year') == '2022') selected @endif>2022</option>
            <option value="2021" @if(request('year') == '2021') selected @endif>2021</option>
            <option value="2020" @if(request('year') == '2020') selected @endif>2020</option>
            <option value="2010s" @if(request('year') == '2010s') selected @endif>2010-2019</option>
            <option value="2000s" @if(request('year') == '2000s') selected @endif>2000-2009</option>
            <option value="1990s" @if(request('year') == '1990s') selected @endif>1990-1999</option>
            <option value="1980s" @if(request('year') == '1980s') selected @endif>1980-1989</option>
            <option value="older" @if(request('year') == 'older') selected @endif>Before 1980</option>
          </select>
        </div>

        <div class="col-md-3 mb-3">
          <label class="col-form-label">Sort By</label>
          <select name="sort" class="custom-select" onchange="this.form.submit()">
            <option value="title-asc" @if(request('sort') == 'title-asc') selected @endif>Title (A-Z)</option>
            <option value="title-desc" @if(request('sort') == 'title-desc') selected @endif>Title (Z-A)</option>
            <option value="year-desc" @if(request('sort') == 'year-desc') selected @endif>Year (Newest First)</option>
            <option value="year-asc" @if(request('sort') == 'year-asc') selected @endif>Year (Oldest First)</option>
          </select>
        </div>

        <div class="col-md-3 mb-3">
          <label class="col-form-label">Language</label>
          <select name="language" class="custom-select" onchange="this.form.submit()">
            <option value="">All Languages</option>
            <option value="en" @if(request('language') == 'en') selected @endif>English</option>
            <option value="es" @if(request('language') == 'es') selected @endif>Spanish</option>
            <option value="fr" @if(request('language') == 'fr') selected @endif>French</option>
            <option value="de" @if(request('language') == 'de') selected @endif>German</option>
            <option value="it" @if(request('language') == 'it') selected @endif>Italian</option>
            <option value="ja" @if(request('language') == 'ja') selected @endif>Japanese</option>
            <option value="ko" @if(request('language') == 'ko') selected @endif>Korean</option>
            <option value="zh" @if(request('language') == 'zh') selected @endif>Chinese</option>
          </select>
        </div>
      </div>

      <!-- Additional Filters -->
      <div class="row">
        <div class="col-md-3 mb-3">
          <label class="col-form-label">Runtime</label>
          <select name="runtime" class="custom-select" onchange="this.form.submit()">
            <option value="">Any Length</option>
            <option value="short" @if(request('runtime') == 'short') selected @endif>Under 90 min</option>
            <option value="medium" @if(request('runtime') == 'medium') selected @endif>90-120 min</option>
            <option value="long" @if(request('runtime') == 'long') selected @endif>120-150 min</option>
            <option value="epic" @if(request('runtime') == 'epic') selected @endif>Over 150 min</option>
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
