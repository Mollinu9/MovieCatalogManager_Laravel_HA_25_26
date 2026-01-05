<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!--Security measure for CRUD -->

    <title>@yield('title', 'Movie Catalog')</title> <!--Show Constant Movie Catalog-->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Varela+Round">
    <!-- Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">
    @stack('styles')
  </head>
  
  <body>
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container">
        <a class="navbar-brand text-uppercase" href="{{ url('/') }}">            
            <strong>Movie Catalog </strong> J.M.
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-toggler" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
            
        <!-- /.navbar-header -->
        <div class="collapse navbar-collapse" id="navbar-toggler">
          <ul class="navbar-nav">
            @guest
              <li class="nav-item"><a href="{{ route('movies.search') }}" class="nav-link">Search</a></li>
            @else
              <li class="nav-item"><a href="{{ route('movies.index') }}" class="nav-link">Movies</a></li>
              <li class="nav-item"><a href="{{ route('movies.search') }}" class="nav-link">Search</a></li>
              <li class="nav-item"><a href="{{ route('movies.watchlist') }}" class="nav-link">My Watchlist</a></li>
              <li class="nav-item"><a href="{{ route('movies.request') }}" class="nav-link">Request Movie</a></li>
              @if(Auth::user()->is_admin)
                <li class="nav-item"><a href="{{ route('admin.movies.index') }}" class="nav-link">Admin</a></li>
              @endif
            @endguest
          </ul>
          <ul class="navbar-nav ml-auto">
            @auth
              <li class="nav-item">
                <span class="nav-link">Hi, {{ Auth::user()->name }}</span>
              </li>
              <li class="nav-item">
                <form action="{{ route('auth.logout') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="nav-link btn btn-link">Logout</button>
                </form>
              </li>
            @else
              <li class="nav-item"><a href="{{ route('auth.login') }}" class="nav-link">Login</a></li>
              <li class="nav-item"><a href="{{ route('auth.register') }}" class="nav-link btn btn-primary text-white ml-2">Register</a></li>
            @endauth
          </ul>
        </div>
      </div>
    </nav>

    <!-- Genre Filter Bar (only on movies page) -->
    @yield('genre-filter')

    <!-- Search Filter Bar (only on search page) -->
    @yield('search-filter')

    <!-- content -->
    <main class="py-5">
        <div class="container-fluid px-4">
            @yield('content')
        </div>
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