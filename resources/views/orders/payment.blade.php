@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Payment</h1>
            <p class="text-muted">Complete payment for Order #{{ $order->id }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Order
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Payment Methods -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Select Payment Method</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.initiate-payment', $order) }}" method="POST" id="payment-form">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <div class="payment-option">
                                    <input type="radio" class="btn-check" name="payment_method" id="credit_card" value="credit_card" autocomplete="off" checked>
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" for="credit_card">
                                        <i class="fas fa-credit-card fa-2x mb-2"></i>
                                        <span>Credit Card</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="payment-option">
                                    <input type="radio" class="btn-check" name="payment_method" id="mobile_payment" value="mobile_payment" autocomplete="off">
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" for="mobile_payment">
                                        <i class="fas fa-mobile-alt fa-2x mb-2"></i>
                                        <span>Mobile Payment</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="payment-option">
                                    <input type="radio" class="btn-check" name="payment_method" id="cash_on_delivery" value="cash_on_delivery" autocomplete="off">
                                    <label class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-4" for="cash_on_delivery">
                                        <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                        <span>Cash on Delivery</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Credit Card Form -->
                        <div id="credit-card-form" class="payment-form">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="card_number" class="form-label">Card Number</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
                                                <span class="input-group-text">
                                                    <i class="fab fa-cc-visa me-1"></i>
                                                    <i class="fab fa-cc-mastercard me-1"></i>
                                                    <i class="fab fa-cc-amex"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="card_name" class="form-label">Name on Card</label>
                                            <input type="text" class="form-control" id="card_name" name="card_name" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiry_date" class="form-label">Expiry Date</label>
                                            <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
                                                <span class="input-group-text">
                                                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="3-digit code on the back of your card"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mobile Payment Form -->
                        <div id="mobile-payment-form" class="payment-form d-none">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="mobile_provider" class="form-label">Mobile Payment Provider</label>
                                            <select class="form-select" id="mobile_provider" name="mobile_provider">
                                                <option value="bkash">bKash</option>
                                                <option value="nagad">Nagad</option>
                                                <option value="rocket">Rocket</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="mobile_number" class="form-label">Mobile Number</label>
                                            <input type="text" class="form-control" id="mobile_number" name="mobile_number">
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        You will receive a payment confirmation code on your mobile phone. Please enter that code below.
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="confirmation_code" class="form-label">Confirmation Code</label>
                                            <input type="text" class="form-control" id="confirmation_code" name="confirmation_code">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cash on Delivery Form -->
                        <div id="cash-on-delivery-form" class="payment-form d-none">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        You will pay the full amount to the delivery person when your order arrives.
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirm_cod" name="confirm_cod" required>
                                        <label class="form-check-label" for="confirm_cod">
                                            I confirm that I will pay ৳{{ number_format($order->total_amount, 2) }} upon delivery.
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-lock me-2"></i> Pay ৳{{ number_format($order->total_amount, 2) }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Payment Security -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-shield-alt fa-2x text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Secure Payment</h5>
                            <p class="mb-0 text-muted">Your payment information is encrypted and secure.</p>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <i class="fas fa-lock text-muted mb-2"></i>
                            <p class="mb-0 small">SSL Encrypted</p>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <i class="fas fa-credit-card text-muted mb-2"></i>
                            <p class="mb-0 small">Secure Payments</p>
                        </div>
                        <div class="col-md-4">
                            <i class="fas fa-shield-alt text-muted mb-2"></i>
                            <p class="mb-0 small">Data Protection</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Order ID:</span>
                            <span>#{{ $order->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Date:</span>
                            <span>{{ $order->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <h6>Items</h6>
                        @foreach($order->orderItems as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ $item->name }} x {{ $item->quantity }}</span>
                                <span>৳{{ number_format($item->price * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>৳{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Delivery Fee:</span>
                        <span>৳{{ number_format($order->delivery_fee, 2) }}</span>
                    </div>
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
            
            <!-- Restaurant Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Restaurant</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        @if($order->restaurant->logo)
                            <img src="{{ asset('storage/' . $order->restaurant->logo) }}" alt="{{ $order->restaurant->name }}" class="me-3 rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-utensils fa-lg text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="mb-1">{{ $order->restaurant->name }}</h6>
                            <p class="mb-0 text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i> 
                                {{ $order->restaurant->address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Toggle payment forms
        $('input[name="payment_method"]').change(function() {
            const method = $(this).val();
            
            // Hide all forms
            $('.payment-form').addClass('d-none');
            
            // Show selected form
            if (method === 'credit_card') {
                $('#credit-card-form').removeClass('d-none');
            } else if (method === 'mobile_payment') {
                $('#mobile-payment-form').removeClass('d-none');
            } else if (method === 'cash_on_delivery') {
                $('#cash-on-delivery-form').removeClass('d-none');
            }
        });
        
        // Format credit card number
        $('#card_number').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            $(this).val(formattedValue);
        });
        
        // Format expiry date
        $('#expiry_date').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            $(this).val(value);
        });
        
        // Limit CVV to 3 or 4 digits
        $('#cvv').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(value.substring(0, 4));
        });
        
        // Form validation
        $('#payment-form').submit(function(e) {
            const method = $('input[name="payment_method"]:checked').val();
            
            if (method === 'credit_card') {
                // Validate credit card fields
                if ($('#card_number').val().replace(/\s/g, '').length < 16) {
                    alert('Please enter a valid card number');
                    e.preventDefault();
                    return false;
                }
                
                if ($('#card_name').val().trim() === '') {
                    alert('Please enter the name on the card');
                    e.preventDefault();
                    return false;
                }
                
                if ($('#expiry_date').val().length < 5) {
                    alert('Please enter a valid expiry date');
                    e.preventDefault();
                    return false;
                }
                
                if ($('#cvv').val().length < 3) {
                    alert('Please enter a valid CVV');
                    e.preventDefault();
                    return false;
                }
            } else if (method === 'mobile_payment') {
                // Validate mobile payment fields
                if ($('#mobile_number').val().trim() === '') {
                    alert('Please enter your mobile number');
                    e.preventDefault();
                    return false;
                }
                
                if ($('#confirmation_code').val().trim() === '') {
                    alert('Please enter the confirmation code');
                    e.preventDefault();
                    return false;
                }
            } else if (method === 'cash_on_delivery') {
                // Validate cash on delivery fields
                if (!$('#confirm_cod').is(':checked')) {
                    alert('Please confirm that you will pay upon delivery');
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    });
</script>
@endpush

@push('styles')
<style>
    .payment-option {
        height: 100%;
    }
    
    .btn-check:checked + .btn-outline-primary {
        background-color: #0d6efd;
        color: white;
    }
</style>
@endpush