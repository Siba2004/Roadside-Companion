<?php
include_once 'navbar.php';
require_once '../dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Roadside Repairs | RoadSide Companion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0d6efd;
            --primary-dark: #0b5ed7;
            --secondary: #6c757d;
            --success: #20B2AA;
            --warning: #FFA500;
            --danger: #DC2626;
            --card-bg: rgba(0,0,0,0.65);
            --border: rgba(255,255,255,0.1);
            --dark-overlay: rgba(6, 6, 6, 0.95);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

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

        .page-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 15px 0;
            box-shadow: 0 2px 15px rgba(0,0,0,0.8);
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

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            margin: 0 10px;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: .85rem;
            letter-spacing: 1px;
            padding: 8px 20px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 0 15px var(--primary);
            transform: scale(1.05);
        }

        /* Service Page Header */
        .service-header {
            margin-top: 100px;
            padding: 20px 0 40px;
        }

        .service-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            box-shadow: 0 0 20px rgba(13,110,253,0.3);
        }

        .service-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .service-header p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.9);
            max-width: 700px;
            margin: 0 auto;
        }

        /* Main Service Card */
        .service-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }

        /* Service Image Section */
        .service-image-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .service-main-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 20px;
            transition: transform 0.5s ease;
        }

        .service-main-image:hover {
            transform: scale(1.05);
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            padding: 20px;
            text-align: center;
        }

        .image-overlay h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i {
            color: var(--primary);
            width: 20px;
        }

        .form-control, .form-select {
            background: rgba(255,255,255,0.1);
            border: 1px solid var(--border);
            color: white;
            padding: 12px 15px;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15);
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(13,110,253,0.3);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255,255,255,0.5);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Location Input with Icon */
        .location-input-wrapper {
            position: relative;
        }

        .location-input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            z-index: 2;
        }

        .location-input-wrapper .form-control {
            padding-left: 45px;
        }

        .detect-location-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(13,110,253,0.2);
            border: none;
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .detect-location-btn:hover {
            background: var(--primary);
            color: white;
        }

        /* Availability Card */
        .availability-card {
            background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,110,253,0.05));
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
        }

        .availability-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border);
        }

        .availability-header i {
            font-size: 2rem;
            color: var(--primary);
        }

        .availability-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .machine-status {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .machine-status:last-child {
            border-bottom: none;
        }

        .machine-name {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .machine-name i {
            width: 25px;
            color: var(--primary);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-available {
            background: rgba(32, 178, 170, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }

        .status-busy {
            background: rgba(220, 38, 38, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        /* Note Section */
        .note-section {
            background: rgba(255, 165, 0, 0.1);
            border-left: 4px solid var(--warning);
            border-radius: 12px;
            padding: 15px 20px;
            margin-top: 2px;
            margin-bottom:15px;
        }

        .note-section i {
            color: var(--warning);
            margin-right: 10px;
        }

        .note-section p {
            margin: 0;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            padding: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 12px;
            font-size: 1rem;
            transition: 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(13,110,253,0.4);
        }

        /* Service Features */
        .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .feature-item i {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(13,110,253,0.1);
            border-radius: 10px;
            color: var(--primary);
        }

        .feature-item span {
            flex: 1;
        }

        /* Back Button */
        .back-btn {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: 0.3s;
            z-index: 999;
            text-decoration: none;
        }

        .back-btn:hover {
            background: var(--primary);
            transform: scale(1.1);
            color: white;
        }

        /* SOS Button */
        .sos-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background: var(--danger);
            border: none;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: .6rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 20px rgba(220,38,38,.4);
            z-index: 999;
            cursor: pointer;
            transition: .2s;
            border: 2px solid rgba(255,255,255,0.3);
        }

        .sos-btn i {
            font-size: 1.5rem;
            margin-bottom: 3px;
        }

        .sos-btn:hover {
            background: #B91C1C;
            transform: scale(1.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .service-header h1 {
                font-size: 1.8rem;
            }
            .service-card {
                padding: 20px;
            }
            .service-main-image {
                height: 200px;
            }
            .back-btn, .sos-btn {
                width: 45px;
                height: 45px;
            }
            .sos-btn {
                width: 55px;
                height: 55px;
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .navbar-brand i {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body>
<div class="page-container">

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
                <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#why-us">Why Us</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#reviews">Reviews</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
                <li class="nav-item ms-2">
                    <a class="btn btn-primary" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- BACK BUTTON -->
<a href="index.php#emergency-assistance" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>

<!-- MAIN CONTENT -->
<div class="container" style="margin-top: 100px; margin-bottom: 80px;">
    
    <!-- Header -->
    <div class="text-center service-header" data-aos="fade-up">
        <h1>Emergency Roadside Repairs</h1>
        <p>Professional on-site repair services available round the clock. Get help within minutes!</p>
    </div>

    <div class="row g-4">
        <!-- Left Column - Form -->
        <div class="col-lg-7" data-aos="fade-right">
            <div class="service-card">
                <!-- Service Image -->
                <div class="service-image-wrapper">
                    <img src="pic/emergency-repair-service.jpg" 
                         alt="Emergency Roadside Repair Service" 
                         class="service-main-image"
                         onerror="this.src='https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=1170&q=80'">
                    <div class="image-overlay">
                        <h3><i class="fas fa-tools"></i> On-Site Emergency Repairs</h3>
                    </div>
                </div>

                <!-- Booking Form -->
                <form action="process_booking.php" method="POST" id="bookingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <input type="text" class="form-control" name="full_name" placeholder="Enter your full name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <input type="tel" class="form-control" name="phone" placeholder="Enter mobile number" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-car"></i> Vehicle Type
                                </label>
                                <select class="form-select" name="vehicle_type" required>
                                    <option value="">Select Vehicle Type</option>
                                    <option value="car">Car (4 Wheeler)</option>
                                    <option value="suv">SUV / MUV</option>
                                    <option value="bike">Bike (2 Wheeler)</option>
                                    <option value="auto">Auto Rickshaw</option>
                                    <option value="truck">Light Truck</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i>Vehicle Number
                                </label>
                                <input type="email" class="form-control" name="email" placeholder="Enter Vehicle Number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-car-side"></i> Vehicle Model
                                </label>
                                <input type="text" class="form-control" name="vehicle_model" placeholder="e.g., Maruti Suzuki Swift">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt"></i> Service Date
                                </label>
                                <input type="date" class="form-control" name="service_date" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-map-marker-alt"></i> Location
                                </label>
                                <div class="location-input-wrapper">
                                    <i class="fas fa-location-dot"></i>
                                    <input type="text" class="form-control" id="locationInput" name="location" placeholder="Enter your current location" required>
                                    <button type="button" class="detect-location-btn" onclick="detectLocation()">
                                        Detect
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-tools"></i> Type of Repair Needed
                                </label>
                                <select class="form-select" name="repair_type" id="repairType" onchange="updateAvailability()" required>
                                    <option value="">Select repair service</option>
                                    <option value="engine">Engine Troubleshooting</option>
                                    <option value="electrical">Electrical System Repair</option>
                                    <option value="alternator">Alternator / Starter Motor</option>
                                    <option value="coolant">Coolant / Fluid Leak</option>
                                    <option value="belt">Belt / Hose Replacement</option>
                                    <option value="sensor">Sensor / Fuse Replacement</option>
                                    <option value="general">General Mechanical Repair</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-align-left"></i> Describe Your Issue
                                </label>
                                <textarea class="form-control" name="issue_description" rows="4" placeholder="Please describe the problem you're facing in detail..." required></textarea>
                            </div>
                        </div>
                    </div>

                   

                    <!-- Note Section -->
                    <div class="note-section">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong>
                        <p>Our technician will arrive at your location within 10-15 minutes. Emergency service charges apply for immediate assistance</p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary submit-btn">
                        <i class="fas fa-phone-alt me-2"></i> Request Emergency Service
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column - Information -->
        <div class="col-lg-5" data-aos="fade-left">
            <div class="service-card">
                <h4 class="mb-3">
                    <i class="fas fa-star-of-life text-primary me-2"></i>
                    Service Includes
                </h4>
                <div class="feature-item">
                    <i class="fas fa-clock"></i>
                    <span>24/7 Emergency Response</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>15-25 Min Average Response Time</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-certificate"></i>
                    <span>Certified & Experienced Mechanics</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Real-Time Mechanic Tracking</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-hand-holding-usd"></i>
                    <span>Transparent Pricing - No Hidden Charges</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-warranty"></i>
                    <span>30-Day Warranty on Repairs</span>
                </div>

                <hr class="my-4" style="border-color: var(--border);">

                <h4 class="mb-3">
                    <i class="fas fa-tag text-primary me-2"></i>
                    Pricing
                </h4>
                <div class="d-flex justify-content-between py-2">
                    <span>On-Site Diagnosis</span>
                    <span class="text-primary fw-bold">₹299</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span>Minor Mechanical Repair</span>
                    <span class="text-primary fw-bold">₹499 - ₹999</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span>Electrical System Repair</span>
                    <span class="text-primary fw-bold">₹399 - ₹799</span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span>Engine Troubleshooting</span>
                    <span class="text-primary fw-bold">₹599 - ₹1499</span>
                </div>
                <div class="mt-3 p-3 bg-primary bg-opacity-10 rounded-3">
                    <div class="text-center">
                        <i class="fas fa-gift fa-2x text-primary mb-2"></i>
                        <h5 class="mb-0">First Service Offer</h5>
                        <p class="mb-0"><span class="h3 text-primary">30% OFF</span> on all repairs</p>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Card -->
            <div class="service-card mt-4">
                <h4 class="mb-3">
                    <i class="fas fa-phone-alt text-primary me-2"></i>
                    Need Immediate Help?
                </h4>
                <div class="text-center">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h2 class="mb-2" style="font-family: monospace;">1800-123-4567</h2>
                    <p class="mb-3">24/7 Emergency Helpline</p>
                    <a href="tel:18001234567" class="btn btn-outline-primary w-100">
                        <i class="fas fa-phone-alt me-2"></i>Call Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SOS BUTTON -->
<button class="sos-btn" id="sosBtn">
    <i class="fas fa-exclamation-triangle"></i>
    <span>SOS</span>
</button>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 900, once: true, offset: 80 });

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            navbar.style.boxShadow = window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)';
        }
    });

    // SOS Button
    document.getElementById('sosBtn').addEventListener('click', () => {
        if (confirm('🚨 EMERGENCY SOS 🚨\n\nDo you need immediate roadside assistance?')) {
            alert('Emergency services have been notified! Help is on the way. Our team will call you shortly.');
        }
    });

    // Location Detection
    function detectLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            const area = data.address.suburb || data.address.neighbourhood || data.address.village || "";
                            const city = data.address.city || data.address.town || "";
                            const location = area ? `${area}, ${city}` : city;
                            document.getElementById('locationInput').value = location || "Location detected";
                        })
                        .catch(() => {
                            document.getElementById('locationInput').value = "Location detected";
                        });
                },
                (error) => {
                    if (error.code === 1) {
                        alert("Please enable location access to auto-detect your location.");
                    } else {
                        alert("Unable to detect location. Please enter manually.");
                    }
                }
            );
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    }

    // Update machine availability based on repair type
    function updateAvailability() {
        const repairType = document.getElementById('repairType').value;
        const availabilityDiv = document.getElementById('availabilityContent');
        
        const availabilityData = {
            engine: {
                diagnostic: 'Available',
                engineKit: 'Available',
                electrical: 'Busy',
                battery: 'Available'
            },
            electrical: {
                diagnostic: 'Available',
                engineKit: 'Busy',
                electrical: 'Available',
                battery: 'Available'
            },
            alternator: {
                diagnostic: 'Available',
                engineKit: 'Available',
                electrical: 'Available',
                battery: 'Busy'
            },
            default: {
                diagnostic: 'Available',
                engineKit: 'Available',
                electrical: 'Available',
                battery: 'Available'
            }
        };
        
        const data = availabilityData[repairType] || availabilityData.default;
        
        availabilityDiv.innerHTML = `
            <div class="machine-status">
                <div class="machine-name">
                    <i class="fas fa-microscope"></i>
                    <span>Diagnostic Scanner</span>
                </div>
                <span class="status-badge ${data.diagnostic === 'Available' ? 'status-available' : 'status-busy'}">
                    <i class="fas ${data.diagnostic === 'Available' ? 'fa-check-circle' : 'fa-clock'}"></i> ${data.diagnostic}
                </span>
            </div>
            <div class="machine-status">
                <div class="machine-name">
                    <i class="fas fa-oil-can"></i>
                    <span>Engine Diagnostic Kit</span>
                </div>
                <span class="status-badge ${data.engineKit === 'Available' ? 'status-available' : 'status-busy'}">
                    <i class="fas ${data.engineKit === 'Available' ? 'fa-check-circle' : 'fa-clock'}"></i> ${data.engineKit}
                </span>
            </div>
            <div class="machine-status">
                <div class="machine-name">
                    <i class="fas fa-bolt"></i>
                    <span>Electrical Tester Kit</span>
                </div>
                <span class="status-badge ${data.electrical === 'Available' ? 'status-available' : 'status-busy'}">
                    <i class="fas ${data.electrical === 'Available' ? 'fa-check-circle' : 'fa-clock'}"></i> ${data.electrical}
                </span>
            </div>
            <div class="machine-status">
                <div class="machine-name">
                    <i class="fas fa-car-battery"></i>
                    <span>Battery Analyzer</span>
                </div>
                <span class="status-badge ${data.battery === 'Available' ? 'status-available' : 'status-busy'}">
                    <i class="fas ${data.battery === 'Available' ? 'fa-check-circle' : 'fa-clock'}"></i> ${data.battery}
                </span>
            </div>
        `;
    }
</script>

</body>
</html>
<?php include_once 'footer.php'; ?>