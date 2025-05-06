@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Checkout</h1>
            <p class="text-muted">Complete your order from {{ $restaurant->name }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Restaurant
            </a>
        </div>
    </div>

    <form action="{{ route('restaurants.orders.store', $restaurant) }}" method="POST" id="checkout-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div id="cart-items">
                            <!-- Cart items will be displayed here -->
                        </div>
                        <div id="cart-empty" class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p>Your cart is empty</p>
                            <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-primary">
                                <i class="fas fa-utensils me-1"></i> Add Items to Your Order
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_type" id="delivery" value="delivery" checked>
                                <label class="form-check-label" for="delivery">Delivery</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="delivery_type" id="pickup" value="pickup">
                                <label class="form-check-label" for="pickup">Pickup</label>
                            </div>
                        </div>
                        
                        <div id="delivery-fields">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('recipient_name') is-invalid @enderror" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" required>
                                    @error('recipient_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state') }}" required>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="zip_code" class="form-label">ZIP Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="zip_code" name="zip_code" value="{{ old('zip_code') }}" required>
                                    @error('zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="delivery_instructions" class="form-label">Delivery Instructions (Optional)</label>
                                <textarea class="form-control @error('delivery_instructions') is-invalid @enderror" id="delivery_instructions" name="delivery_instructions" rows="2">{{ old('delivery_instructions') }}</textarea>
                                @error('delivery_instructions')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" id="cash_on_delivery" value="cash_on_delivery" checked>
                                        <label class="form-check-label w-100" for="cash_on_delivery">
                                            <div class="d-flex align-items-center">
                                                <div class="payment-icon me-2">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </div>
                                                <div>Cash on Delivery</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card">
                                        <label class="form-check-label w-100" for="credit_card">
                                            <div class="d-flex align-items-center">
                                                <div class="payment-icon me-2">
                                                    <i class="fas fa-credit-card"></i>
                                                </div>
                                                <div>Credit Card</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-check payment-method-card">
                                        <input class="form-check-input" type="radio" name="payment_method" id="mobile_payment" value="mobile_payment">
                                        <label class="form-check-label w-100" for="mobile_payment">
                                            <div class="d-flex align-items-center">
                                                <div class="payment-icon me-2">
                                                    <i class="fas fa-mobile-alt"></i>
                                                </div>
                                                <div>Mobile Payment</div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="credit-card-fields" class="d-none">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="card_name" class="form-label">Name on Card</label>
                                    <input type="text" class="form-control" id="card_name" name="card_name">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="expiry_date" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>
                        
                        <div id="mobile-payment-fields" class="d-none">
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
                        </div>
                    </div>
                </div>
                
                <!-- Special Instructions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Special Instructions (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-0">
                            <textarea class="form-control" id="special_instructions" name="special_instructions" rows="2" placeholder="Any special instructions for your order...">{{ old('special_instructions') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="card mb-4 sticky-top" style="top: 20px; z-index: 100;">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div id="summary-items">
                            <!-- Summary items will be displayed here -->
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="summary-subtotal">৳0.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery Fee:</span>
                            <span id="summary-delivery-fee">৳50.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (5%):</span>
                            <span id="summary-tax">৳0.00</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="summary-total">৳0.00</strong>
                        </div>
                        
                        <!-- Hidden fields for order data -->
                        <input type="hidden" id="cart_data" name="cart_data">
                        <input type="hidden" id="subtotal" name="subtotal">
                        <input type="hidden" id="delivery_fee" name="delivery_fee" value="50.00">
                        <input type="hidden" id="tax_amount" name="tax_amount">
                        <input type="hidden" id="total_amount" name="total_amount">
                        
                        <button type="submit" class="btn btn-primary w-100" id="place-order-btn">
                            <i class="fas fa-check-circle me-1"></i> Place Order
                        </button>
                    </div>
                </div>
                
                <!-- Restaurant Information -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Restaurant Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            @if($restaurant->logo)
                                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="me-3 rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="me-3 rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="fas fa-utensils fa-lg text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-1">{{ $restaurant->name }}</h6>
                                <p class="mb-0 text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i> 
                                    {{ $restaurant->address }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Load cart from localStorage
        let cart = [];
        if (localStorage.getItem('cart')) {
            cart = JSON.parse(localStorage.getItem('cart'));
        }
        
        // Update UI based on cart
        updateCartUI();
        
        // Toggle delivery/pickup fields
        $('input[name="delivery_type"]').change(function() {
            if ($(this).val() === 'delivery') {
                $('#delivery-fields').show();
                $('#summary-delivery-fee').text('৳50.00');
                $('input[name="delivery_fee"]').val('50.00');
            } else {
                $('#delivery-fields').hide();
                $('#summary-delivery-fee').text('৳0.00');
                $('input[name="delivery_fee"]').val('0.00');
            }
            updateTotals();
        });
        
        // Toggle payment method fields
        $('input[name="payment_method"]').change(function() {
            const method = $(this).val();
            
            $('#credit-card-fields, #mobile-payment-fields').addClass('d-none');
            
            if (method === 'credit_card') {
                $('#credit-card-fields').removeClass('d-none');
            } else if (method === 'mobile_payment') {
                $('#mobile-payment-fields').removeClass('d-none');
            }
        });
        
        // Update cart UI
        function updateCartUI() {
            const cartItems = $('#cart-items');
            const cartEmpty = $('#cart-empty');
            const summaryItems = $('#summary-items');
            
            if (cart.length === 0) {
                cartItems.hide();
                cartEmpty.show();
                $('#place-order-btn').prop('disabled', true);
                return;
            }
            
            cartItems.empty().show();
            summaryItems.empty();
            cartEmpty.hide();
            $('#place-order-btn').prop('disabled', false);
            
            cart.forEach((item, index) => {
                cartItems.append(`
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">${item.name}</h6>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <small class="text-muted">৳${item.price.toFixed(2)} x ${item.quantity}</small>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary decrease-quantity" data-index="${index}">-</button>
                                    <button type="button" class="btn btn-outline-secondary increase-quantity" data-index="${index}">+</button>
                                    <button type="button" class="btn btn-outline-danger remove-item" data-index="${index}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <span>৳${item.subtotal.toFixed(2)}</span>
                        </div>
                    </div>
                `);
                
                summaryItems.append(`
                    <div class="d-flex justify-content-between mb-2">
                        <span>${item.name} x ${item.quantity}</span>
                        <span>৳${item.subtotal.toFixed(2)}</span>
                    </div>
                `);
            });
            
            // Update totals
            updateTotals();
            
            // Set hidden fields
            $('#cart_data').val(JSON.stringify(cart));
        }
        
        // Update totals
        function updateTotals() {
            let subtotal = 0;
            cart.forEach(item => {
                subtotal += item.subtotal;
            });
            
            const deliveryFee = parseFloat($('input[name="delivery_fee"]').val());
            const taxRate = 0.05; // 5% tax
            const taxAmount = subtotal * taxRate;
            const total = subtotal + deliveryFee + taxAmount;
            
            $('#summary-subtotal').text(`৳${subtotal.toFixed(2)}`);
            $('#summary-tax').text(`৳${taxAmount.toFixed(2)}`);
            $('#summary-total').text(`৳${total.toFixed(2)}`);
            
            // Set hidden fields
            $('#subtotal').val(subtotal.toFixed(2));
            $('#tax_amount').val(taxAmount.toFixed(2));
            $('#total_amount').val(total.toFixed(2));
        }
        
        // Increase quantity
        $(document).on('click', '.increase-quantity', function() {
            const index = $(this).data('index');
            cart[index].quantity += 1;
            cart[index].subtotal = cart[index].quantity * cart[index].price;
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        });
        
        // Decrease quantity
        $(document).on('click', '.decrease-quantity', function() {
            const index = $(this).data('index');
            if (cart[index].quantity > 1) {
                cart[index].quantity -= 1;
                cart[index].subtotal = cart[index].quantity * cart[index].price;
            } else {
                cart.splice(index, 1);
            }
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        });
        
        // Remove item
        $(document).on('click', '.remove-item', function() {
            const index = $(this).data('index');
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        });
        
        // Form submission
        $('#checkout-form').submit(function() {
            if (cart.length === 0) {
                alert('Your cart is empty. Please add items to your order.');
                return false;
            }
            
            // Clear cart after successful submission
            localStorage.removeItem('cart');
            return true;
        });
    });
</script>
@endpush

@push('styles')
<style>
    .payment-method-card {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .payment-method-card:hover {
        border-color: #adb5bd;
    }
    
    .form-check-input:checked + .form-check-label .payment-method-card {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }
    
    .payment-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush