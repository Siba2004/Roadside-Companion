<?php
include_once 'navbar.php';
require_once '../dbcon.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=booking-emergency-repairs.php");
    exit();
}

// Get service details from URL or set defaults
$service_type = isset($_GET['service']) ? $_GET['service'] : 'emergency-repairs';
$service_name = "Emergency On-Site Repairs";
$base_price = 499; // Starting price
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Emergency Repairs - RoadSide Companion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --primary: #0d6efd;
            --dark-blue: #0b5ed7;
            --success: #20B2AA;
            --warning: #FFA500;
            --dark-overlay: rgba(0,0,0,0.75);
        }

        * { font-family: 'Poppins', sans-serif; }
        
        body { 
            background: url('vehiclebg.png') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            color: white;
            position: relative;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark-overlay);
            z-index: 0;
        }

        .booking-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            padding: 100px 0 50px;
        }

        /* Navbar styles (same as before) */
        .navbar { 
            background: rgba(0,0,0,0.65); 
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 0; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.8);
            z-index: 1000;
        }
        .navbar-brand { 
            font-weight: 600; 
            font-size: 1.4rem; 
            color: white !important; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            font-family: 'Orbitron', sans-serif;
        }
        .navbar-brand i { 
            color: var(--primary); 
            font-size: 1.8rem;
        }

        /* Booking Form Card */
        .booking-card {
            background: rgba(0,0,0,0.75);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 0 40px rgba(0,0,0,0.9);
            margin-bottom: 30px;
        }

        .page-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem;
            color: white;
            margin-bottom: 30px;
            text-align: center;
            text-shadow: 0 0 15px rgba(13,110,253,0.5);
        }

        .service-badge {
            background: var(--primary);
            color: white;
            padding: 8px 25px;
            border-radius: 30px;
            display: inline-block;
            margin-bottom: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Location Picker */
        .location-section {
            background: rgba(255,255,255,0.05);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .location-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .location-header i {
            font-size: 1.8rem;
            color: var(--primary);
            margin-right: 15px;
        }

        .location-header h3 {
            color: white;
            margin: 0;
            font-size: 1.3rem;
        }

        .location-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn-location {
            background: rgba(13,110,253,0.1);
            border: 2px solid var(--primary);
            color: white;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
            flex: 1;
            min-width: 200px;
        }

        .btn-location:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(13,110,253,0.4);
            color: white;
        }

        .btn-location i {
            margin-right: 10px;
        }

        .location-display {
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 15px 20px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .location-display i {
            color: var(--success);
            font-size: 1.2rem;
        }

        .location-display span {
            color: white;
            font-size: 1rem;
        }

        #locationText {
            flex: 1;
        }

        /* Map */
        #map {
            height: 300px;
            width: 100%;
            border-radius: 15px;
            margin-top: 20px;
            border: 2px solid var(--primary);
            z-index: 1;
        }

        /* Form Fields */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            color: white;
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .form-group label i {
            color: var(--primary);
            margin-right: 8px;
        }

        .form-control, .form-select {
            background: rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            padding: 12px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(0,0,0,0.7);
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(13,110,253,0.3);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Service Options */
        .service-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px 0;
        }

        .service-option {
            flex: 1;
            min-width: 150px;
        }

        .service-option input[type="radio"] {
            display: none;
        }

        .service-option label {
            display: block;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px 15px;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            margin: 0;
        }

        .service-option input[type="radio"]:checked + label {
            background: rgba(13,110,253,0.2);
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(13,110,253,0.3);
            transform: translateY(-3px);
        }

        .service-option label i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
            display: block;
        }

        .service-option label .price {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            color: white;
            margin: 5px 0;
        }

        .service-option label .desc {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
        }

        /* Price Summary */
        .price-summary {
            background: rgba(13,110,253,0.1);
            border: 1px solid var(--primary);
            border-radius: 15px;
            padding: 25px;
            margin: 30px 0;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .price-row.total {
            border-bottom: none;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary);
            padding-top: 15px;
        }

        /* Continue Button */
        .btn-continue {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 10px;
            width: 100%;
            transition: 0.3s;
            cursor: pointer;
        }

        .btn-continue:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 0 30px rgba(13,110,253,0.4);
        }

        .btn-continue:disabled {
            background: rgba(255,255,255,0.2);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Vehicle Info */
        .vehicle-info {
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .booking-card {
                padding: 25px;
            }
            
            .location-buttons {
                flex-direction: column;
            }
            
            .service-options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-tools me-2"></i>RoadSide Companion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php">My Account</a></li>
                <li class="nav-item ms-2">
                    <a class="btn btn-outline-primary" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="booking-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="booking-card" data-aos="fade-up">
                    
                    <!-- Service Badge -->
                    <div class="text-center">
                        <span class="service-badge">
                            <i class="fas fa-tools me-2"></i>Emergency On-Site Repairs
                        </span>
                    </div>

                    <h1 class="page-title">Book Your Service</h1>

                    <form id="bookingForm" action="checkout.php" method="POST">
                        
                        <!-- LOCATION SECTION (matches screenshot 1) -->
                        <div class="location-section" data-aos="fade-up">
                            <div class="location-header">
                                <i class="fas fa-map-marker-alt"></i>
                                <h3>Your Location</h3>
                            </div>
                            
                            <div class="location-buttons">
                                <button type="button" class="btn-location" id="detectLocationBtn">
                                    <i class="fas fa-location-dot"></i> Detect My Location
                                </button>
                                <button type="button" class="btn-location" id="chooseOnMapBtn">
                                    <i class="fas fa-map"></i> Choose on Map
                                </button>
                            </div>

                            <!-- Location Display -->
                            <div class="location-display" id="locationDisplay" style="display: none;">
                                <i class="fas fa-check-circle"></i>
                                <span id="locationText"></span>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editLocation()">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>

                            <!-- Map (hidden by default) -->
                            <div id="map" style="display: none;"></div>

                            <!-- Hidden inputs for location data -->
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="address" id="address">
                        </div>

                        <!-- VEHICLE INFORMATION -->
                        <div class="vehicle-info" data-aos="fade-up" data-aos-delay="100">
                            <h4 style="color: white; margin-bottom: 20px;">
                                <i class="fas fa-car me-2" style="color: var(--primary);"></i>Vehicle Information
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-car-side"></i> Vehicle Type</label>
                                        <select class="form-select" name="vehicle_type" required>
                                            <option value="">Select Vehicle Type</option>
                                            <option value="car">Car / Sedan</option>
                                            <option value="suv">SUV</option>
                                            <option value="hatchback">Hatchback</option>
                                            <option value="motorcycle">Motorcycle</option>
                                            <option value="truck">Truck</option>
                                            <option value="boat">Boat (Special Towing)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-hashtag"></i> Vehicle Number</label>
                                        <input type="text" class="form-control" name="vehicle_number" placeholder="e.g., MH01AB1234" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-tag"></i> Vehicle Model</label>
                                        <input type="text" class="form-control" name="vehicle_model" placeholder="e.g., Hyundai i20" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><i class="fas fa-calendar"></i> Year</label>
                                        <input type="text" class="form-control" name="vehicle_year" placeholder="e.g., 2022">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SERVICE TYPE SELECTION -->
                        <div data-aos="fade-up" data-aos-delay="200">
                            <h4 style="color: white; margin-bottom: 20px;">
                                <i class="fas fa-tools me-2" style="color: var(--primary);"></i>Select Service Package
                            </h4>

                            <div class="service-options">
                                <div class="service-option">
                                    <input type="radio" name="service_package" id="basic" value="basic" checked>
                                    <label for="basic">
                                        <i class="fas fa-search"></i>
                                        <span class="price">₹499</span>
                                        <span class="desc">Basic Diagnostic</span>
                                    </label>
                                </div>
                                <div class="service-option">
                                    <input type="radio" name="service_package" id="standard" value="standard">
                                    <label for="standard">
                                        <i class="fas fa-tools"></i>
                                        <span class="price">₹999</span>
                                        <span class="desc">Standard Repair</span>
                                    </label>
                                </div>
                                <div class="service-option">
                                    <input type="radio" name="service_package" id="premium" value="premium">
                                    <label for="premium">
                                        <i class="fas fa-shield-alt"></i>
                                        <span class="price">₹1,499</span>
                                        <span class="desc">Premium Package</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- ADDITIONAL SERVICES -->
                        <div class="row mt-4" data-aos="fade-up" data-aos-delay="250">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-clock"></i> Arrival Within</label>
                                    <select class="form-select" name="arrival_time">
                                        <option value="30">30 mins (Standard)</option>
                                        <option value="15">15 mins (Priority - ₹200 extra)</option>
                                        <option value="45">45 mins (Standard)</option>
                                        <option value="60">60 mins (Standard)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><i class="fas fa-phone"></i> Contact Number</label>
                                    <input type="tel" class="form-control" name="contact" placeholder="Your phone number" required>
                                </div>
                            </div>
                        </div>

                        <!-- NOTE (matches screenshot 1) -->
                        <div class="form-group" data-aos="fade-up" data-aos-delay="300">
                            <label><i class="fas fa-pen"></i> Note (optional)</label>
                            <textarea class="form-control" name="note" placeholder="Write a description here... Tell us more about the issue, e.g., 'Engine making strange noise', 'Car won't start', etc."></textarea>
                        </div>

                        <!-- PRICE SUMMARY (updates dynamically) -->
                        <div class="price-summary" data-aos="fade-up" data-aos-delay="350">
                            <h4 style="color: white; margin-bottom: 20px;">Price Summary</h4>
                            <div class="price-row">
                                <span>Service Package:</span>
                                <span id="summaryService">Basic Diagnostic - ₹499</span>
                            </div>
                            <div class="price-row" id="priorityRow" style="display: none;">
                                <span>Priority Service:</span>
                                <span id="priorityFee">+ ₹200</span>
                            </div>
                            <div class="price-row">
                                <span>GST (18%):</span>
                                <span id="summaryGST">₹89.82</span>
                            </div>
                            <div class="price-row total">
                                <span>Total Amount:</span>
                                <span id="summaryTotal">₹588.82</span>
                            </div>
                        </div>

                        <!-- Hidden fields for price calculation -->
                        <input type="hidden" name="base_price" id="base_price" value="499">
                        <input type="hidden" name="priority_fee" id="priority_fee" value="0">
                        <input type="hidden" name="total_amount" id="total_amount" value="588.82">
                        <input type="hidden" name="service_name" value="Emergency On-Site Repairs">
                        <input type="hidden" name="service_type" value="<?php echo $service_type; ?>">

                        <!-- Continue Button -->
                        <button type="submit" class="btn-continue" id="continueBtn" disabled>
                            <i class="fas fa-arrow-right me-2"></i>Continue to Checkout
                        </button>

                        <p class="text-center mt-3" style="color: rgba(255,255,255,0.6); font-size: 0.9rem;">
                            <i class="fas fa-shield-alt me-1"></i>Your information is secure and encrypted
                        </p>
                    </form>
                </div>

                <!-- Service Information Card (matches screenshot 2 style) -->
                <div class="booking-card mt-4" data-aos="fade-up" style="background: rgba(0,0,0,0.6);">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 style="color: var(--primary); font-weight: 600;">Boat Towing Services</h3>
                            <p style="color: rgba(255,255,255,0.9);">Our specialized towing services include boat towing services that help transport your boat from one place to another.</p>
                            
                            <div class="row mt-3">
                                <div class="col-4">
                                    <small style="color: rgba(255,255,255,0.7);">Arrival Within</small>
                                    <p style="color: white; font-weight: 600;">30-45 mins</p>
                                </div>
                                <div class="col-4">
                                    <small style="color: rgba(255,255,255,0.7);">Starting Price</small>
                                    <p style="color: white; font-weight: 600;">₹1,500 Per Service</p>
                                </div>
                                <div class="col-4">
                                    <small style="color: rgba(255,255,255,0.7);">Availability</small>
                                    <p style="color: white; font-weight: 600;">24/7 Non-stop</p>
                                </div>
                            </div>
                            
                            <a href="booking.php?service=boat-towing" class="btn btn-primary mt-2">
                                Request Now <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                        <div class="col-md-4 text-center">
                            <i class="fas fa-ship" style="font-size: 5rem; color: var(--primary); opacity: 0.5;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SOS BUTTON -->
<button class="sos-btn" id="sosBtn" style="position: fixed; bottom: 30px; right: 30px; width: 70px; height: 70px; background: #DC2626; border: none; border-radius: 50%; color: white; font-weight: 700; font-size: .6rem; z-index: 9999;">
    <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
    <span>SOS</span>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 900, once: true, offset: 80 });

    // Variables
    let map, marker, isMapVisible = false;
    const locationDisplay = document.getElementById('locationDisplay');
    const locationText = document.getElementById('locationText');
    const latitude = document.getElementById('latitude');
    const longitude = document.getElementById('longitude');
    const address = document.getElementById('address');
    const mapDiv = document.getElementById('map');
    const continueBtn = document.getElementById('continueBtn');
    
    // Price calculation elements
    const basePrice = document.getElementById('base_price');
    const priorityFee = document.getElementById('priority_fee');
    const totalAmount = document.getElementById('total_amount');
    const summaryService = document.getElementById('summaryService');
    const summaryGST = document.getElementById('summaryGST');
    const summaryTotal = document.getElementById('summaryTotal');
    const priorityRow = document.getElementById('priorityRow');

    // ========== LOCATION FUNCTIONS ==========
    
    // Detect Location
    document.getElementById('detectLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Detecting...';
            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                enableHighAccuracy: true,
                timeout: 10000
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    });

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        
        latitude.value = lat;
        longitude.value = lon;
        
        // Reverse geocoding to get address
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            const addr = data.display_name || '';
            address.value = addr;
            
            // Format location display
            const area = data.address?.suburb || data.address?.neighbourhood || data.address?.road || '';
            const city = data.address?.city || data.address?.town || data.address?.village || '';
            const state = data.address?.state || '';
            
            locationText.innerHTML = `<strong>📍 ${area}, ${city}</strong><br><small>${state}</small>`;
            locationDisplay.style.display = 'flex';
            
            // Update map if visible
            if (map) {
                map.setView([lat, lon], 15);
                if (marker) marker.setLatLng([lat, lon]);
            }
            
            // Enable continue button
            continueBtn.disabled = false;
            
            document.getElementById('detectLocationBtn').innerHTML = '<i class="fas fa-location-dot"></i> Detect My Location';
        })
        .catch(error => {
            console.error('Reverse geocoding error:', error);
            locationText.innerHTML = `📍 Lat: ${lat.toFixed(4)}, Lon: ${lon.toFixed(4)}`;
            locationDisplay.style.display = 'flex';
            continueBtn.disabled = false;
            document.getElementById('detectLocationBtn').innerHTML = '<i class="fas fa-location-dot"></i> Detect My Location';
        });
    }

    function showError(error) {
        let message = '';
        switch(error.code) {
            case error.PERMISSION_DENIED:
                message = "Location permission denied. Please enable location access.";
                break;
            case error.POSITION_UNAVAILABLE:
                message = "Location information unavailable.";
                break;
            case error.TIMEOUT:
                message = "Location request timed out.";
                break;
            default:
                message = "An unknown error occurred.";
        }
        alert(message);
        document.getElementById('detectLocationBtn').innerHTML = '<i class="fas fa-location-dot"></i> Detect My Location';
    }

    // Choose on Map
    document.getElementById('chooseOnMapBtn').addEventListener('click', function() {
        if (!map) {
            mapDiv.style.display = 'block';
            // Initialize map
            map = L.map('map').setView([20.5937, 78.9629], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            marker = L.marker([20.5937, 78.9629], { draggable: true }).addTo(map);
            
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                updateLocationFromMap(pos.lat, pos.lng);
            });
            
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateLocationFromMap(e.latlng.lat, e.latlng.lng);
            });
        }
        
        mapDiv.style.display = mapDiv.style.display === 'none' ? 'block' : 'none';
        if (mapDiv.style.display === 'block') {
            setTimeout(() => map.invalidateSize(), 100);
        }
    });

    function updateLocationFromMap(lat, lon) {
        latitude.value = lat;
        longitude.value = lon;
        
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`)
        .then(response => response.json())
        .then(data => {
            address.value = data.display_name || '';
            const area = data.address?.suburb || data.address?.neighbourhood || data.address?.road || '';
            const city = data.address?.city || data.address?.town || data.address?.village || '';
            locationText.innerHTML = `<strong>📍 ${area}, ${city}</strong>`;
            locationDisplay.style.display = 'flex';
            continueBtn.disabled = false;
        });
    }

    function editLocation() {
        locationDisplay.style.display = 'none';
        latitude.value = '';
        longitude.value = '';
        address.value = '';
        continueBtn.disabled = true;
    }

    // ========== PRICE CALCULATION ==========
    
    function calculatePrice() {
        const packageRadios = document.getElementsByName('service_package');
        let selectedPackage = 'basic';
        let price = 499;
        
        for (let radio of packageRadios) {
            if (radio.checked) {
                selectedPackage = radio.value;
                break;
            }
        }
        
        switch(selectedPackage) {
            case 'basic':
                price = 499;
                summaryService.innerText = 'Basic Diagnostic - ₹499';
                break;
            case 'standard':
                price = 999;
                summaryService.innerText = 'Standard Repair - ₹999';
                break;
            case 'premium':
                price = 1499;
                summaryService.innerText = 'Premium Package - ₹1,499';
                break;
        }
        
        basePrice.value = price;
        
        // Check for priority service
        const arrivalTime = document.querySelector('select[name="arrival_time"]');
        let priority = 0;
        
        if (arrivalTime.value === '15') {
            priority = 200;
            priorityRow.style.display = 'flex';
            priorityFee.innerText = '+ ₹200';
        } else {
            priority = 0;
            priorityRow.style.display = 'none';
        }
        
        priorityFee.value = priority;
        
        // Calculate GST and total
        const subtotal = price + priority;
        const gst = Math.round(subtotal * 0.18 * 100) / 100;
        const total = subtotal + gst;
        
        summaryGST.innerText = '₹' + gst.toFixed(2);
        summaryTotal.innerText = '₹' + total.toFixed(2);
        totalAmount.value = total.toFixed(2);
    }

    // Event listeners for price calculation
    document.querySelectorAll('input[name="service_package"]').forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });
    
    document.querySelector('select[name="arrival_time"]').addEventListener('change', calculatePrice);

    // SOS Button
    document.getElementById('sosBtn').addEventListener('click', function() {
        if (confirm('🚨 EMERGENCY SOS 🚨\n\nDo you need immediate roadside assistance?')) {
            alert('Emergency services notified! Help is on the way.');
            // Auto-detect location for SOS
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    console.log('SOS Location:', pos.coords.latitude, pos.coords.longitude);
                });
            }
        }
    });

    // Form validation before submit
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        if (!latitude.value || !longitude.value) {
            e.preventDefault();
            alert('Please select your location first!');
            return false;
        }
        
        const vehicleNumber = document.querySelector('input[name="vehicle_number"]').value;
        if (!vehicleNumber) {
            e.preventDefault();
            alert('Please enter vehicle number');
            return false;
        }
        
        return true;
    });

    // Navbar shadow
    window.addEventListener('scroll', () => {
        document.querySelector('.navbar').style.boxShadow =
            window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)';
    });
</script>

<?php include_once 'footer.php'; ?>
</body>
</html>