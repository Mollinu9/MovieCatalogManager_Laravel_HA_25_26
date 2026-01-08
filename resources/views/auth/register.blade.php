@extends('layouts.app')

@section('content')
@component('partials.auth-card', [
  'title' => 'Create Your Account',
  'icon' => 'fa-user-plus',
  'width' => 'col-md-6'
])
  <form action="{{ route('auth.register.submit') }}" method="POST">
    @csrf
    
    <div class="form-group">
      <label for="name">Full Name</label>
      <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter your full name" value="{{ old('name') }}" required>
      @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="email">Email Address</label>
      <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" value="{{ old('email') }}" required>
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Create a password" required>
      <small class="form-text text-muted">Must be at least 8 characters</small>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="password_confirmation">Confirm Password</label>
      <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm your password" required>
    </div>

    <button type="submit" class="btn btn-success btn-block">
      <i class="fa fa-user-plus"></i> Register
    </button>

    <hr>

    <div class="text-center">
      <p class="mb-0">Already have an account? <a href="{{ route('auth.login') }}" class="text-primary">Login here</a></p>
    </div>
  </form>
@endcomponent
@endsection