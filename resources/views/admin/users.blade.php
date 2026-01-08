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
    'type' => 'danger',
    'message' => session('error')
  ])
@endif

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-title">
        <div class="d-flex align-items-center">
          <h2 class="mb-0">Manage Users</h2>
          <div class="ml-auto">
            <span class="badge badge-info">{{ $users->total() }} Total Users</span>
          </div>
        </div>
      </div>
      <div class="card-body">
        @if($users->count() > 0)
          <div class="row">
            @foreach($users as $user)
              <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2 mb-3">
                <div class="card h-100 shadow-sm">
                  <div class="card-body d-flex flex-column p-3">
                    <div class="text-center mb-3">
                      <i class="fa fa-user-circle fa-4x text-primary"></i>
                    </div>
                    
                    <h6 class="card-title mb-2 text-center">{{ $user->name }}</h6>
                    
                    <div class="mb-2">
                      <small class="text-muted d-block"><strong>Email:</strong></small>
                      <small class="text-muted">{{ $user->email }}</small>
                    </div>
                    
                    <div class="mb-2">
                      <small class="text-muted d-block"><strong>Role:</strong></small>
                      @if($user->is_admin)
                        <span class="badge badge-success">Admin</span>
                      @else
                        <span class="badge badge-secondary">User</span>
                      @endif
                    </div>
                    
                    <div class="mb-2">
                      <small class="text-muted d-block"><strong>Joined:</strong></small>
                      <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                    </div>
                    
                    <div class="mb-2">
                      <small class="text-muted d-block"><strong>Watchlist:</strong></small>
                      <small class="text-muted">{{ $user->watchlistMovies->count() }} movies</small>
                    </div>
                    
                    @if($user->id !== Auth::id())
                      <div class="mt-auto">
                        <form action="{{ route('admin.users.toggleAdmin', $user->id) }}" method="POST" class="mb-2">
                          @csrf
                          @if($user->is_admin)
                            <button type="submit" class="btn btn-sm btn-warning w-100" onclick="return confirm('Remove admin privileges from {{ $user->name }}?')">
                              <i class="fa fa-user-times"></i> Revoke Admin
                            </button>
                          @else
                            <button type="submit" class="btn btn-sm btn-success w-100" onclick="return confirm('Grant admin privileges to {{ $user->name }}?')">
                              <i class="fa fa-user-plus"></i> Make Admin
                            </button>
                          @endif
                        </form>
                        
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete {{ $user->name }}? This will also delete their watchlist and requests. This action cannot be undone.')">
                            <i class="fa fa-trash"></i> Delete User
                          </button>
                        </form>
                      </div>
                    @else
                      <div class="mt-auto">
                        <div class="alert alert-info mb-0 p-2">
                          <small><i class="fa fa-info-circle"></i> This is you</small>
                        </div>
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-4">
            {{ $users->links('pagination::bootstrap-4') }}
          </div>
        @else
          @include('partials.empty-state', [
            'icon' => 'fa-users',
            'title' => 'No users found',
            'description' => 'There are no users in the system.'
          ])
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
