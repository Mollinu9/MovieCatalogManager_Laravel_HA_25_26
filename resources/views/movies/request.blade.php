@extends('layouts.app')

@section('content')
<div class="row">
  <!-- Left Side: Search Form -->
  <div class="col-lg-8 col-md-7">
    <div class="card mb-4">
      <div class="card-header card-title">
        <strong>Request a Movie</strong>
      </div>           
      <div class="card-body">
        @include('partials.alert', [
          'type' => 'info',
          'message' => "Can't find a movie in our catalog? Search for it below and request it to be added!"
        ])

        @include('partials.tmdb-search')
      </div>
    </div>
  </div>

  <!-- Right Side: My Requests -->
  <div class="col-lg-4 col-md-5">
    <div class="card sticky-top-20">
      <div class="card-header card-title">
        <strong><i class="fa fa-list"></i> My Requests</strong>
      </div>
      <div class="card-body p-0">
        @if($requests->isEmpty())
          <div class="text-center text-muted py-5 px-3">
            <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
            <p class="mb-0">You haven't requested any movies yet.</p>
          </div>
        @else
          <div class="list-group list-group-flush" id="requests-list">
            @foreach($requests as $request)
              <div class="list-group-item" data-request-id="{{ $request->id }}">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1 pr-2">
                    <h6 class="mb-1">{{ $request->movie_title }}</h6>
                    <small class="text-muted d-block">
                      <i class="fa fa-clock-o"></i> {{ $request->created_at->diffForHumans() }}
                    </small>
                  </div>
                  <div>
                    @if($request->status === 'pending')
                      <span class="badge badge-warning">Pending</span>
                    @elseif($request->status === 'approved')
                      <span class="badge badge-success">Approved</span>
                    @elseif($request->status === 'rejected')
                      <span class="badge badge-danger">Rejected</span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/tmdb-search.js') }}"></script>
<script src="{{ asset('assets/js/request.js') }}"></script>
@endpush
