@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Track Order #{{ $order->id }}</h1>
            <p class="text-muted">Follow your delivery in real-time</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Order
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Map -->
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Delivery Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div class="status-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-1">Order is on the way</h6>
                            <p class="mb-0 text-muted" id="estimated-time">Calculating arrival time...</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between text-center">
                            <div>
                                <div class="status-dot active"></div>
                                <div class="status-text">Confirmed</div>
                            </div>
                            <div>
                                <div class="status-dot active"></div>
                                <div class="status-text">Preparing</div>
                            </div>
                            <div>
                                <div class="status-dot active"></div>
                                <div class="status-text">On the way</div>
                            </div>
                            <div>
                                <div class="status-dot"></div>
                                <div class="status-text">Delivered</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Details</h5>
                </div>
                <div class="card-body">
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
    let map;
    let restaurantMarker;
    let deliveryMarker;
    let deliveryPath;
    let directionsService;
    let directionsRenderer;
    
    // Initialize map
    function initMap() {
        // Restaurant coordinates
        const restaurantLat = {{ $order->restaurant->latitude ?? 23.8103 }};
        const restaurantLng = {{ $order->restaurant->longitude ?? 90.4125 }};
        const restaurantPosition = { lat: restaurantLat, lng: restaurantLng };
        
        // Delivery address coordinates
        const deliveryLat = {{ $order->deliveryInfo->latitude ?? 23.8103 }};
        const deliveryLng = {{ $order->deliveryInfo->longitude ?? 90.4225 }};
        const deliveryPosition = { lat: deliveryLat, lng: deliveryLng };
        
        // Initialize map
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: (restaurantLat + deliveryLat) / 2, lng: (restaurantLng + deliveryLng) / 2 },
            zoom: 13,
            mapTypeControl: false,
            fullscreenControl: false,
            streetViewControl: false
        });
        
        // Initialize directions service
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            polylineOptions: {
                strokeColor: '#0d6efd',
                strokeWeight: 5
            }
        });
        directionsRenderer.setMap(map);
        
        // Restaurant marker
        restaurantMarker = new google.maps.Marker({
            position: restaurantPosition,
            map: map,
            title: '{{ $order->restaurant->name }}',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(40, 40)
            }
        });
        
        // Restaurant info window
        const restaurantInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 5px;">
                    <strong>{{ $order->restaurant->name }}</strong><br>
                    {{ $order->restaurant->address }}
                </div>
            `
        });
        
        restaurantMarker.addListener('click', () => {
            restaurantInfoWindow.open(map, restaurantMarker);
        });
        
        // Delivery marker
        deliveryMarker = new google.maps.Marker({
            position: deliveryPosition,
            map: map,
            title: 'Delivery Location',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                scaledSize: new google.maps.Size(40, 40)
            }
        });
        
        // Delivery info window
        const deliveryInfoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 5px;">
                    <strong>Delivery Location</strong><br>
                    {{ $order->deliveryInfo->address }}
                </div>
            `
        });
        
        deliveryMarker.addListener('click', () => {
            deliveryInfoWindow.open(map, deliveryMarker);
        });
        
        // Calculate and display route
        calculateRoute(restaurantPosition, deliveryPosition);
        
        // Simulate delivery movement
        simulateDelivery(restaurantPosition, deliveryPosition);
    }
    
    // Calculate route between restaurant and delivery location
    function calculateRoute(origin, destination) {
        directionsService.route({
            origin: origin,
            destination: destination,
            travelMode: google.maps.TravelMode.DRIVING
        }, (response, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(response);
                
                // Calculate estimated arrival time
                const route = response.routes[0];
                let duration = 0;
                
                for (let i = 0; i < route.legs.length; i++) {
                    duration += route.legs[i].duration.value;
                }
                
                // Convert seconds to minutes
                const minutes = Math.ceil(duration / 60);
                
                // Update estimated time
                document.getElementById('estimated-time').textContent = `Estimated arrival in ${minutes} minutes`;
            }
        });
    }
    
    // Simulate delivery movement
    function simulateDelivery(start, end) {
        // Create a delivery driver marker
        const driverMarker = new google.maps.Marker({
            position: start,
            map: map,
            title: 'Delivery Driver',
            icon: {
                url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                scaledSize: new google.maps.Size(40, 40)
            },
            zIndex: 999
        });
        
        // Get route points
        directionsService.route({
            origin: start,
            destination: end,
            travelMode: google.maps.TravelMode.DRIVING
        }, (response, status) => {
            if (status === 'OK') {
                const route = response.routes[0];
                const path = route.overview_path;
                
                // Animate driver along the path
                let i = 0;
                const interval = setInterval(() => {
                    if (i >= path.length) {
                        clearInterval(interval);
                        return;
                    }
                    
                    driverMarker.setPosition(path[i]);
                    i++;
                }, 1000);
            }
        });
    }
    
    // Refresh location data every 30 seconds
    setInterval(() => {
        // In a real application, you would fetch the latest location data from the server
        // For this demo, we'll just simulate movement
        
        // Get current position
        const currentPosition = deliveryMarker.getPosition();
        
        // Simulate small movement
        const newLat = currentPosition.lat() + (Math.random() - 0.5) * 0.001;
        const newLng = currentPosition.lng() + (Math.random() - 0.5) * 0.001;
        
        // Update marker position
        deliveryMarker.setPosition({ lat: newLat, lng: newLng });
        
        // Recalculate route
        calculateRoute(restaurantMarker.getPosition(), deliveryMarker.getPosition());
    }, 30000);
</script>

<!-- Include Google Maps API if you have an API key -->
@if(config('services.google_maps.api_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&callback=initMap" async defer></script>
@else
    <script src="https://maps.googleapis.com/maps/api/js?callback=initMap" async defer></script>
@endif
@endpush

@push('styles')
<style>
    .status-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #e9f5ff;
        color: #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .status-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #dee2e6;
        margin: 0 auto 5px;
    }
    
    .status-dot.active {
        background-color: #0d6efd;
    }
    
    .status-text {
        font-size: 0.8rem;
    }
</style>
@endpush