<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title', 'Movie Catalog')</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Varela+Round">
    <!-- Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    @stack('styles')
  </head>
  
  <body>
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container">
        <a class="navbar-brand text-uppercase" href="{{ url('/') }}">            
            <strong>Movie</strong> Catalog
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-toggler" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
            
        <!-- /.navbar-header -->
        <div class="collapse navbar-collapse" id="navbar-toggler">
          @auth
          <ul class="navbar-nav">
            <li class="nav-item"><a href="#" class="nav-link">Movies</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Search</a></li>
            <li class="nav-item"><a href="#" class="nav-link">My Watchlist</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Request Movie</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Admin</a></li>
          </ul>
          @else
          <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a href="#" class="nav-link">Movies</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Search</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Login</a></li>
            <li class="nav-item"><a href="#" class="nav-link btn btn-primary text-white ml-2">Get Started</a></li>
          </ul>
          @endauth
        </div>
      </div>
    </nav>

    <!-- content -->
    <main class="py-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white py-4">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-12 text-center">
            <h5><strong>Movie</strong> Catalog</h5>
            <p class="text-muted">Your ultimate movie collection manager</p>
          </div>
        </div>
        <hr class="bg-secondary">
        <div class="text-center text-muted">
          <p class="mb-0">&copy; 2025 Movie Catalog J.M. All rights reserved.</p>
        </div>
      </div>
    </footer>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    @stack('scripts')
  </body>
</html>