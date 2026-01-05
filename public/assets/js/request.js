document.addEventListener('DOMContentLoaded', function () {
    // Initialize TMDB search for user context
    if (typeof initTmdbSearch === 'function') {
        initTmdbSearch('user');
    }
});

// Add new request to the list (called from tmdb-search.js)
function addRequestToList(movieTitle) {
    const requestsList = document.getElementById('requests-list');
    const cardBody = document.querySelector('.col-lg-4 .card-body');

    // If the list doesn't exist (no requests yet), create it and remove empty state
    if (!requestsList) {
        cardBody.className = 'card-body p-0';
        cardBody.innerHTML = '<div class="list-group list-group-flush" id="requests-list"></div>';
    }

    const list = document.getElementById('requests-list');

    // Create new request item
    const newRequest = document.createElement('div');
    newRequest.className = 'list-group-item';
    newRequest.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1 pr-2">
                <h6 class="mb-1">${movieTitle}</h6>
                <small class="text-muted d-block">
                    <i class="fa fa-clock-o"></i> just now
                </small>
            </div>
            <div>
                <span class="badge badge-warning">Pending</span>
            </div>
        </div>
    `;

    // Add to the top of the list
    list.insertBefore(newRequest, list.firstChild);
}
