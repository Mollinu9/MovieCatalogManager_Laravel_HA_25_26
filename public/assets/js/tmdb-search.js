/**
 * TMDB Search Shared Functionality
 * Used by both admin add movie and user request pages
 */

// Initialize TMDB search functionality
function initTmdbSearch(context = 'admin') {
    // Search Method Toggle (Title vs ID)
    const searchByTitleOption = document.getElementById('search-by-title-option');
    const searchByIdOption = document.getElementById('search-by-id-option');
    const tmdbTitleSearch = document.getElementById('tmdb-title-search');
    const tmdbIdSearch = document.getElementById('tmdb-id-search');

    if (searchByTitleOption && searchByIdOption && tmdbTitleSearch && tmdbIdSearch) {
        searchByTitleOption.addEventListener('click', function () {
            tmdbTitleSearch.style.display = 'flex';
            tmdbIdSearch.style.display = 'none';
        });

        searchByIdOption.addEventListener('click', function () {
            tmdbTitleSearch.style.display = 'none';
            tmdbIdSearch.style.display = 'flex';
        });
    }

    // Search by Title
    const searchTmdbBtn = document.getElementById('search-tmdb-btn');
    const tmdbSearchTitle = document.getElementById('tmdb_search_title');
    const tmdbSearchResults = document.getElementById('tmdb-search-results');

    if (searchTmdbBtn && tmdbSearchTitle && tmdbSearchResults) {
        searchTmdbBtn.addEventListener('click', function () {
            const query = tmdbSearchTitle.value.trim();
            if (!query) {
                alert('Please enter a movie title');
                return;
            }

            // Show loading
            tmdbSearchResults.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Searching TMDB...</div>';

            // Make AJAX request
            fetch('/admin/tmdb/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCSRFToken()
                },
                body: JSON.stringify({ query: query })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.results.length > 0) {
                        displaySearchResults(data.results, context);
                    } else {
                        tmdbSearchResults.innerHTML = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No movies found. Try a different search term.</div>';
                    }
                })
                .catch(error => {
                    tmdbSearchResults.innerHTML = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Error searching TMDB. Please try again.</div>';
                    console.error('Error:', error);
                });
        });

        // Allow Enter key to trigger search
        tmdbSearchTitle.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchTmdbBtn.click();
            }
        });
    }

    // Fetch by ID
    const fetchTmdbBtn = document.getElementById('fetch-tmdb-btn');
    const tmdbIdInput = document.getElementById('tmdb_id');

    if (fetchTmdbBtn && tmdbIdInput && tmdbSearchResults) {
        fetchTmdbBtn.addEventListener('click', function () {
            const tmdbId = tmdbIdInput.value.trim();
            if (!tmdbId) {
                alert('Please enter a TMDB ID');
                return;
            }

            fetchMovieDetails(tmdbId, context);
        });

        // Allow Enter key to trigger fetch
        tmdbIdInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchTmdbBtn.click();
            }
        });
    }
}

// Display search results
function displaySearchResults(results, context) {
    const tmdbSearchResults = document.getElementById('tmdb-search-results');
    let html = '<div class="card"><div class="card-header"><strong>Search Results</strong> - Click on a movie to view details</div><div class="card-body"><div class="list-group">';

    results.forEach(movie => {
        const year = movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A';
        const posterUrl = movie.poster_path
            ? `https://image.tmdb.org/t/p/w92${movie.poster_path}`
            : 'https://via.placeholder.com/92x138';

        html += `
            <a href="#" class="list-group-item list-group-item-action tmdb-result-item" data-tmdb-id="${movie.id}">
                <div class="d-flex align-items-center">
                    <img src="${posterUrl}" alt="${movie.title}" class="mr-3" style="width: 46px; height: 69px; object-fit: cover; border-radius: 4px;">
                    <div>
                        <h6 class="mb-1">${movie.title} <span class="text-muted">(${year})</span></h6>
                        <small class="text-muted">${movie.overview ? movie.overview.substring(0, 100) + '...' : 'No description available'}</small>
                    </div>
                </div>
            </a>
        `;
    });

    html += '</div></div></div>';
    tmdbSearchResults.innerHTML = html;

    // Add click handlers to results
    document.querySelectorAll('.tmdb-result-item').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const tmdbId = this.getAttribute('data-tmdb-id');
            fetchMovieDetails(tmdbId, context);
        });
    });
}

// Fetch movie details from TMDB
function fetchMovieDetails(tmdbId, context) {
    const tmdbSearchResults = document.getElementById('tmdb-search-results');
    tmdbSearchResults.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Fetching movie details...</div>';

    fetch('/admin/tmdb/fetch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        },
        body: JSON.stringify({ tmdb_id: tmdbId })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMovieDetails(data.movie, context);
            } else if (data.existing) {
                // Movie already exists in database
                tmdbSearchResults.innerHTML = `
                <div class="alert alert-warning">
                    <h5><i class="fa fa-exclamation-triangle"></i> Movie Already Exists</h5>
                    <p>The movie "<strong>${data.movie_title}</strong>" is already in the database.</p>
                    <a href="/movies/${data.movie_id}" class="btn btn-sm btn-primary" target="_blank">
                        <i class="fa fa-eye"></i> View Movie
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="search-again-duplicate">
                        <i class="fa fa-search"></i> Search Another Movie
                    </button>
                </div>
            `;

                // Add event listener for search again button
                document.getElementById('search-again-duplicate').addEventListener('click', clearSearch);
            } else {
                tmdbSearchResults.innerHTML = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> ' + (data.error || 'Failed to fetch movie details.') + '</div>';
            }
        })
        .catch(error => {
            tmdbSearchResults.innerHTML = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Error fetching movie details. Please try again.</div>';
            console.error('Error:', error);
        });
}

// Display movie details
function displayMovieDetails(movie, context) {
    const tmdbSearchResults = document.getElementById('tmdb-search-results');
    const posterUrl = movie.poster_url || 'https://via.placeholder.com/300x450';
    const year = movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A';

    let actionButton = '';
    if (context === 'admin') {
        actionButton = `
            <button type="button" class="btn btn-primary" id="save-tmdb-movie">
                <i class="fa fa-save"></i> Save Movie to Database
            </button>
        `;
    } else if (context === 'user') {
        actionButton = `
            <button type="button" class="btn btn-primary" id="request-tmdb-movie">
                <i class="fa fa-paper-plane"></i> Request This Movie
            </button>
        `;
    }

    let html = `
        <div class="card">
            <div class="card-header bg-success text-white">
                <strong><i class="fa fa-check-circle"></i> Movie Found</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <img src="${posterUrl}" alt="${movie.title}" class="img-fluid rounded shadow-sm">
                    </div>
                    <div class="col-md-9">
                        <h4>${movie.title} <span class="text-muted">(${year})</span></h4>
                        <p class="text-muted mb-3">${movie.genre_names.join(', ') || 'No genres'}</p>
                        <p>${movie.description || 'No description available'}</p>
                        <hr>
                        <p><strong>Runtime:</strong> ${movie.runtime || 'N/A'} minutes</p>
                        <p><strong>Language:</strong> ${movie.language ? movie.language.toUpperCase() : 'N/A'}</p>
                        <p><strong>Release Date:</strong> ${movie.release_date || 'N/A'}</p>
                        ${movie.trailer_link ? `<p><strong>Trailer:</strong> <a href="${movie.trailer_link}" target="_blank">Watch on YouTube</a></p>` : ''}
                        <hr>
                        ${actionButton}
                        <button type="button" class="btn btn-outline-secondary" id="search-again">
                            <i class="fa fa-search"></i> Search Again
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    tmdbSearchResults.innerHTML = html;

    // Add event listeners based on context
    if (context === 'admin') {
        document.getElementById('save-tmdb-movie').addEventListener('click', function () {
            saveMovieToDatabase(movie);
        });
    } else if (context === 'user') {
        document.getElementById('request-tmdb-movie').addEventListener('click', function () {
            requestMovie(movie);
        });
    }

    // Search again button
    document.getElementById('search-again').addEventListener('click', clearSearch);
}

// Clear search
function clearSearch() {
    const tmdbSearchResults = document.getElementById('tmdb-search-results');
    const tmdbSearchTitle = document.getElementById('tmdb_search_title');
    const tmdbIdInput = document.getElementById('tmdb_id');

    if (tmdbSearchResults) tmdbSearchResults.innerHTML = '';
    if (tmdbSearchTitle) tmdbSearchTitle.value = '';
    if (tmdbIdInput) tmdbIdInput.value = '';
}

// Get CSRF Token
function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;
}

// Request movie (for user context)
function requestMovie(movie) {
    const requestBtn = document.getElementById('request-tmdb-movie');
    requestBtn.disabled = true;
    requestBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';

    fetch('/movies/request', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCSRFToken()
        },
        body: JSON.stringify({
            movie_title: movie.title,
            tmdb_id: movie.tmdb_id
        })
    })
        .then(response => response.json())
        .then(data => {
            const tmdbSearchResults = document.getElementById('tmdb-search-results');

            if (data.success) {
                // Add the new request to the list if function exists
                if (typeof addRequestToList === 'function') {
                    addRequestToList(movie.title);
                }

                tmdbSearchResults.innerHTML = `
                    <div class="alert alert-success">
                        <h5><i class="fa fa-check-circle"></i> Request Submitted!</h5>
                        <p>Your request for "<strong>${movie.title}</strong>" has been submitted to the admin.</p>
                        <p>You will be notified once the movie is added to the catalog.</p>
                        <button type="button" class="btn btn-sm btn-primary" id="request-another">
                            <i class="fa fa-plus"></i> Request Another Movie
                        </button>
                    </div>
                `;

                document.getElementById('request-another').addEventListener('click', clearSearch);
            } else if (data.duplicate) {
                tmdbSearchResults.innerHTML = `
                    <div class="alert alert-warning">
                        <h5><i class="fa fa-exclamation-triangle"></i> Already Requested</h5>
                        <p>You have already requested this movie. Please wait for admin approval.</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="search-again-dup">
                            <i class="fa fa-search"></i> Search Another Movie
                        </button>
                    </div>
                `;

                document.getElementById('search-again-dup').addEventListener('click', clearSearch);
            } else {
                alert(data.error || 'Failed to submit request');
                requestBtn.disabled = false;
                requestBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Request This Movie';
            }
        })
        .catch(error => {
            alert('Error submitting request. Please try again.');
            console.error('Error:', error);
            requestBtn.disabled = false;
            requestBtn.innerHTML = '<i class="fa fa-paper-plane"></i> Request This Movie';
        });
}

// Save movie to database (for admin context)
function saveMovieToDatabase(movie) {
    console.log('Saving movie:', movie.title);

    // Create a new form element
    const newForm = document.createElement('form');
    newForm.method = 'POST';
    newForm.action = '/admin/movies';

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = getCSRFToken();
    newForm.appendChild(csrfInput);

    // Helper function to add hidden input
    function addInput(name, value) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value || '';
        newForm.appendChild(input);
    }

    // Add all movie data
    addInput('input_method', 'tmdb');
    addInput('tmdb_id', movie.tmdb_id);
    addInput('title', movie.title);
    addInput('description', movie.description || '');
    addInput('release_date', movie.release_date || '');
    addInput('runtime', movie.runtime || '');
    addInput('language', movie.language || '');
    addInput('poster_url', movie.poster_url || '');
    addInput('trailer_link', movie.trailer_link || '');

    // Add genres
    if (movie.genres && movie.genres.length > 0) {
        movie.genres.forEach(genreId => {
            addInput('genres[]', genreId);
        });
    }

    // Append to body and submit
    document.body.appendChild(newForm);
    newForm.submit();
}
