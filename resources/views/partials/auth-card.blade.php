{{-- Auth Card Layout Partial --}}
{{-- Usage: Wrap form content with this layout --}}

@php
    $width = $width ?? 'col-md-5';
    $icon = $icon ?? 'fa-user';
    $title = $title ?? 'Authentication';
    $alert = $alert ?? null;
@endphp

<div class="row justify-content-md-center">
  <div class="{{ $width }}">
    <div class="card">
      <div class="card-header card-title text-center">
        <h4 class="mb-0">
          <i class="fa {{ $icon }}"></i> {{ $title }}
        </h4>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if($alert)
          <div class="alert alert-{{ $alert['type'] }}">
            <i class="fa fa-{{ $alert['icon'] ?? 'info-circle' }}"></i> {{ $alert['message'] }}
          </div>
        @endif

        {{ $slot }}
      </div>
    </div>
  </div>
</div>
