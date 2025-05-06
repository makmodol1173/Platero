@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Restaurant Header -->
    <div class="position-relative mb-5">
        <div class="restaurant-cover">
            @if($restaurant->cover_image)
                <img src="{{ asset('storage/' . $restaurant->cover_image) }}" alt="{{ $restaurant->name }}" class="w-100 rounded" style="height: 300px; object-fit: cover;">
            @else
                <div class="bg-light w-100 rounded" style="height: 300px;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <i class="fas fa-utensils fa-4x text-muted"></i>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="restaurant-info position-absolute w-100" style="bottom: -50px;">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="d-flex align-items-end">
                            @if($restaurant->logo)
                                <div class="me-3">
                                    <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="rounded-circle border border-white" style="width: 100px; height: 100px; object-fit: cover; background-color: white;">
                                </div>
                            @endif
                            <div>
                                <h1 class="mb-0 text-white text-shadow">{{ $restaurant->name }}</h1>
                                <p class="text-white text-shadow mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i> 
                                    {{ $restaurant->address }}, {{ $restaurant->city }}, {{ $restaurant->state }} {{ $restaurant->zip_code }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        @can('update', $restaurant)
                            <a href="{{ route('restaurants.edit', $restaurant) }}" class="btn btn-outline-light">
                                <i class="fas fa-edit"></i> Edit Restaurant
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Restaurant Details -->
    <div class="row mt-5 pt-3">
        <div class="col-md-8">
            <!-- Restaurant Description -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">About {{ $restaurant->name }}</h5>
                    <p class="card-text">{{ $restaurant->description }}</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-clock me-2"></i> Hours</h6>
                            @if($restaurant->opening_time && $restaurant->closing_time)
                                <p class="mb-0">
                                    {{ \Carbon\Carbon::parse($restaurant->opening_time)->format('g:i A') }} - 
                                    {{ \Carbon\Carbon::parse($restaurant->closing_time)->format('g:i A') }}
                                </p>
                            @else
                                <p class="mb-0">Hours not specified</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-phone me-2"></i> Contact</h6>
                            <p class="mb-0">{{ $restaurant->phone }}</p>
                            <p class="mb-0">{{ $restaurant->email }}</p>
                            @if($restaurant->website)
                                <p class="mb-0">
                                    <a href="{{ $restaurant->website }}" target="_blank">{{ $restaurant->website }}</a>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Menus -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Menus</h5>
                    @can('update', $restaurant)
                        <a href="{{ route('restaurants.menus.create', $restaurant) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Menu
                        </a>
                    @endcan
                </div>
                <div class="card-body">
                    @if($menus->count() > 0)
                        <ul class="nav nav-tabs mb-4" id="menuTabs" role="tablist">
                            @foreach($menus as $index => $menu)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                            id="menu-{{ $menu->id }}-tab" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#menu-{{ $menu->id }}" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="menu-{{ $menu->id }}" 
                                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                        {{ $menu->name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        
                        <div class="tab-content" id="menuTabsContent">
                            @foreach($menus as $index => $menu)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                     id="menu-{{ $menu->id }}" 
                                     role="tabpanel" 
                                     aria-labelledby="menu-{{ $menu->id }}-tab">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5>{{ $menu->name }}</h5>
                                        @can('update', $restaurant)
                                            <div>
                                                <a href="{{ route('restaurants.menus.edit', [$restaurant, $menu]) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Edit Menu
                                                </a>
                                                <a href="{{ route('restaurants.menus.menu-items.create', [$restaurant, $menu]) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-plus"></i> Add Item
                                                </a>
                                            </div>
                                        @endcan
                                    </div>
                                    
                                    @if($menu->description)
                                        <p class="text-muted">{{ $menu->description }}</p>
                                    @endif
                                    
                                    @if($menu->menuItems->count() > 0)
                                        <div class="row">
                                            @foreach($menu->menuItems as $menuItem)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card h-100">
                                                        <div class="row g-0">
                                                            @if($menuItem->image)
                                                                <div class="col-4">
                                                                    <img src="{{ asset('storage/' . $menuItem->image) }}" class="img-fluid rounded-start h-100" alt="{{ $menuItem->name }}" style="object-fit: cover;">
                                                                </div>
                                                            @endif
                                                            <div class="{{ $menuItem->image ? 'col-8' : 'col-12' }}">
                                                                <div class="card-body">
                                                                    <div class="d-flex justify-content-between">
                                                                        <h6 class="card-title">{{ $menuItem->name }}</h6>
                                                                        <span class="badge bg-primary">৳{{ number_format($menuItem->price, 2) }}</span>
                                                                    </div>
                                                                    <p class="card-text small text-muted">{{ Str::limit($menuItem->description, 60) }}</p>
                                                                    
                                                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                                                        @if($menuItem->is_vegetarian)
                                                                            <span class="badge bg-success">Vegetarian</span>
                                                                        @endif
                                                                        @if($menuItem->is_vegan)
                                                                            <span class="badge bg-success">Vegan</span>
                                                                        @endif
                                                                        @if($menuItem->is_gluten_free)
                                                                            <span class="badge bg-info">Gluten Free</span>
                                                                        @endif
                                                                    </div>
                                                                    
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <button class="btn btn-sm btn-outline-primary add-to-cart" 
                                                                                data-id="{{ $menuItem->id }}" 
                                                                                data-name="{{ $menuItem->name }}" 
                                                                                data-price="{{ $menuItem->price }}">
                                                                            <i class="fas fa-cart-plus"></i> Add
                                                                        </button>
                                                                        
                                                                        @can('update', $restaurant)
                                                                            <div>
                                                                                <a href="{{ route('restaurants.menus.menu-items.edit', [$restaurant, $menu, $menuItem]) }}" class="btn btn-sm btn-outline-secondary">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <form action="{{ route('restaurants.menus.menu-items.destroy', [$restaurant, $menu, $menuItem]) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        @endcan
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            No menu items found for this menu.
                                            @can('update', $restaurant)
                                                <a href="{{ route('restaurants.menus.menu-items.create', [$restaurant, $menu]) }}">Add your first menu item</a>.
                                            @endcan
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            No menus found for this restaurant.
                            @can('update', $restaurant)
                                <a href="{{ route('restaurants.menus.create', $restaurant) }}">Add your first menu</a>.
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Order Card -->
            <div class="card mb-4 sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header">
                    <h5 class="mb-0">Your Order</h5>
                </div>
                <div class="card-body">
                    <div id="cart-items">
                        <!-- Cart items will be displayed here -->
                    </div>
                    <div id="cart-empty" class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="cart-subtotal">৳0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Delivery Fee:</span>
                        <span>৳50.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="cart-total">৳0.00</strong>
                    </div>
                    <a href="{{ route('restaurants.orders.create', $restaurant) }}" id="checkout-btn" class="btn btn-primary w-100 disabled">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
            
            <!-- Map Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Location</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 300px;"></div>
                </div>
                <div class="card-footer">
                    <address class="mb-0">
                        {{ $restaurant->address }}<br>
                        {{ $restaurant->city }}, {{ $restaurant->state }} {{ $restaurant->zip_code }}
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-shadow {
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
    }
    
    .restaurant-cover::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
        border-radius: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        let cart = [];
        const restaurantId = {{ $restaurant->id }};
        
        // Load cart from localStorage
        if (localStorage.getItem('cart')) {
            cart = JSON.parse(localStorage.getItem('cart'));
            updateCartUI();
        }
        
        // Add to cart
        $('.add-to-cart').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const price = parseFloat($(this).data('price'));
            
            // Check if item already in cart
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.subtotal = existingItem.quantity * existingItem.price;
            } else {
                cart.push({
                    id: id,
                    name: name,
                    price: price,
                    quantity: 1,
                    subtotal: price
                });
            }
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update UI
            updateCartUI();
            
            // Show toast notification
            showToast(`${name} added to cart`);
        });
        
        // Update cart UI
        function updateCartUI() {
            const cartItems = $('#cart-items');
            const cartEmpty = $('#cart-empty');
            const cartSubtotal = $('#cart-subtotal');
            const cartTotal = $('#cart-total');
            const checkoutBtn = $('#checkout-btn');
            
            if (cart.length === 0) {
                cartItems.hide();
                cartEmpty.show();
                cartSubtotal.text('৳0.00');
                cartTotal.text('৳0.00');
                checkoutBtn.addClass('disabled');
                return;
            }
            
            cartItems.empty().show();
            cartEmpty.hide();
            
            let subtotal = 0;
            
            cart.forEach((item, index) => {
                subtotal += item.subtotal;
                
                cartItems.append(`
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="mb-0">${item.name}</h6>
                            <small class="text-muted">৳${item.price.toFixed(2)} x ${item.quantity}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-3">৳${item.subtotal.toFixed(2)}</span>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary decrease-quantity" data-index="${index}">-</button>
                                <button type="button" class="btn btn-outline-secondary increase-quantity" data-index="${index}">+</button>
                                <button type="button" class="btn btn-outline-danger remove-item" data-index="${index}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);
            });
            
            const total = subtotal + 50; // Adding delivery fee
            
            cartSubtotal.text(`৳${subtotal.toFixed(2)}`);
            cartTotal.text(`৳${total.toFixed(2)}`);
            checkoutBtn.removeClass('disabled');
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
        
        // Show toast notification
        function showToast(message) {
            const toast = $(`
                <div class="toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `);
            
            $('body').append(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Initialize map if Google Maps API is available
        if (typeof google !== 'undefined') {
            initMap();
        }
        
        function initMap() {
            const lat = {{ $restaurant->latitude ?? 23.8103 }};
            const lng = {{ $restaurant->longitude ?? 90.4125 }};
            
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat, lng },
                zoom: 15
            });
            
            const marker = new google.maps.Marker({
                position: { lat, lng },
                map: map,
                title: '{{ $restaurant->name }}'
            });
        }
    });
</script>

<!-- Include Google Maps API if you have an API key -->
@if(config('services.google_maps.api_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap" async defer></script>
@endif
@endpush