@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Track Order #{{ $order->order_number }}</h1>
            <p class="text-muted">
                Ordered from <strong>{{ $order->restaurant->name }}</strong> on {{ $order->created_at->format('M d, Y h:i A') }}
            </p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Order Details
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Order Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Status:</span>
                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </span>
                    </div>
                    
                    <div class="progress-tracker">
                        <div class="progress-step {{ in_array($order->status, ['pending', 'confirmed', 'preparing', 'out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <div class="step-text">Order Placed</div>
                        </div>
                        <div class="progress-step {{ in_array($order->status, ['confirmed', 'preparing', 'out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                            <div class="step-icon"><i class="fas fa-check"></i></div>
                            <div class="step-text">Confirmed</div>
                        </div>
                        <div class="progress-step {{ in_array($order->status, ['preparing', 'out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                            <div class="step-icon"><i class="fas fa-utensils"></i></div>
                            <div class="step-text">Preparing</div>
                        </div>
                        <div class="progress-step {{ in_array($order->status, ['out_for_delivery', 'delivered']) ? 'completed' : '' }}">
                            <div class="step-icon"><i class="fas fa-motorcycle"></i></div>
                            <div class="step-text">Out for Delivery</div>
                        </div>
                        <div class="progress-step {{ $order->status === 'delivered' ? 'completed' : '' }}">
                            <div class="step-icon"><i class="fas fa-home"></i></div>
                            <div class="step-text">Delivered</div>
                        </div>
                    </div>
                    
                    @if($order->estimated_delivery_time)
                        <div class="mt-3">
                            <strong>Estimated Delivery:</strong>
                            <span>{{ \Carbon\Carbon::parse($order->estimated_delivery_time)->format('h:i A') }}</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Delivery Address</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->deliveryInfo->recipient_name }}</strong></p>
                    <p class="mb-1">{{ $order->deliveryInfo->address }}</p>
                    <p class="mb-1">{{ $order->deliveryInfo->city }}, {{ $order->deliveryInfo->state }} {{ $order->deliveryInfo->zip_code }}</p>
                    <p class="mb-0">Phone: {{ $order->deliveryInfo->phone }}</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Restaurant</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $order->restaurant->name }}</strong></p>
                    <p class="mb-1">{{ $order->restaurant->address }}</p>
                    <p class="mb-1">{{ $order->restaurant->city }}, {{ $order->restaurant->state }} {{ $order->restaurant->zip_code }}</p>
                    <p class="mb-0">Phone: {{ $order->restaurant->phone }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Live Tracking</h5>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .progress-tracker {
        display: flex;
        flex-direction: column;
        gap: 20px;
        position: relative;
        margin: 20px 0;
    }
    
    .progress-tracker::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 15px;
        width: 2px;
        background-color: #e9ecef;
        z-index: 0;
    }
    
    .progress-step {
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    
    .step-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: #6c757d;
    }
    
    .progress-step.completed .step-icon {
        background-color: #198754;
        color: white;
    }
    
    .step-text {
        font-weight: 500;
    }
    
    .progress-step.completed .step-text {
        color: #198754;
    }
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ $apiKey }}&callback=initMap" async defer></script>
<script>
    let map;
    let restaurantMarker;
    let deliveryMarker;
    let directionsService;
    let directionsRenderer;
    
    function initMap() {
        // Initialize the map
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13,
            center: { lat: {{ $order->restaurant->latitude ?? 23.8103 }}, lng: {{ $order->restaurant->longitude ?? 90.4125 }} },
        });
        
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: true,
        });
        
        // Add restaurant marker
        restaurantMarker = new google.maps.Marker({
            position: { lat: {{ $order->restaurant->latitude ?? 23.8103 }}, lng: {{ $order->restaurant->longitude ?? 90.4125 }} },
            map: map,
            title: '{{ $order->restaurant->name }}',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
            }
        });
        
        // Add delivery location marker
        deliveryMarker = new google.maps.Marker({
            position: { lat: {{ $order->deliveryInfo->latitude ?? 23.8203 }}, lng: {{ $order->deliveryInfo->longitude ?? 90.4225 }} },
            map: map,
            title: 'Delivery Location',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
            }
        });
        
        // If order is out for delivery, add delivery person marker and start tracking
        if ('{{ $order->status }}' === 'out_for_delivery') {
            // Add delivery person marker
            const deliveryPersonMarker = new google.maps.Marker({
                position: { lat: {{ $order->restaurant->latitude ?? 23.8103 }}, lng: {{ $order->restaurant->longitude ?? 90.4125 }} },
                map: map,
                title: 'Delivery Person',
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                }
            });
            
            // Start tracking
            trackDelivery(deliveryPersonMarker);
        }
        
        // Calculate and display route
        calculateRoute();
    }
    
    function calculateRoute() {
        const restaurantLocation = restaurantMarker.getPosition();
        const deliveryLocation = deliveryMarker.getPosition();
        
        directionsService.route({
            origin: restaurantLocation,
            destination: deliveryLocation,
            travelMode: google.maps.TravelMode.DRIVING,
        }, (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                
                // Fit map to route bounds
                const bounds = new google.maps.LatLngBounds();
                bounds.extend(restaurantLocation);
                bounds.extend(deliveryLocation);
                map.fitBounds(bounds);
            }
        });
    }
    
    function trackDelivery(marker) {
        // In a real application, you would use WebSockets or polling to get real-time updates
        // For this example, we'll simulate movement along the route
        
        // Check for location updates every 5 seconds
        setInterval(() => {
            // In a real app, you would fetch the current location from the server
            // For this example, we'll just simulate movement
            $.ajax({
                url: '{{ route("orders.track.location", $order) }}',
                method: 'GET',
                success: function(data) {
                    if (data.latitude && data.longitude) {
                        const newPosition = new google.maps.LatLng(data.latitude, data.longitude);
                        marker.setPosition(newPosition);
                    }
                }
            });
        }, 5000);
    }
</script>
@endpush