@extends('layouts.app')

@section('content')
    <!-- content -->
    <main class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
                <div class="card-header card-title">
                  <div class="d-flex align-items-center">
                    <h2 class="mb-0">My Watchlist</h2>
                  </div>
                </div>
              <div class="card-body">
                <!-- Filter by status -->
                <div class="row mb-3">
                  <div class="col-md-4">
                    <select class="custom-select">
                      <option value="" selected>All Movies</option>
                      <option value="watchlist">To Watch</option>
                      <option value="watched">Watched</option>
                    </select>
                  </div>
                </div>

                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Poster</th>
                      <th scope="col">Title</th>
                      <th scope="col">Year</th>
                      <th scope="col">Genre</th>
                      <th scope="col">Status</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><img src="https://via.placeholder.com/50x75" alt="Poster" class="img-thumbnail"></td>
                      <td>The Dark Knight</td>
                      <td>2008</td>
                      <td>Action, Drama</td>
                      <td><span class="badge badge-success">Watched</span></td>
                      <td width="200">
                        <a href="movie-detail.html" class="btn btn-sm btn-circle btn-outline-info" title="View"><i class="fa fa-eye"></i></a>
                        <button class="btn btn-sm btn-circle btn-outline-warning" title="Mark as To Watch"><i class="fa fa-undo"></i></button>
                        <button class="btn btn-sm btn-circle btn-outline-danger" title="Remove" onclick="confirm('Remove from watchlist?')"><i class="fa fa-times"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td><img src="https://via.placeholder.com/50x75" alt="Poster" class="img-thumbnail"></td>
                      <td>Inception</td>
                      <td>2010</td>
                      <td>Sci-Fi, Thriller</td>
                      <td><span class="badge badge-primary">To Watch</span></td>
                      <td>
                        <a href="movie-detail.html" class="btn btn-sm btn-circle btn-outline-info" title="View"><i class="fa fa-eye"></i></a>
                        <button class="btn btn-sm btn-circle btn-outline-success" title="Mark as Watched"><i class="fa fa-check"></i></button>
                        <button class="btn btn-sm btn-circle btn-outline-danger" title="Remove" onclick="confirm('Remove from watchlist?')"><i class="fa fa-times"></i></button>
                      </td>
                    </tr>
                    <tr>
                      <td><img src="https://via.placeholder.com/50x75" alt="Poster" class="img-thumbnail"></td>
                      <td>Pulp Fiction</td>
                      <td>1994</td>
                      <td>Crime, Drama</td>
                      <td><span class="badge badge-primary">To Watch</span></td>
                      <td>
                        <a href="movie-detail.html" class="btn btn-sm btn-circle btn-outline-info" title="View"><i class="fa fa-eye"></i></a>
                        <button class="btn btn-sm btn-circle btn-outline-success" title="Mark as Watched"><i class="fa fa-check"></i></button>
                        <button class="btn btn-sm btn-circle btn-outline-danger" title="Remove" onclick="confirm('Remove from watchlist?')"><i class="fa fa-times"></i></button>
                      </td>
                    </tr>
                  </tbody>
                </table> 

              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
@endsection