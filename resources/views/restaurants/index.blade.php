@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Restaurants</h1>
            <p class="text-muted">Discover the best restaurants in your area</p>
        </div>
        <div class="col-md-4 text-md-end">
            @auth
                @if(auth()->user()->isRestaurant() || auth()->user()->isAdmin())
                    <a href="{{ route('restaurants.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Restaurant
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('restaurants.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search restaurants..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="sort" class="form-select">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Restaurants List -->
    <div class="row">
        @forelse($restaurants as $restaurant)
            <div class="col-md-4 mb-4">
                <div class="card h-100 restaurant-card">
                    <div class="position-relative">
                        @if($restaurant->cover_image)
                            <img src="{{ asset('storage/' . $restaurant->cover_image) }}" class="card-img-top" alt="{{ $restaurant->name }}" style="height: 180px; object-fit: cover;">
                        @else
                            <div class="bg-light text-center py-5">
                                <i class="fas fa-utensils fa-3x text-muted"></i>
                            </div>
                        @endif
                        @if($restaurant->logo)
                            <div class="position-absolute" style="bottom: -30px; left: 20px;">
                                <img src="{{ asset('storage/' . $restaurant->logo) }}" class="rounded-circle border border-white" alt="{{ $restaurant->name }}" style="width: 60px; height: 60px; object-fit: cover; background-color: white;">
                            </div>
                        @endif
                    </div>
                    <div class="card-body pt-4">
                        <h5 class="card-title mt-2">{{ $restaurant->name }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($restaurant->description, 100) }}</p>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                            <span class="text-muted small">{{ $restaurant->city }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                @if($restaurant->opening_time && $restaurant->closing_time)
                                    <span class="badge bg-light text-dark">
                                        {{ \Carbon\Carbon::parse($restaurant->opening_time)->format('g:i A') }} - 
                                        {{ \Carbon\Carbon::parse($restaurant->closing_time)->format('g:i A') }}
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-sm btn-outline-primary">View Menu</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No restaurants found. 
                    @auth
                        @if(auth()->user()->isRestaurant() || auth()->user()->isAdmin())
                            <a href="{{ route('restaurants.create') }}">Add your restaurant</a> to get started.
                        @endif
                    @endauth
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $restaurants->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .restaurant-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .restaurant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush