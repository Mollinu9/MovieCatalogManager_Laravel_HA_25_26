@extends('layouts.app')

@section('content')
@component('partials.auth-card', [
  'title' => 'Reset Your Password',
  'icon' => 'fa-key',
  'width' => 'col-md-5',
  'alert' => [
    'type' => 'info',
    'message' => 'Enter your email and new password to reset your account password.'
  ]
])
  <form action="{{ route('auth.reset') }}" method="POST">
    @csrf
    
    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" 
             name="email" 
             id="email" 
             class="form-control @error('email') is-invalid @enderror" 
             placeholder="Enter your registered email" 
             value="{{ old('email') }}" 
             required>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <small class="form-text text-muted">Enter the email address associated with your account</small>
    </div>

    <div class="form-group">
      <label for="password">New Password</label>
      <input type="password" 
             name="password" 
             id="password" 
             class="form-control @error('password') is-invalid @enderror" 
             placeholder="Enter new password" 
             required>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
      <small class="form-text text-muted">Must be at least 8 characters</small>
    </div>

    <div class="form-group">
      <label for="password_confirmation">Confirm New Password</label>
      <input type="password" 
             name="password_confirmation" 
             id="password_confirmation" 
             class="form-control" 
             placeholder="Confirm new password" 
             required>
    </div>

    <button type="submit" class="btn btn-primary btn-block">
      <i class="fa fa-check"></i> Reset Password
    </button>

    <hr>

    <div class="text-center">
      <p class="mb-0">
        <a href="{{ route('auth.login') }}" class="text-muted">
          <i class="fa fa-arrow-left"></i> Back to Login
        </a>
      </p>
    </div>
  </form>
@endcomponent
@endsection