@extends('layouts.app')

@section('content')
<main class="py-5">
  <div class="container-fluid px-4">
    <!-- Success Message -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-title">
            <div class="d-flex align-items-center">
              <h2 class="mb-0">Manage Movies</h2>
              <div class="ml-auto">
                <a href="{{ route('admin.movies.create') }}" class="btn btn-success">
                  <i class="fa fa-plus-circle"></i> Add New Movie
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            @if($movies->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Poster</th>
                      <th scope="col">Title</th>
                      <th scope="col">TMDB ID</th>
                      <th scope="col">Year</th>
                      <th scope="col">Genres</th>
                      <th scope="col" class="text-center">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($movies as $index => $movie)
                      <tr>
                        <th scope="row">{{ $movies->firstItem() + $index }}</th>
                        <td>
                          <img src="{{ $movie->poster_url ?? 'https://via.placeholder.com/50x75?text=No+Image' }}" 
                               alt="{{ $movie->title }}" 
                               class="admin-movie-poster">
                        </td>
                        <td>
                          <span class="admin-movie-title">{{ Str::limit($movie->title, 30) }}</span>
                        </td>
                        <td>
                          @if($movie->tmdb_id > 0)
                            <span class="badge badge-tmdb">{{ $movie->tmdb_id }}</span>
                          @else
                            <span class="badge badge-manual">Manual</span>
                          @endif
                        </td>
                        <td>{{ $movie->release_date ? $movie->release_date->format('Y') : 'N/A' }}</td>
                        <td>
                          <span class="admin-movie-genres">{{ $movie->genres->pluck('name')->join(', ') ?: 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                          <a href="{{ route('movies.details', $movie->id) }}" 
                             class="btn btn-sm btn-outline-info mr-1">
                            <i class="fa fa-eye"></i> Details
                          </a>
                          <a href="{{ route('admin.movies.edit', $movie->id) }}" 
                             class="btn btn-sm btn-outline-secondary mr-1">
                            <i class="fa fa-edit"></i> Edit
                          </a>
                          <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Are you sure you want to delete \'{{ $movie->title }}\'? This action cannot be undone.')">
                              <i class="fa fa-trash"></i> Delete
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              <div class="d-flex justify-content-center mt-4">
                {{ $movies->links() }}
              </div>
            @else
              <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> No movies found. Start by adding your first movie!
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
@endsection
