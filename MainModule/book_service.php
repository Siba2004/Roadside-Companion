<?php
session_start();
require_once '../dbcon.php';

// ---- ONLY CUSTOMERS CAN BOOK ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$service_name = isset($_GET['service']) ? $_GET['service'] : '';
$price = isset($_GET['price']) ? floatval($_GET['price']) : 0;
$service_type = isset($_GET['type']) ? $_GET['type'] : 'four_wheeler'; // Get service type

// Fetch complete service details from appropriate table
$service_details = null;

// Determine which table to query based on service type or id
if ($service_id > 0) {
    // Try both tables
    $tables = ['four_wheeler_services', 'two_wheeler_services'];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT *, '$table' as source_table FROM $table WHERE id = ?");
        $stmt->bind_param("i", $service_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $service_details = $row;
            break;
        }
        $stmt->close();
    }
}

// If service not found by ID, try to fetch by name from both tables
if (!$service_details && !empty($service_name)) {
    $tables = ['four_wheeler_services', 'two_wheeler_services'];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT *, '$table' as source_table FROM $table WHERE service_name LIKE ?");
        $search_name = "%$service_name%";
        $stmt->bind_param("s", $search_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $service_details = $row;
            break;
        }
        $stmt->close();
    }
}

// Update variables with fetched data
if ($service_details) {
    $service_name = $service_details['service_name'];
    $price = $service_details['offer_price'];
    $original_price = $service_details['original_price'];
    $description = $service_details['description'];
    // Calculate discount percentage safely
    if ($original_price > 0) {
        $discount_percent = round((($original_price - $price) / $original_price) * 100);
    } else {
        $discount_percent = 0;
    }
    $rating = $service_details['rating'] ?? 4.8;
    $image_path = $service_details['image_path'] ?? null;
    $badge_text = $service_details['badge_text'] ?? null;
    $source_table = $service_details['source_table'];
} else {
    $original_price = $price * 1.3;
    $description = "Professional roadside assistance service at your location.";
    if ($original_price > 0) {
        $discount_percent = round((($original_price - $price) / $original_price) * 100);
    } else {
        $discount_percent = 0;
    }
    $rating = 4.8;
    $image_path = null;
    $badge_text = null;
    $source_table = 'four_wheeler_services';
}

// Generate time slots (24h, every 30 minutes)
$time_slots = [];
for ($h = 0; $h < 24; $h++) {
    $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
    $time_slots[] = "$hour:00";
    $time_slots[] = "$hour:30";
}

// Get user data for pre-filling
$user_data = null;
$stmt = $conn->prepare("SELECT name, phone_number, email FROM users_details WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_date = $_POST['booking_date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';
    $location = trim($_POST['location'] ?? '');
    $vehicle_type = $_POST['vehicle_type'] ?? '';
    $vehicle_number = $_POST['vehicle_number'] ?? '';
    $vehicle_model = $_POST['vehicle_model'] ?? '';
    $special_requests = $_POST['special_requests'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';
    $full_name = $_POST['full_name'] ?? '';

    if (empty($booking_date) || empty($time_slot) || empty($location) || empty($vehicle_type) || empty($contact_phone)) {
        $message = '<div class="alert alert-danger">Please fill all required fields (*).</div>';
    } else {
        // Store booking data in session for mechanic search
        $_SESSION['pending_booking'] = [
            'booking_date' => $booking_date,
            'time_slot' => $time_slot,
            'location' => $location,
            'vehicle_type' => $vehicle_type,
            'vehicle_number' => $vehicle_number,
            'vehicle_model' => $vehicle_model,
            'special_requests' => $special_requests,
            'contact_phone' => $contact_phone,
            'service_name' => $service_name,
            'price' => $price,
            'full_name' => $full_name
        ];
        
        // Redirect to mechanic search page
        header('location: search_mechanic.php');
        exit();
    }
}

include_once 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f0f1a 100%);
            min-height: 100vh;
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
            background: radial-gradient(circle at 20% 50%, rgba(13,110,253,0.08) 0%, transparent 50%);
            z-index: 0;
        }
        
        .main-wrapper {
            position: relative;
            z-index: 1;
            padding: 30px 20px 50px;
        }
        
        .booking-wrapper {
            max-width: 1300px;
            margin: 0 auto;
        }
        
        /* Left Panel - Service Details */
        .service-panel {
            background: rgba(15, 20, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .service-image {
            position: relative;
            height: 300px;
            overflow: hidden;
            background: #1a1a2e;
        }
        
        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .service-image .no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: white;
            font-size: 3rem;
        }
        
        .service-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            padding: 5px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.7rem;
            text-transform: uppercase;
            z-index: 2;
        }
        
        .service-rating {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: rgba(0,0,0,0.7);
            backdrop-filter: blur(10px);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            z-index: 2;
        }
        
        .service-rating i {
            color: #FFD700;
            margin-right: 5px;
        }
        
        .service-content {
            padding: 20px;
            flex: 1;
        }
        
        .service-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: white;
        }
        
        .price-section {
            background: rgba(13,110,253,0.1);
            border-radius: 12px;
            padding: 12px;
            margin: 12px 0;
            text-align: left;
        }
        
        .original-price {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.6);
            text-decoration: line-through;
            margin-right: 8px;
        }
        
        .offer-price {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fd0d0d;
        }
        
        .discount-badge {
            display: inline-block;
            background: #20B2AA;
            padding: 3px 8px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 6px;
        }
        
        .service-description {
            color: rgba(255,255,255,0.8);
            line-height: 1.5;
            margin: 12px 0;
            font-size: 0.85rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 12px 0;
        }
        
        .feature-list li {
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.8rem;
        }
        
        .feature-list li i {
            width: 25px;
            color: #0d6efd;
            font-size: 0.9rem;
        }
        
        /* Right Panel - Booking Form */
        .booking-panel {
            background: rgba(15, 20, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 25px;
            height: 100%;
        }
        
        .booking-panel h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.3rem;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-section {
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .form-section-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #0d6efd;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 5px;
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
        }
        
        .form-label i {
            color: #0d6efd;
            width: 18px;
            font-size: 0.8rem;
        }
        
        .required::after {
            content: " *";
            color: #DC2626;
        }
        
        .form-control, .form-select {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15);
            border-color: #0d6efd;
            box-shadow: 0 0 10px rgba(13,110,253,0.3);
            color: white;
        }
        
        .form-control::placeholder {
            color: rgba(255,255,255,0.4);
            font-size: 0.8rem;
        }
        
        .form-select option {
            background: #1a1a2e;
            color: white;
        }
        
        .row {
            margin-right: -8px;
            margin-left: -8px;
        }
        
        .row > [class*="col-"] {
            padding-right: 8px;
            padding-left: 8px;
        }
        
        .mb-3 {
            margin-bottom: 12px !important;
        }
        
        .location-input-wrapper {
            position: relative;
        }
        
        .location-input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #0d6efd;
            z-index: 2;
            font-size: 0.8rem;
        }
        
        .location-input-wrapper .form-control {
            padding-left: 35px;
        }
        
        .detect-location-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(13,110,253,0.2);
            border: none;
            color: #0d6efd;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .detect-location-btn:hover {
            background: #0d6efd;
            color: white;
        }
        
        .btn-book {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px;
            width: 100%;
            border-radius: 10px;
            font-size: 0.85rem;
            transition: 0.3s;
            margin-top: 15px;
            color: white;
        }
        
        .btn-book:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(13,110,253,0.4);
            color: white;
        }
        
        .summary-box {
            background: rgba(13,110,253,0.08);
            border-radius: 12px;
            padding: 12px;
            margin-top: 15px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 0.85rem;
        }
        
        .summary-total {
            border-top: 1px solid #0d6efd;
            padding-top: 8px;
            margin-top: 6px;
            font-weight: 700;
            font-size: 0.95rem;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 60px;
        }
        
        .alert {
            padding: 8px 12px;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        
        .back-link {
            margin-top: 15px;
            text-align: center;
        }
        
        .back-link a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        
        .back-link a:hover {
            color: white;
        }
        
        @media (max-width: 992px) {
            .service-panel {
                margin-bottom: 20px;
            }
            .main-wrapper {
                padding: 80px 15px 40px;
            }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="booking-wrapper">
        <div class="row g-4">
            <!-- Left Column - Service Details (FIRST COLUMN) -->
            <div class="col-lg-6">
                <div class="service-panel">
                    <div class="service-image">
                        <?php 
                        // Construct correct image path
                        $image_src = null;
                        
                        // Check if image_path exists and is not empty
                        if (!empty($image_path)) {
                            // Try different possible paths
                            $possible_paths = [
                                $image_path, // direct path
                                '../' . $image_path, // with leading ../
                                '../services_pics/' . basename($image_path), // from services_pics folder
                                '../services_pics/four_wheeler_services_pics/' . basename($image_path), // four wheeler folder
                                '../services_pics/two_wheeler_services_pics/' . basename($image_path), // two wheeler folder
                            ];
                            
                            foreach ($possible_paths as $path) {
                                if (file_exists($path)) {
                                    $image_src = $path;
                                    break;
                                }
                            }
                        }
                        
                        // If no image found, use placeholder based on service type
                        if (!$image_src) {
                            $image_src = "https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=400&q=80";
                        }
                        ?>
                        <img src="<?= htmlspecialchars($image_src) ?>" alt="<?= htmlspecialchars($service_name) ?>" 
                             onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=400&q=80';">
                        <?php if (!empty($badge_text)): ?>
                            <div class="service-badge"><?= htmlspecialchars($badge_text) ?></div>
                        <?php endif; ?>
                        <div class="service-rating">
                            <i class="fas fa-star"></i> <?= number_format($rating, 1) ?> 
                            <span style="font-size: 0.7rem;">(1.2k+)</span>
                        </div>
                    </div>
                    <div class="service-content">
                        <h1 class="service-title"><?= htmlspecialchars($service_name) ?></h1>
                        
                        <div class="price-section">
                            <span class="offer-price">₹<?= number_format($price) ?></span>
                            <?php if ($original_price > 0 && $original_price != $price): ?>
                                <span class="original-price">₹<?= number_format($original_price) ?></span>
                            <?php endif; ?>
                    
                            <?php if ($discount_percent > 0): ?>
                                <span class="discount-badge">SAVE <?= $discount_percent ?>%</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="service-description">
                            <p><?= htmlspecialchars($description) ?></p>
                        </div>
                        
                        <h6 style="font-size: 0.85rem;"><i class="fas fa-check-circle text-primary me-2"></i>Service Includes:</h6>
                        <ul class="feature-list">
                            <li><i class="fas fa-clock"></i> 15-20 Min Response</li>
                            <li><i class="fas fa-user-check"></i> Certified Professionals</li>
                            <li><i class="fas fa-shield-alt"></i> 1 Year Warranty</li>
                            <li><i class="fas fa-headset"></i> 24/7 Support</li>
                        </ul>
                        
                        <div class="summary-box">
                            <div class="summary-item">
                                <span>Service Charge:</span>
                                <span>₹<?= number_format($price) ?></span>
                            </div>
                            <div class="summary-item">
                                <span>GST (18%):</span>
                                <span>₹<?= number_format($price * 0.18) ?></span>
                            </div>
                            <div class="summary-item summary-total">
                                <span>Total Amount:</span>
                                <span class="text-primary fw-bold">₹<?= number_format($price * 1.18) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Booking Form (SECOND COLUMN) -->
            <div class="col-lg-6">
                <div class="booking-panel">
                    <h3><i class="fas fa-calendar-check me-2"></i>Complete Your Booking</h3>
                    
                    <?= $message ?>
                    
                    <form method="post" id="bookingForm">
                        <!-- Personal Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-user-circle"></i> Personal Info
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required"><i class="fas fa-user"></i> Full Name</label>
                                    <input type="text" class="form-control" name="full_name" value="<?= htmlspecialchars($user_data['name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required"><i class="fas fa-phone"></i> Contact No.</label>
                                    <input type="tel" class="form-control" name="contact_phone" value="<?= htmlspecialchars($user_data['phone_number'] ?? '') ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Vehicle Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-car"></i> Vehicle Info
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required"><i class="fas fa-truck"></i> Vehicle Type</label>
                                    <select name="vehicle_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="car">Car</option>
                                        <option value="suv">SUV</option>
                                        <option value="bike">Bike</option>
                                        <option value="auto">Auto</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"><i class="fas fa-id-card"></i> Vehicle No.</label>
                                    <input type="text" class="form-control" name="vehicle_number" placeholder="e.g., MH01AB1234">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label"><i class="fas fa-car-side"></i> Vehicle Model</label>
                                    <input type="text" class="form-control" name="vehicle_model" placeholder="e.g., Maruti Swift">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Service Schedule Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-calendar-alt"></i> Schedule
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required"><i class="fas fa-calendar-day"></i> Date</label>
                                    <input type="date" name="booking_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required"><i class="fas fa-clock"></i> Time</label>
                                    <select name="time_slot" class="form-select" required>
                                        <option value="">Select Time</option>
                                        <?php foreach (array_slice($time_slots, 0, 24) as $slot): ?>
                                            <option value="<?= $slot ?>"><?= $slot ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-map-marker-alt"></i> Location
                            </div>
                            <div class="mb-3">
                                <label class="form-label required"><i class="fas fa-location-dot"></i> Address</label>
                                <div class="location-input-wrapper">
                                    <i class="fas fa-map-pin"></i>
                                    <input type="text" name="location" class="form-control" id="locationInput" 
                                           placeholder="Your location" required>
                                    <button type="button" class="detect-location-btn" onclick="detectLocation()">
                                        <i class="fas fa-crosshairs"></i> Detect
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Information Section -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="fas fa-info-circle"></i> Additional
                            </div>
                            <div class="mb-3">
                                <textarea name="special_requests" class="form-control" rows="2" 
                                          placeholder="Special requests or instructions..."></textarea>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-book">
                            <i class="fas fa-check-circle me-2"></i>Find Mechanic
                        </button>
                    </form>
                    
                    <div class="back-link">
                        <a href="services.php"><i class="fas fa-arrow-left me-2"></i>Back to Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Set minimum date to today
    const dateInput = document.querySelector('input[name="booking_date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }
    
    // Detect user location
    function detectLocation() {
        const locationInput = document.getElementById('locationInput');
        if (navigator.geolocation) {
            locationInput.placeholder = "Detecting...";
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}&zoom=18&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            let address = '';
                            if (data.address.road) address += data.address.road;
                            if (data.address.suburb) address += (address ? ', ' : '') + data.address.suburb;
                            if (data.address.city) address += (address ? ', ' : '') + data.address.city;
                            locationInput.value = address || "Location detected";
                            locationInput.placeholder = "Your location";
                        })
                        .catch(() => {
                            locationInput.value = "Location detected";
                            locationInput.placeholder = "Your location";
                        });
                },
                () => {
                    alert("Unable to detect location. Please enter manually.");
                    locationInput.placeholder = "Your location";
                }
            );
        } else {
            alert("Geolocation not supported.");
        }
    }
    
    // Form validation
    document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
        const requiredFields = document.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#DC2626';
                isValid = false;
            } else {
                field.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill all required fields (*)');
        }
    });
    
    // Remove red border on input
    document.querySelectorAll('.form-control, .form-select').forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.style.borderColor = '';
            }
        });
    });
</script>

</body>
</html>
<?php include_once 'footer.php'; ?>