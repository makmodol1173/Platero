@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>My Orders</h1>
            <p class="text-muted">
                @if(auth()->user()->isRestaurant())
                    Manage orders for your restaurant
                @else
                    Track and manage your food orders
                @endif
            </p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('orders.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready_for_pickup" {{ request('status') == 'ready_for_pickup' ? 'selected' : '' }}>Ready for Pickup</option>
                        <option value="out_for_delivery" {{ request('status') == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="total_high" {{ request('sort') == 'total_high' ? 'selected' : '' }}>Highest Amount</option>
                        <option value="total_low" {{ request('sort') == 'total_low' ? 'selected' : '' }}>Lowest Amount</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search by order ID or restaurant..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                @if(!auth()->user()->isRestaurant())
                                    <th>Restaurant</th>
                                @endif
                                @if(auth()->user()->isRestaurant())
                                    <th>Customer</th>
                                @endif
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="fw-bold text-decoration-none">
                                            #{{ $order->id }}
                                        </a>
                                    </td>
                                    @if(!auth()->user()->isRestaurant())
                                        <td>
                                            <a href="{{ route('restaurants.show', $order->restaurant) }}" class="text-decoration-none">
                                                {{ $order->restaurant->name }}
                                            </a>
                                        </td>
                                    @endif
                                    @if(auth()->user()->isRestaurant())
                                        <td>{{ $order->user->name }}</td>
                                    @endif
                                    <td>{{ $order->created_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $order->orderItems->count() }} items</td>
                                    <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $order->status_badge }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->status != 'cancelled' && $order->status != 'completed' && $order->status != 'delivered')
                                                @if(auth()->user()->isRestaurant())
                                                    <button type="button" class="btn btn-outline-secondary update-status-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateStatusModal" 
                                                            data-order-id="{{ $order->id }}"
                                                            data-current-status="{{ $order->status }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                @else
                                                    @if($order->status == 'pending')
                                                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                                                                <i class="fas fa-times"></i> Cancel
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            @endif
                                            @if($order->status == 'delivered' && !$order->is_rated)
                                                <button type="button" class="btn btn-outline-warning rate-order-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#rateOrderModal"
                                                        data-order-id="{{ $order->id }}"
                                                        data-restaurant-name="{{ $order->restaurant->name }}">
                                                    <i class="fas fa-star"></i> Rate
                                                </button>
                                            @endif
                                            @if($order->status == 'out_for_delivery')
                                                <a href="{{ route('orders.track', $order) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-map-marker-alt"></i> Track
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No orders found. 
            @if(!auth()->user()->isRestaurant())
                <a href="{{ route('restaurants.index') }}" class="alert-link">Browse restaurants</a> to place an order.
            @endif
        </div>
    @endif
</div>

<!-- Update Status Modal for Restaurant Owners -->
@if(auth()->user()->isRestaurant())
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusForm" action="" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="preparing">Preparing</option>
                                <option value="ready_for_pickup">Ready for Pickup</option>
                                <option value="out_for_delivery">Out for Delivery</option>
                                <option value="delivered">Delivered</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="status_note" class="form-label">Note (Optional)</label>
                            <textarea class="form-control" id="status_note" name="status_note" rows="3"></textarea>
                            <div class="form-text">This note will be visible to the customer.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Rate Order Modal for Customers -->
@if(!auth()->user()->isRestaurant())
    <div class="modal fade" id="rateOrderModal" tabindex="-1" aria-labelledby="rateOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rateOrderModalLabel">Rate Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rateOrderForm" action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <h5 id="restaurant-name-display"></h5>
                            <div class="rating-stars">
                                <div class="d-flex justify-content-center">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="mx-1">
                                            <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" class="d-none" required>
                                            <label for="star{{ $i }}" class="fs-3 text-warning rating-label">
                                                <i class="far fa-star"></i>
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="review" class="form-label">Review (Optional)</label>
                            <textarea class="form-control" id="review" name="review" rows="3" placeholder="Tell us about your experience..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Rating</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Update Status Modal
        $('.update-status-btn').click(function() {
            const orderId = $(this).data('order-id');
            const currentStatus = $(this).data('current-status');
            
            $('#updateStatusForm').attr('action', `/orders/${orderId}/status`);
            $('#status').val(currentStatus);
        });
        
        // Rate Order Modal
        $('.rate-order-btn').click(function() {
            const orderId = $(this).data('order-id');
            const restaurantName = $(this).data('restaurant-name');
            
            $('#rateOrderForm').attr('action', `/orders/${orderId}/rate`);
            $('#restaurant-name-display').text(restaurantName);
        });
        
        // Star Rating System
        $('.rating-label').hover(
            function() {
                const value = $(this).prev('input').val();
                
                // Fill in this star and all stars before it
                $(this).closest('.rating-stars').find('.rating-label').each(function() {
                    const starValue = $(this).prev('input').val();
                    if (starValue <= value) {
                        $(this).html('<i class="fas fa-star"></i>');
                    }
                });
            },
            function() {
                // On mouseout, reset stars to reflect the current selection
                updateStarsFromSelection();
            }
        );
        
        $('.rating-label').click(function() {
            $(this).prev('input').prop('checked', true);
            updateStarsFromSelection();
        });
        
        function updateStarsFromSelection() {
            const selectedValue = $('input[name="rating"]:checked').val();
            
            $('.rating-label').each(function() {
                const starValue = $(this).prev('input').val();
                if (selectedValue && starValue <= selectedValue) {
                    $(this).html('<i class="fas fa-star"></i>');
                } else {
                    $(this).html('<i class="far fa-star"></i>');
                }
            });
        }
        
        // Initialize stars based on any existing selection
        updateStarsFromSelection();
    });
</script>
@endpush

@push('styles')
<style>
    .status-badge {
        font-size: 0.8rem;
        padding: 0.35em 0.65em;
    }
    
    .rating-label {
        cursor: pointer;
    }
</style>
@endpush