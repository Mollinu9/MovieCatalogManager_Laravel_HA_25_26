@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card watchlist-card">
            <div class="card-header card-title">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0"><i class="fa fa-heart mr-2"></i>My Watchlist</h2>
                    <span class="badge badge-light">{{ $movies->count() }} Movies</span>
                </div>
            </div>
            <div class="card-body">
                @if($movies->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa fa-film" style="font-size: 80px; color: #dee2e6;"></i>
                        <h4 class="mt-4 text-muted">Your watchlist is empty</h4>
                        <p class="text-muted">Start adding movies to your watchlist!</p>
                        <a href="{{ route('movies.index') }}" class="btn btn-primary mt-3">
                            <i class="fa fa-search mr-2"></i>Browse Movies
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover watchlist-table">
                            <thead>
                                <tr>
                                    <th scope="col" width="120">Poster</th>
                                    <th scope="col">Title</th>
                                    <th scope="col" width="100">Year</th>
                                    <th scope="col" width="200">Genres</th>
                                    <th scope="col" width="100">Runtime</th>
                                    <th scope="col" width="120">Status</th>
                                    <th scope="col" width="200" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movies as $movie)
                                <tr>
                                    <td>
                                        @if($movie->poster_url)
                                            <img src="{{ $movie->poster_url }}" 
                                                 alt="{{ $movie->title }}" 
                                                 class="watchlist-poster">
                                        @else
                                            <div class="watchlist-poster-placeholder">
                                                <i class="fa fa-film"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="watchlist-movie-title">{{ $movie->title }}</strong>
                                    </td>
                                    <td>
                                        @if($movie->release_date)
                                            {{ \Carbon\Carbon::parse($movie->release_date)->format('Y') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movie->genres->isNotEmpty())
                                            <span class="watchlist-genres">
                                                {{ $movie->genres->pluck('name')->join(', ') }}
                                            </span>
                                        @else
                                            <span class="text-muted">No genres</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($movie->runtime)
                                            {{ $movie->runtime }} min
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-primary status-badge">To Watch</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('movies.details', $movie->id) }}" 
                                           class="btn btn-sm btn-circle btn-outline-info" 
                                           title="View Details">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-circle btn-outline-success" 
                                                title="Mark as Watched">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-circle btn-outline-danger" 
                                                title="Remove from Watchlist">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
