// Toggle genre badge selection
document.addEventListener('DOMContentLoaded', function () {
    // Genre badge toggle
    document.querySelectorAll('label.genre-badge').forEach(function (label) {
        label.addEventListener('click', function (e) {
            e.preventDefault();
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;

            // Toggle selected class
            if (checkbox.checked) {
                this.classList.add('selected');
            } else {
                this.classList.remove('selected');
            }
        });
    });

    // Input method toggle (TMDB vs Manual)
    const tmdbOption = document.getElementById('tmdb-option');
    const manualOption = document.getElementById('manual-option');
    const tmdbSection = document.getElementById('tmdb-section');
    const manualSection = document.getElementById('manual-section');

    if (tmdbOption && manualOption && tmdbSection && manualSection) {
        tmdbOption.addEventListener('click', function () {
            // Add selected class to TMDB, remove from Manual
            this.classList.add('selected');
            manualOption.classList.remove('selected');

            // Show TMDB section, hide manual section
            tmdbSection.style.display = 'block';
            manualSection.style.display = 'none';
        });

        manualOption.addEventListener('click', function () {
            // Add selected class to Manual, remove from TMDB
            this.classList.add('selected');
            tmdbOption.classList.remove('selected');

            // Hide TMDB section, show manual section
            tmdbSection.style.display = 'none';
            manualSection.style.display = 'block';
        });
    }

    // TMDB Search Method Toggle (Title vs ID)
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

    // TMDB Search by Title
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ query: query })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.results.length > 0) {
                        displaySearchResults(data.results);
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

    // TMDB Fetch by ID
    const fetchTmdbBtn = document.getElementById('fetch-tmdb-btn');
    const tmdbIdInput = document.getElementById('tmdb_id');

    if (fetchTmdbBtn && tmdbIdInput && tmdbSearchResults) {
        fetchTmdbBtn.addEventListener('click', function () {
            const tmdbId = tmdbIdInput.value.trim();
            if (!tmdbId) {
                alert('Please enter a TMDB ID');
                return;
            }

            fetchMovieDetails(tmdbId);
        });

        // Allow Enter key to trigger fetch
        tmdbIdInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchTmdbBtn.click();
            }
        });
    }

    // Display search results
    function displaySearchResults(results) {
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
                fetchMovieDetails(tmdbId);
            });
        });
    }

    // Fetch movie details from TMDB
    function fetchMovieDetails(tmdbId) {
        tmdbSearchResults.innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Fetching movie details...</div>';

        fetch('/admin/tmdb/fetch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({ tmdb_id: tmdbId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMovieDetails(data.movie);
                } else if (data.existing) {
                    // Movie already exists in database
                    tmdbSearchResults.innerHTML = `
                    <div class="alert alert-warning">
                        <h5><i class="fa fa-exclamation-triangle"></i> Movie Already Exists</h5>
                        <p>The movie "<strong>${data.movie_title}</strong>" is already in your database.</p>
                        <a href="/movies/${data.movie_id}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa fa-eye"></i> View Movie
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="search-again-duplicate">
                            <i class="fa fa-search"></i> Search Another Movie
                        </button>
                    </div>
                `;

                    // Add event listener for search again button
                    document.getElementById('search-again-duplicate').addEventListener('click', function () {
                        tmdbSearchResults.innerHTML = '';
                        tmdbSearchTitle.value = '';
                        tmdbIdInput.value = '';
                    });
                } else {
                    tmdbSearchResults.innerHTML = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> ' + (data.error || 'Failed to fetch movie details.') + '</div>';
                }
            })
            .catch(error => {
                tmdbSearchResults.innerHTML = '<div class="alert alert-danger"><i class="fa fa-times-circle"></i> Error fetching movie details. Please try again.</div>';
                console.error('Error:', error);
            });
    }

    // Display movie details with preview and save option
    function displayMovieDetails(movie) {
        const posterUrl = movie.poster_url || 'https://via.placeholder.com/300x450';
        const year = movie.release_date ? new Date(movie.release_date).getFullYear() : 'N/A';

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
                            <button type="button" class="btn btn-primary" id="save-tmdb-movie">
                                <i class="fa fa-save"></i> Save Movie to Database
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="search-again">
                                <i class="fa fa-search"></i> Search Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        tmdbSearchResults.innerHTML = html;

        // Save movie button
        document.getElementById('save-tmdb-movie').addEventListener('click', function () {
            saveMovieToDatabase(movie);
        });

        // Search again button
        document.getElementById('search-again').addEventListener('click', function () {
            tmdbSearchResults.innerHTML = '';
            tmdbSearchTitle.value = '';
            tmdbIdInput.value = '';
        });
    }

    // Save movie to database
    function saveMovieToDatabase(movie) {
        // Populate form with movie data
        const form = document.querySelector('form');
        const formData = new FormData(form);

        // Set all the fields
        formData.set('input_method', 'tmdb');
        formData.set('tmdb_id', movie.tmdb_id);
        formData.set('title', movie.title);
        formData.set('description', movie.description || '');
        formData.set('release_date', movie.release_date || '');
        formData.set('runtime', movie.runtime || '');
        formData.set('language', movie.language || '');
        formData.set('poster_url', movie.poster_url || '');
        formData.set('trailer_link', movie.trailer_link || '');

        // Add genres
        movie.genres.forEach(genreId => {
            formData.append('genres[]', genreId);
        });

        // Submit form
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .catch(error => {
                alert('Error saving movie. Please try again.');
                console.error('Error:', error);
            });
    }
});

// Movie Preview functionality
document.addEventListener('DOMContentLoaded', function () {
    const previewBtn = document.getElementById('preview-btn');
    const saveFromPreviewBtn = document.getElementById('save-from-preview');
    const movieForm = document.querySelector('form');

    if (previewBtn) {
        previewBtn.addEventListener('click', function () {
            // Get form values
            const title = document.getElementById('title').value || 'Untitled Movie';
            const description = document.getElementById('description').value || 'No description provided.';
            const releaseDate = document.getElementById('release_date').value;
            const runtime = document.getElementById('runtime').value;
            const language = document.getElementById('language').value || 'N/A';
            const posterUrl = document.getElementById('poster_url').value || 'https://via.placeholder.com/300x450';

            // Get selected genres
            const selectedGenres = [];
            document.querySelectorAll('input[name="genres[]"]:checked').forEach(function (checkbox) {
                const label = checkbox.parentElement;
                selectedGenres.push(label.textContent.trim());
            });

            // Extract year from release date
            const year = releaseDate ? new Date(releaseDate).getFullYear() : 'N/A';
            const genresText = selectedGenres.length > 0 ? selectedGenres.join(', ') : 'N/A';

            // Update preview modal
            document.getElementById('preview-title').textContent = title;
            document.getElementById('preview-meta').textContent = year + ' • ' + (selectedGenres[0] || 'Genre');
            document.getElementById('preview-description').textContent = description;
            document.getElementById('preview-runtime').textContent = runtime ? runtime + ' minutes' : 'N/A';
            document.getElementById('preview-language').textContent = language;
            document.getElementById('preview-genres').textContent = genresText;
            document.getElementById('preview-poster').src = posterUrl;

            // Show modal
            $('#previewModal').modal('show');
        });
    }

    // Save from preview button
    if (saveFromPreviewBtn && movieForm) {
        saveFromPreviewBtn.addEventListener('click', function () {
            $('#previewModal').modal('hide');
            movieForm.submit();
        });
    }
});
