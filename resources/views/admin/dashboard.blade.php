@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Admin Dashboard</h1>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <h2 class="mb-0">{{ $totalUsers }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.users') }}" class="text-white text-decoration-none">View Details</a>
                    <i class="fas fa-arrow-right text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Restaurants</h5>
                            <h2 class="mb-0">{{ $totalRestaurants }}</h2>
                        </div>
                        <i class="fas fa-utensils fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.restaurants') }}" class="text-white text-decoration-none">View Details</a>
                    <i class="fas fa-arrow-right text-white"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Orders</h5>
                            <h2 class="mb-0">{{ $totalOrders }}</h2>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.orders') }}" class="text-white text-decoration-none">View Details</a>
                    <i class="fas fa-arrow-right text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Restaurant</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>{{ $order->order_number }}</td>
                                        <td>{{ $order->user->name }}</td>
                                        <td>{{ $order->restaurant->name }}</td>
                                        <td>৳{{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No orders found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-primary">View All Orders</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Statistics</h5>
                </div>
                <div class="card-body">
                    <canvas id="orderStatsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('orderStatsChart').getContext('2d');
        
        const orderStats = {
            pending: {{ $orderStats['pending'] }},
            confirmed: {{ $orderStats['confirmed'] }},
            preparing: {{ $orderStats['preparing'] }},
            out_for_delivery: {{ $orderStats['out_for_delivery'] }},
            delivered: {{ $orderStats['delivered'] }},
            cancelled: {{ $orderStats['cancelled'] }}
        };
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'Pending', 
                    'Confirmed', 
                    'Preparing', 
                    'Out for Delivery', 
                    'Delivered', 
                    'Cancelled'
                ],
                datasets: [{
                    data: [
                        orderStats.pending,
                        orderStats.confirmed,
                        orderStats.preparing,
                        orderStats.out_for_delivery,
                        orderStats.delivered,
                        orderStats.cancelled
                    ],
                    backgroundColor: [
                        '#6c757d',
                        '#0d6efd',
                        '#ffc107',
                        '#fd7e14',
                        '#198754',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endpush