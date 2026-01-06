{{-- Empty State Partial --}}
{{-- Usage: @include('partials.empty-state', ['icon' => 'fa-film', 'title' => 'No movies found']) --}}

@php
    $icon = $icon ?? 'fa-inbox';
    $title = $title ?? 'No items found';
    $description = $description ?? null;
    $actionText = $actionText ?? null;
    $actionRoute = $actionRoute ?? null;
    $iconSize = $iconSize ?? '80px';
@endphp

<div class="text-center py-5">
  <i class="fa {{ $icon }}" style="font-size: {{ $iconSize }}; color: #dee2e6;"></i>
  <h4 class="mt-4 text-muted">{{ $title }}</h4>
  
  @if($description)
    <p class="text-muted">{{ $description }}</p>
  @endif
  
  @if($actionText && $actionRoute)
    <a href="{{ route($actionRoute) }}" class="btn btn-primary mt-3">
      <i class="fa fa-plus mr-2"></i>{{ $actionText }}
    </a>
  @endif
</div>
