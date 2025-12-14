@extends('layouts.app')

@section('content')
<!-- content -->
    <main class="py-5">
      <div class="container">
        <!-- Header -->
        <div class="row mb-4">
          <div class="col-md-12">
            <h2 class="mb-0">Browse Movies</h2>
          </div>
        </div>

        <!-- Trending Now -->
        <div class="mb-5">
          <h3 class="mb-4">Trending Now</h3>
          <div class="row">
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Dark Knight">
                <div class="card-body">
                  <h5 class="card-title">The Dark Knight</h5>
                  <p class="text-muted small mb-2">2008 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 9.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Inception">
                <div class="card-body">
                  <h5 class="card-title">Inception</h5>
                  <p class="text-muted small mb-2">2010 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.8/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Interstellar">
                <div class="card-body">
                  <h5 class="card-title">Interstellar</h5>
                  <p class="text-muted small mb-2">2014 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.6/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Matrix">
                <div class="card-body">
                  <h5 class="card-title">The Matrix</h5>
                  <p class="text-muted small mb-2">1999 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.7/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Pulp Fiction">
                <div class="card-body">
                  <h5 class="card-title">Pulp Fiction</h5>
                  <p class="text-muted small mb-2">1994 • Crime</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.9/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Fight Club">
                <div class="card-body">
                  <h5 class="card-title">Fight Club</h5>
                  <p class="text-muted small mb-2">1999 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.8/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action & Adventure -->
        <div class="mb-5">
          <h3 class="mb-4">Action & Adventure</h3>
          <div class="row">
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Mad Max">
                <div class="card-body">
                  <h5 class="card-title">Mad Max: Fury Road</h5>
                  <p class="text-muted small mb-2">2015 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.1/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="John Wick">
                <div class="card-body">
                  <h5 class="card-title">John Wick</h5>
                  <p class="text-muted small mb-2">2014 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 7.4/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Gladiator">
                <div class="card-body">
                  <h5 class="card-title">Gladiator</h5>
                  <p class="text-muted small mb-2">2000 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.5/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Die Hard">
                <div class="card-body">
                  <h5 class="card-title">Die Hard</h5>
                  <p class="text-muted small mb-2">1988 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.2/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Bourne Identity">
                <div class="card-body">
                  <h5 class="card-title">The Bourne Identity</h5>
                  <p class="text-muted small mb-2">2002 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 7.9/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Casino Royale">
                <div class="card-body">
                  <h5 class="card-title">Casino Royale</h5>
                  <p class="text-muted small mb-2">2006 • Action</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Award-Winning Dramas -->
        <div class="mb-5">
          <h3 class="mb-4">Award-Winning Dramas</h3>
          <div class="row">
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Shawshank Redemption">
                <div class="card-body">
                  <h5 class="card-title">The Shawshank Redemption</h5>
                  <p class="text-muted small mb-2">1994 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 9.3/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Forrest Gump">
                <div class="card-body">
                  <h5 class="card-title">Forrest Gump</h5>
                  <p class="text-muted small mb-2">1994 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.8/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Godfather">
                <div class="card-body">
                  <h5 class="card-title">The Godfather</h5>
                  <p class="text-muted small mb-2">1972 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 9.2/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Schindler's List">
                <div class="card-body">
                  <h5 class="card-title">Schindler's List</h5>
                  <p class="text-muted small mb-2">1993 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 9.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="12 Angry Men">
                <div class="card-body">
                  <h5 class="card-title">12 Angry Men</h5>
                  <p class="text-muted small mb-2">1957 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 9.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Green Mile">
                <div class="card-body">
                  <h5 class="card-title">The Green Mile</h5>
                  <p class="text-muted small mb-2">1999 • Drama</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.6/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sci-Fi & Fantasy -->
        <div class="mb-5">
          <h3 class="mb-4">Sci-Fi & Fantasy</h3>
          <div class="row">
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Blade Runner 2049">
                <div class="card-body">
                  <h5 class="card-title">Blade Runner 2049</h5>
                  <p class="text-muted small mb-2">2017 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Arrival">
                <div class="card-body">
                  <h5 class="card-title">Arrival</h5>
                  <p class="text-muted small mb-2">2016 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 7.9/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Ex Machina">
                <div class="card-body">
                  <h5 class="card-title">Ex Machina</h5>
                  <p class="text-muted small mb-2">2014 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 7.7/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Dune">
                <div class="card-body">
                  <h5 class="card-title">Dune</h5>
                  <p class="text-muted small mb-2">2021 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="The Martian">
                <div class="card-body">
                  <h5 class="card-title">The Martian</h5>
                  <p class="text-muted small mb-2">2015 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 8.0/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-sm-6 mb-4">
              <div class="card movie-grid-card h-100">
                <img src="https://via.placeholder.com/300x450" class="card-img-top" alt="Gravity">
                <div class="card-body">
                  <h5 class="card-title">Gravity</h5>
                  <p class="text-muted small mb-2">2013 • Sci-Fi</p>
                  <p class="mb-2"><i class="fa fa-star text-warning"></i> 7.7/10</p>
                  <a href="movie-detail.html" class="btn btn-sm btn-primary btn-block">View Details</a>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>
@endsection