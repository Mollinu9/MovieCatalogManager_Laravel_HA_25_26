{{-- Alert Partial --}}
{{-- Usage: @include('partials.alert', ['type' => 'success', 'message' => 'Success!']) --}}

@php
    $type = $type ?? 'info';
    $dismissible = $dismissible ?? true;
    
    $icons = [
        'success' => 'fa-check-circle',
        'error' => 'fa-exclamation-circle',
        'danger' => 'fa-exclamation-triangle',
        'warning' => 'fa-exclamation-triangle',
        'info' => 'fa-info-circle'
    ];
    
    $icon = $icon ?? $icons[$type] ?? 'fa-info-circle';
    $alertType = $type === 'error' ? 'danger' : $type;
@endphp

<div class="alert alert-{{ $alertType }} @if($dismissible) alert-dismissible fade show @endif" role="alert">
  <i class="fa {{ $icon }}"></i> {{ $message }}
  
  @if($dismissible)
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  @endif
</div>
