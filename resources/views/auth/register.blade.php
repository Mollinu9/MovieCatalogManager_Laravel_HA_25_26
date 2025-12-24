@extends('layouts.app')

@section('content')
<div class="row justify-content-md-center">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header card-title text-center">
                <h4 class="mb-0"><i class="fa fa-user-plus"></i> Create Your Account</h4>
              </div>           
              <div class="card-body">
                <form>
                  <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter your full name" required>
                  </div>

                  <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required>
                  </div>

                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Create a password" required>
                    <small class="form-text text-muted">Must be at least 8 characters</small>
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
              </div>
            </div>
          </div>
        </div>
@endsection