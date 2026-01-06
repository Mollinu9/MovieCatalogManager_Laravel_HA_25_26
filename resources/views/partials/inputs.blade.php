{{-- Unified Form Input Partial --}}
{{-- Usage: @include('partials.form.input', ['type' => 'text', 'name' => 'title', 'label' => 'Title', 'value' => $movie->title]) --}}

@php
    $type = $type ?? 'text';
    $layout = $layout ?? 'horizontal';
    $required = $required ?? false;
    $value = $value ?? old($name);
    $placeholder = $placeholder ?? '';
    $help = $help ?? null;
    $readonly = $readonly ?? false;
    $rows = $rows ?? 4;
    $min = $min ?? null;
    $max = $max ?? null;
    $showPreview = $showPreview ?? false;
    $options = $options ?? [];
    
    if ($type === 'date' && $value instanceof \Carbon\Carbon) {
        $value = $value->format('Y-m-d');
    }
@endphp

<div class="form-group @if($layout === 'horizontal') row @endif">
  <label for="{{ $name }}" class="@if($layout === 'horizontal') col-md-3 col-form-label @endif">
    {{ $label }}
    @if($required)
      <span class="text-danger">*</span>
    @endif
  </label>
  
  <div class="@if($layout === 'horizontal') col-md-9 @endif">
    @switch($type)
      @case('textarea')
        <textarea 
          name="{{ $name }}" 
          id="{{ $name }}" 
          rows="{{ $rows }}"
          class="form-control @error($name) is-invalid @enderror" 
          placeholder="{{ $placeholder }}"
          @if($required) required @endif
        >{{ $value }}</textarea>
        @break
      
      @case('select')
        <select 
          name="{{ $name }}" 
          id="{{ $name }}" 
          class="custom-select @error($name) is-invalid @enderror"
          @if($required) required @endif
        >
          <option value="">
            @if($placeholder)
              {{ $placeholder }}
            @else
              Select an option
            @endif
          </option>
          @foreach($options as $key => $label)
            <option value="{{ $key }}" @if($value == $key) selected @endif>
              {{ $label }}
            </option>
          @endforeach
        </select>
        @break
      
      @default
        <input 
          type="{{ $type }}" 
          name="{{ $name }}" 
          id="{{ $name }}" 
          class="form-control @error($name) is-invalid @enderror" 
          value="{{ $value }}" 
          placeholder="{{ $placeholder }}"
          @if($min) min="{{ $min }}" @endif
          @if($max) max="{{ $max }}" @endif
          @if($required) required @endif
          @if($readonly) readonly @endif
        >
    @endswitch
    
    @if($help)
      <small class="form-text text-muted">{{ $help }}</small>
    @endif
    
    @if($showPreview && $value)
      <div class="mt-2">
        <img src="{{ $value }}" alt="Preview" style="max-width: 150px; border-radius: 8px;">
      </div>
    @endif
    
    @error($name)
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>
