@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Profile</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-4">
                        <h4>Profile Information</h4>
                        <p><strong>Name:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                        <p><strong>Joined:</strong> {{ $user->created_at->format('F j, Y') }}</p>
                        
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    </div>
                    
                    @if ($user->role === 'customer')
                        <div class="mt-5">
                            <h4>Order History</h4>
                            @if ($user->orders && $user->orders->count() > 0)
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Restaurant</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->orders as $order)
                                            <tr>
                                                <td>#{{ $order->id }}</td>
                                                <td>{{ $order->restaurant->name }}</td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                                <td>{{ ucfirst($order->status) }}</td>
                                                <td>
                                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>You haven't placed any orders yet.</p>
                            @endif
                        </div>
                    @elseif ($user->role === 'restaurant')
                        <div class="mt-5">
                            <h4>Your Restaurant</h4>
                            @if ($user->restaurant)
                                <div class="card mb-3">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="{{ asset('storage/' . $user->restaurant->cover_image) }}" class="img-fluid rounded-start" alt="{{ $user->restaurant->name }}">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $user->restaurant->name }}</h5>
                                                <p class="card-text">{{ $user->restaurant->description }}</p>
                                                <p class="card-text"><small class="text-muted">{{ $user->restaurant->address }}, {{ $user->restaurant->city }}, {{ $user->restaurant->state }} {{ $user->restaurant->zip_code }}</small></p>
                                                <a href="{{ route('restaurants.show', $user->restaurant->id) }}" class="btn btn-primary">View Restaurant</a>
                                                <a href="{{ route('restaurants.edit', $user->restaurant->id) }}" class="btn btn-secondary">Edit Restaurant</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p>You don't have a restaurant set up yet.</p>
                                <a href="{{ route('restaurants.create') }}" class="btn btn-primary">Create Restaurant</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection