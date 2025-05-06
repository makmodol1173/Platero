@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>{{ $menu->name }}</h1>
            <p class="text-muted">{{ $menu->description }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            @can('update', $restaurant)
                <a href="{{ route('restaurants.menus.menu-items.create', [$restaurant, $menu]) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Menu Item
                </a>
                <a href="{{ route('restaurants.menus.edit', [$restaurant, $menu]) }}" class="btn btn-secondary">
                    <i class="fas fa-edit"></i> Edit Menu
                </a>
            @endcan
            <a href="{{ route('restaurants.menus.index', $restaurant) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Menus
            </a>
        </div>
    </div>

    <div class="row">
        @forelse($menuItems as $menuItem)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($menuItem->image)
                        <img src="{{ asset('storage/' . $menuItem->image) }}" class="card-img-top" alt="{{ $menuItem->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5">
                            <i class="fas fa-utensils fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">{{ $menuItem->name }}</h5>
                            <span class="badge bg-primary">৳{{ number_format($menuItem->price, 2) }}</span>
                        </div>
                        <p class="card-text text-muted">{{ Str::limit($menuItem->description, 100) }}</p>
                        
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @if($menuItem->is_vegetarian)
                                <span class="badge bg-success">Vegetarian</span>
                            @endif
                            @if($menuItem->is_vegan)
                                <span class="badge bg-success">Vegan</span>
                            @endif
                            @if($menuItem->is_gluten_free)
                                <span class="badge bg-info">Gluten Free</span>
                            @endif
                            @if(!$menuItem->is_available)
                                <span class="badge bg-danger">Not Available</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button class="btn btn-sm btn-primary add-to-cart" data-id="{{ $menuItem->id }}" data-name="{{ $menuItem->name }}" data-price="{{ $menuItem->price }}">
                                <i class="fas fa-cart-plus"></i> Add to Cart
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
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No menu items found. 
                    @can('update', $restaurant)
                        <a href="{{ route('restaurants.menus.menu-items.create', [$restaurant, $menu]) }}">Add your first menu item</a>.
                    @endcan
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $menuItems->links() }}
    </div>
</div>

<!-- Shopping Cart Modal -->
<div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Your Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="cart-items">
                    <!-- Cart items will be displayed here -->
                </div>
                <div id="cart-empty" class="text-center py-4">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <p>Your cart is empty</p>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <strong>Total: </strong>
                    <span id="cart-total">৳0.00</span>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <a href="#" id="checkout-btn" class="btn btn-primary">Checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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
            const cartTotal = $('#cart-total');
            
            if (cart.length === 0) {
                cartItems.hide();
                cartEmpty.show();
                cartTotal.text('৳0.00');
                return;
            }
            
            cartItems.empty().show();
            cartEmpty.hide();
            
            let total = 0;
            
            cart.forEach((item, index) => {
                total += item.subtotal;
                
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
            
            cartTotal.text(`৳${total.toFixed(2)}`);
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
        
        // Show cart modal
        $('.show-cart').click(function() {
            $('#cartModal').modal('show');
        });
        
        // Checkout button
        $('#checkout-btn').click(function(e) {
            e.preventDefault();
            
            if (cart.length === 0) {
                alert('Your cart is empty');
                return;
            }
            
            window.location.href = `/restaurants/${restaurantId}/checkout`;
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
    });
</script>
@endpush