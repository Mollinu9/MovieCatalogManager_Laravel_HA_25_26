// Toggle genre badge selection
document.addEventListener('DOMContentLoaded', function () {
    // Genre badge toggle
    document.querySelectorAll('label.genre-badge').forEach(function (label) {
        label.addEventListener('click', function (e) {
            e.preventDefault();
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;

            if (checkbox.checked) {
                this.classList.remove('btn-outline-warning');
                this.classList.add('btn-primary');
            } else {
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-warning');
            }
        });
    });

    // Input method toggle (TMDB vs Manual)
    const tmdbOption = document.getElementById('tmdb-option');
    const manualOption = document.getElementById('manual-option');
    const tmdbSection = document.getElementById('tmdb-section');
    const tmdbInput = document.getElementById('tmdb_id');

    if (tmdbOption && manualOption && tmdbSection) {
        tmdbOption.addEventListener('click', function () {
            tmdbSection.style.display = 'flex';
            tmdbInput.required = false;
        });

        manualOption.addEventListener('click', function () {
            tmdbSection.style.display = 'none';
            tmdbInput.required = false;
            tmdbInput.value = '';
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
