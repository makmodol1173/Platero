<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Platero') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #333;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 120px 0;
            margin-bottom: 60px;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #FF6B6B;
        }
        
        .section-title {
            position: relative;
            margin-bottom: 40px;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 3px;
            background-color: #FF6B6B;
        }
        
        .restaurant-card {
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }
        
        .restaurant-card:hover {
            transform: translateY(-10px);
        }
        
        .restaurant-card img {
            height: 200px;
            object-fit: cover;
        }
        
        .cta-section {
            background-color: #f8f9fa;
            padding: 80px 0;
        }
        
        .step-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: #FF6B6B;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .btn-primary {
            background-color: #FF6B6B;
            border-color: #FF6B6B;
        }
        
        .btn-primary:hover {
            background-color: #ff5252;
            border-color: #ff5252;
        }
        
        .btn-outline-primary {
            color: #FF6B6B;
            border-color: #FF6B6B;
        }
        
        .btn-outline-primary:hover {
            background-color: #FF6B6B;
            border-color: #FF6B6B;
        }
        
        footer {
            background-color: #343a40;
            color: white;
            padding: 60px 0 30px;
        }
        
        footer a {
            color: #adb5bd;
            text-decoration: none;
        }
        
        footer a:hover {
            color: white;
        }
        
        .social-icons a {
            display: inline-block;
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background-color: #FF6B6B;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <span style="color: #FF6B6B; font-weight: 700;">{{ config('app.name', 'Platero') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#restaurants">Restaurants</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#join">Join Us</a>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                        @endif

                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link btn btn-primary text-white px-3" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                {{-- Profile Link --}}
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    {{ __('Profile') }}
                                </a>

                                {{-- Logout Link --}}
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">Delicious Food, Delivered Fast</h1>
            <p class="lead mb-5">Order from your favorite local restaurants with just a few taps and enjoy the best meals delivered to your doorstep.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-lg px-4">Explore Restaurants</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4">Sign Up</a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container">
            <h2 class="text-center section-title">Why Choose Us</h2>
            <div class="row g-4 py-5">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h3>Wide Selection</h3>
                        <p class="text-muted">Choose from hundreds of local restaurants offering a variety of cuisines to satisfy any craving.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3>Fast Delivery</h3>
                        <p class="text-muted">Get your food delivered quickly with our efficient delivery system and real-time order tracking.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Secure Payments</h3>
                        <p class="text-muted">Pay securely online with multiple payment options including credit cards and mobile banking.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5 bg-light" id="how-it-works">
        <div class="container">
            <h2 class="text-center section-title">How It Works</h2>
            <div class="row g-4 py-5">
                <div class="col-md-4">
                    <div class="card h-100 step-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number">1</div>
                            <h3>Choose a Restaurant</h3>
                            <p class="text-muted">Browse through our extensive list of restaurants and select your favorite.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 step-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number">2</div>
                            <h3>Select Your Meal</h3>
                            <p class="text-muted">Explore the menu and add your favorite dishes to your cart.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 step-card">
                        <div class="card-body text-center p-4">
                            <div class="step-number">3</div>
                            <h3>Enjoy Your Food</h3>
                            <p class="text-muted">Complete your order, track delivery, and enjoy delicious food at your doorstep.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants Section -->
    <section class="py-5" id="restaurants">
        <div class="container">
            <h2 class="text-center section-title">Featured Restaurants</h2>
            <div class="row py-5">
                <div class="col-md-4">
                    <div class="card restaurant-card h-100">
                        <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1374&q=80" class="card-img-top" alt="Restaurant">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Burger House</h5>
                                <span class="badge bg-success">4.8 ★</span>
                            </div>
                            <p class="card-text text-muted">Delicious burgers, fries, and milkshakes</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-motorcycle me-1"></i> 25-35 min</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Menu</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card restaurant-card h-100">
                        <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="card-img-top" alt="Restaurant">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Pizza Palace</h5>
                                <span class="badge bg-success">4.6 ★</span>
                            </div>
                            <p class="card-text text-muted">Authentic Italian pizzas and pastas</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-motorcycle me-1"></i> 30-45 min</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Menu</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card restaurant-card h-100">
                        <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" class="card-img-top" alt="Restaurant">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0">Green Bowl</h5>
                                <span class="badge bg-success">4.7 ★</span>
                            </div>
                            <p class="card-text text-muted">Healthy salads and grain bowls</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-motorcycle me-1"></i> 20-30 min</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">View Menu</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('restaurants.index') }}" class="btn btn-primary btn-lg">View All Restaurants</a>
            </div>
        </div>
    </section>

    <!-- CTA Section for Restaurant Owners -->
    <section class="cta-section" id="join">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Are You a Restaurant Owner?</h2>
                    <p class="lead mb-4">Join our platform to reach more customers, increase your sales, and grow your business with our easy-to-use restaurant management system.</p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Expand your customer base</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Manage orders efficiently</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Increase your revenue</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Get insights with detailed analytics</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Register Your Restaurant</a>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="https://images.unsplash.com/photo-1552566626-52f8b828add9?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Restaurant Owner" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="text-white mb-4">{{ config('app.name', 'Platero') }}</h5>
                    <p>Connecting hungry customers with the best local restaurants. Order delicious food online and get it delivered to your doorstep.</p>
                    <div class="social-icons mt-4">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Home</a></li>
                        <li class="mb-2"><a href="#features">Features</a></li>
                        <li class="mb-2"><a href="#how-it-works">How It Works</a></li>
                        <li class="mb-2"><a href="#restaurants">Restaurants</a></li>
                        <li class="mb-2"><a href="#join">Join Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-4 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">For Restaurants</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Join as Partner</a></li>
                        <li class="mb-2"><a href="#">Restaurant App</a></li>
                        <li class="mb-2"><a href="#">Pricing</a></li>
                        <li class="mb-2"><a href="#">Marketing</a></li>
                        <li class="mb-2"><a href="#">Resources</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <h5 class="text-white mb-4">Contact Us</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> 123 Food Street, Noakhali, Bangladesh</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i> info@platero.com</li>
                    </ul>
                    <div class="mt-4">
                        <h6 class="text-white mb-3">Subscribe to our Newsletter</h6>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Your Email">
                            <button class="btn btn-primary" type="button">Subscribe</button>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-5 mb-4" style="background-color: rgba(255,255,255,0.1);">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name', 'Platero') }}. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-muted me-3">Privacy Policy</a>
                    <a href="#" class="text-muted me-3">Terms of Service</a>
                    <a href="#" class="text-muted">Cookies Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>