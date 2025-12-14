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
