// Toggle genre badge selection
document.addEventListener('DOMContentLoaded', function () {
    // Initialize TMDB search for admin context
    if (typeof initTmdbSearch === 'function') {
        initTmdbSearch('admin');
    }

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
        // Get the tmdb_id input
        const tmdbIdInput = document.getElementById('tmdb_id');
        const tmdbSearchTitleInput = document.getElementById('tmdb_search_title');

        tmdbOption.addEventListener('click', function () {
            // Check the radio button
            const radioBtn = this.querySelector('input[type="radio"]');
            if (radioBtn) radioBtn.checked = true;

            // Enable TMDB fields
            if (tmdbIdInput) tmdbIdInput.disabled = false;
            if (tmdbSearchTitleInput) tmdbSearchTitleInput.disabled = false;

            // Add selected class to TMDB, remove from Manual
            this.classList.add('selected');
            manualOption.classList.remove('selected');

            // Show TMDB section, hide manual section
            tmdbSection.style.display = 'block';
            manualSection.classList.add('d-none');
        });

        manualOption.addEventListener('click', function () {
            // Check the radio button
            const radioBtn = this.querySelector('input[type="radio"]');
            if (radioBtn) radioBtn.checked = true;

            // Disable and clear TMDB fields so they don't get submitted
            if (tmdbIdInput) {
                tmdbIdInput.disabled = true;
                tmdbIdInput.value = '';
            }
            if (tmdbSearchTitleInput) {
                tmdbSearchTitleInput.disabled = true;
                tmdbSearchTitleInput.value = '';
            }

            // Add selected class to Manual, remove from TMDB
            this.classList.add('selected');
            tmdbOption.classList.remove('selected');

            // Hide TMDB section, show manual section
            tmdbSection.style.display = 'none';
            manualSection.classList.remove('d-none');
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
