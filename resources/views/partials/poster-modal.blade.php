{{-- Poster Modal Partial --}}
{{-- Usage: @include('partials.poster-modal') - Include once per page --}}

<div id="posterModal" class="poster-modal" onclick="closePosterModal()">
  <span class="poster-modal-close">&times;</span>
  <img class="poster-modal-content" id="posterModalImage" alt="Movie Poster">
  <div id="posterModalCaption"></div>
</div>

@push('scripts')
<script>
function openPosterModal(url, title) {
  const modal = document.getElementById('posterModal');
  const modalImg = document.getElementById('posterModalImage');
  const caption = document.getElementById('posterModalCaption');
  
  modal.style.display = 'block';
  modalImg.src = url;
  caption.textContent = title;
  document.body.style.overflow = 'hidden';
}

function closePosterModal() {
  const modal = document.getElementById('posterModal');
  modal.style.display = 'none';
  document.body.style.overflow = 'auto';
}

document.addEventListener('keydown', function(event) {
  if (event.key === 'Escape') {
    closePosterModal();
  }
});
</script>
@endpush
