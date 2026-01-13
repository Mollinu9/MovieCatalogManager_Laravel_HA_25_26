@extends('layouts.app')

@section('content')
<!-- Success/Error Messages -->
@if(session('success'))
  @include('partials.alert', [
    'type' => 'success',
    'message' => session('success')
  ])
@endif

@if(session('error'))
  @include('partials.alert', [
    'type' => 'error',
    'message' => session('error')
  ])
@endif

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-title">
        <div class="d-flex align-items-center justify-content-between">
          <h2 class="mb-0">Movie Requests</h2>
          <div>
            <span class="badge badge-warning">{{ $pendingCount }} Pending</span>
          </div>
        </div>
      </div>
      <div class="card-body">
        @if($requests->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Movie Title</th>
                  <th scope="col">TMDB ID</th>
                  <th scope="col">Requested By</th>
                  <th scope="col">Date</th>
                  <th scope="col" class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($requests as $index => $request)
                  <tr>
                    <th scope="row">{{ $requests->firstItem() + $index }}</th>
                    <td>
                      <strong>{{ $request->movie_title }}</strong>
                    </td>
                    <td>
                      <span class="badge badge-tmdb">{{ $request->tmdb_id }}</span>
                    </td>
                    <td>
                      {{ $request->user->name }}
                      <br>
                      <small class="text-muted">{{ $request->user->email }}</small>
                    </td>
                    <td>
                      {{ $request->created_at->format('M d, Y') }}
                      <br>
                      <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                    </td>
                    <td class="text-center">
                      <!-- Approve Button (adds movie and deletes request) -->
                      <form action="{{ route('admin.requests.approve', $request->id) }}" method="POST" class="d-inline-form">
                        @csrf
                        <button type="submit" 
                                class="btn btn-sm btn-success" 
                                title="Approve and add movie to database"
                                onclick="return confirm('Add this movie to the database?')">
                          <i class="fa fa-check"></i> Approve
                        </button>
                      </form>

                      <!-- Reject Button (deletes request) -->
                      <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST" class="d-inline-form">
                        @csrf
                        <button type="submit" 
                                class="btn btn-sm btn-danger" 
                                title="Reject and delete request"
                                onclick="return confirm('Reject and delete this request?')">
                          <i class="fa fa-times"></i> Reject
                        </button>
                      </form>

                      <!-- View on TMDB -->
                      <a href="https://www.themoviedb.org/movie/{{ $request->tmdb_id }}" 
                         target="_blank" 
                         class="btn btn-sm btn-outline-info"
                         title="View on TMDB">
                        <i class="fa fa-external-link"></i>
                      </a>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          @if($requests->hasPages())
            <div class="d-flex justify-content-center mt-4">
              {{ $requests->links('pagination::bootstrap-4') }}
            </div>
          @endif
        @else
          @include('partials.empty-state', [
            'icon' => 'fa-inbox',
            'title' => 'No movie requests yet',
            'description' => 'Users can request movies from the Request Movie page'
          ])
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
