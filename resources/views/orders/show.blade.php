@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Order #{{ $order->id }}</h1>
            <p class="text-muted">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Status -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Order Status</h5>
                    <span class="badge {{ $order->status_badge }}">{{ ucfirst($order->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="order-timeline">
                        <div class="progress mb-4" style="height: 5px;">
                            @php
                                $statusProgress = [
                                    'pending' => 0,
                                    'confirmed' => 20,
                                    'preparing' => 40,
                                    'ready_for_pickup' => 60,
                                    'out_for_delivery' => 80,
                                    'delivered' => 100,
                                    'completed' => 100,
                                    'cancelled' => 100
                                ];
                                $currentProgress = $statusProgress[$order->status] ?? 0;
                            @endphp
                            <div class="progress-bar {{ $order->status == 'cancelled' ? 'bg-danger' : '' }}" role="progressbar" style="width: {{ $currentProgress }}%" aria-valuenow="{{ $currentProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="d-flex justify-content-between text-center">
                            <div class="timeline-step {{ $currentProgress >= 0 ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="timeline-text">Pending</div>
                            </div>
                            <div class="timeline-step {{ $currentProgress >= 20 ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-text">Confirmed</div>
                            </div>
                            <div class="timeline-step {{ $currentProgress >= 40 ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <div class="timeline-text">Preparing</div>
                            </div>
                            <div class="timeline-step {{ $currentProgress >= 60 ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="timeline-text">Ready</div>
                            </div>
                            <div class="timeline-step {{ $currentProgress >= 80 ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="timeline-text">Delivery</div>
                            </div>
                            <div class="timeline-step {{ $currentProgress >= 100 ? 'active' : '' }}">
                                <div class="timeline-icon">
                                    <i class="fas {{ $order->status == 'cancelled' ? 'fa-times' : 'fa-flag-checkered' }}"></i>
                                </div>
                                <div class="timeline-text">{{ $order->status == 'cancelled' ? 'Cancelled' : 'Delivered' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($order->status_updates->count() > 0)
                        <div class="mt-4">
                            <h6>Status History</h6>
                            <div class="status-history">
                                @foreach($order->status_updates as $update)
                                    <div class="status-item d-flex">
                                        <div class="status-time me-3">
                                            <div class="text-muted">{{ $update->created_at->format('M d, g:i A') }}</div>
                                        </div>
                                        <div class="status-content">
                                            <div class="d-flex align-items-center">
                                                <span class="badge {{ $update->status_badge }} me-2">{{ ucfirst($update->status) }}</span>
                                                <span>{{ $update->updated_by ? 'by ' . $update->updated_by->name : '' }}</span>
                                            </div>
                                            @if($update->note)
                                                <div class="status-note mt-1">{{ $update->note }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if(auth()->user()->isRestaurant() && $order->status != 'cancelled' && $order->status != 'completed' && $order->status != 'delivered')
                        <div class="mt-4">
                            <button type="button" class="btn btn-primary update-status-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#updateStatusModal" 
                                    data-order-id="{{ $order->id }}"
                                    data-current-status="{{ $order->status }}">
                                <i class="fas fa-edit"></i> Update Status
                            </button>
                        </div>
                    @endif
                    
                    @if(!auth()->user()->isRestaurant() && $order->status == 'pending')
                        <div class="mt-4">
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this order?')">
                                    <i class="fas fa-times"></i> Cancel Order
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->menuItem && $item->menuItem->image)
                                                    <img src="{{ asset('storage/' . $item->menuItem->image) }}" alt="{{ $item->name }}" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $item->name }}</div>
                                                    @if($item->special_instructions)
                                                        <div class="text-muted small">{{ $item->special_instructions }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">৳{{ number_format($item->price, 2) }}</td>
                                        <td class="text-end">৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Restaurant Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Restaurant Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @if($order->restaurant->logo)
                            <img src="{{ asset('storage/' . $order->restaurant->logo) }}" alt="{{ $order->restaurant->name }}" class="me-3 rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-utensils fa-2x text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-1">{{ $order->restaurant->name }}</h5>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> 
                                {{ $order->restaurant->address }}, {{ $order->restaurant->city }}
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-phone me-1"></i> {{ $order->restaurant->phone }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('restaurants.show', $order->restaurant) }}" class="btn btn-outline-primary">
                            <i class="fas fa-store me-1"></i> View Restaurant
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Rating and Review (if applicable) -->
            @if($order->status == 'delivered' || $order->status == 'completed')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Rating & Review</h5>
                    </div>
                    <div class="card-body">
                        @if($order->rating)
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $order->rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-muted">{{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($order->review)
                                    <p class="mb-0">{{ $order->review }}</p>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="mb-3">How was your experience with this order?</p>
                                <button type="button" class="btn btn-primary rate-order-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rateOrderModal"
                                        data-order-id="{{ $order->id }}"
                                        data-restaurant-name="{{ $order->restaurant->name }}">
                                    <i class="fas fa-star me-1"></i> Rate This Order
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>৳{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>৳{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Discount:</span>
                            <span class="text-success">-৳{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax:</span>
                        <span>৳{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-0">
                        <strong>Total:</strong>
                        <strong>৳{{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Payment Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Payment Method:</span>
                        <span>{{ ucfirst($order->payment_method) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Payment Status:</span>
                        <span class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                    @if($order->payment_transaction)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Transaction ID:</span>
                            <span>{{ $order->payment_transaction->transaction_id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Transaction Date:</span>
                            <span>{{ $order->payment_transaction->created_at->format('M d, Y g:i A') }}</span>
                        </div>
                    @endif
                    
                    @if($order->payment_status == 'pending')
                        <div class="mt-3">
                            <a href="{{ route('orders.payment', $order) }}" class="btn btn-primary w-100">
                                <i class="fas fa-credit-card me-1"></i> Pay Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Delivery Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Information</h5>
                </div>
                <div class="card-body">
                    @if($order->deliveryInfo)
                        <div class="mb-3">
                            <h6>Delivery Address</h6>
                            <address class="mb-0">
                                {{ $order->deliveryInfo->address }}<br>
                                {{ $order->deliveryInfo->city }}, {{ $order->deliveryInfo->state }} {{ $order->deliveryInfo->zip_code }}
                            </address>
                        </div>
                        <div class="mb-3">
                            <h6>Contact Information</h6>
                            <p class="mb-1">{{ $order->deliveryInfo->recipient_name }}</p>
                            <p class="mb-0">{{ $order->deliveryInfo->phone_number }}</p>
                        </div>
                        @if($order->deliveryInfo->delivery_instructions)
                            <div class="mb-0">
                                <h6>Delivery Instructions</h6>
                                <p class="mb-0">{{ $order->deliveryInfo->delivery_instructions }}</p>
                            </div>
                        @endif
                        
                        @if($order->status == 'out_for_delivery')
                            <div class="mt-3">
                                <a href="{{ route('orders.track', $order) }}" class="btn btn-info w-100">
                                    <i class="fas fa-map-marker-alt me-1"></i> Track Delivery
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="mb-0">This order is for pickup.</p>
                    @endif
                </div>
            </div>
            
            <!-- Customer Information -->
            @if(auth()->user()->isRestaurant())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <div class="avatar-circle">
                                    {{ substr($order->user->name, 0, 1) }}
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $order->user->name }}</h6>
                                <p class="mb-0 text-muted">{{ $order->user->email }}</p>
                            </div>
                        </div>
                        <div class="mb-0">
                            <p class="mb-1"><strong>Customer Since:</strong> {{ $order->user->created_at->format('M Y') }}</p>
                            <p class="mb-0"><strong>Orders:</strong> {{ $order->user->orders->count() }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
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
    .timeline-step {
        position: relative;
        width: 16.666%;
    }
    
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
    }
    
    .timeline-step.active .timeline-icon {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .status-history {
        position: relative;
        padding-left: 20px;
    }
    
    .status-history::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
    }
    
    .status-item {
        position: relative;
        padding-bottom: 15px;
    }
    
    .status-time::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 5px;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background-color: #0d6efd;
    }
    
    .avatar-circle {
        width: 50px;
        height: 50px;
        background-color: #0d6efd;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
    }
    
    .rating-label {
        cursor: pointer;
    }
</style>
@endpush