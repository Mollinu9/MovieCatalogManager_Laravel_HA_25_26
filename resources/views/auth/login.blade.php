@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
          <div class="col-md-5">
            <div class="card">
              <div class="card-header card-title text-center">
                <h4 class="mb-0"><i class="fa fa-sign-in"></i> Login to Your Account</h4>
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

                <form action="{{ route('auth.login.submit') }}" method="POST">
                  @csrf
                  
                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter your email" value="{{ old('email') }}" required>
                    @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Enter your password" required>
                    @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa fa-sign-in"></i> Login
                  </button>

                  <hr>

                  <div class="text-center">
                    <p class="mb-0">Don't have an account? <a href="{{ route('auth.register') }}" class="text-primary">Register here</a></p>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
@endsection