<?php
include_once 'navbar.php';
require_once '../dbcon.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flat Tyre Assistance | RoadSide Companion</title>

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

        .page-container { position: relative; z-index: 1; min-height: 100vh; }

       
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

        .service-header { margin-top: 30px; padding: 20px 0 40px; }

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

        .service-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
        }

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

        .service-main-image:hover { transform: scale(1.05); }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            padding: 20px;
            text-align: center;
        }

        .image-overlay h3 { margin: 0; font-size: 1.3rem; font-weight: 600; }

        .form-group { margin-bottom: 25px; }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label i { color: var(--primary); width: 20px; }

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

        .form-control::placeholder { color: rgba(255,255,255,0.5); }

        textarea.form-control { resize: vertical; min-height: 100px; }

        .location-input-wrapper { position: relative; }

        .location-input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            z-index: 2;
        }

        .location-input-wrapper .form-control { padding-left: 45px; }

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

        .detect-location-btn:hover { background: var(--primary); color: white; }

        .note-section {
            background: rgba(255, 165, 0, 0.1);
            border-left: 4px solid var(--warning);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .note-section i { color: var(--warning); margin-right: 10px; }

        .note-section p {
            margin: 0;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }

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

        .feature-item span { flex: 1; }

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

        .back-btn:hover { background: var(--primary); transform: scale(1.1); color: white; }

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

        .sos-btn i { font-size: 1.5rem; margin-bottom: 3px; }

        .sos-btn:hover { background: #B91C1C; transform: scale(1.1); }

        @media (max-width: 768px) {
            .service-header h1 { font-size: 1.8rem; }
            .service-card { padding: 20px; }
            .service-main-image { height: 200px; }
            .back-btn, .sos-btn { width: 45px; height: 45px; }
            .sos-btn { width: 55px; height: 55px; }
        }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .navbar-brand i { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body>
<div class="page-container">

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-tools me-2"></i>RoadSide Companion</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="index.php#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#why-us">Why Us</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#reviews">Reviews</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
                <li class="nav-item ms-2"><a class="btn btn-primary" href="login.php"><i class="fas fa-sign-in-alt me-2"></i>Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<a href="index.php#emergency-assistance" class="back-btn"><i class="fas fa-arrow-left"></i></a>

<div class="container" style="margin-top: 100px; margin-bottom: 80px;">
    
    <div class="text-center service-header" data-aos="fade-up">
        <div class="service-badge"><i class="fas fa-tire-flat"></i><span>Quick Assistance</span></div>
        <h1>Flat Tyre Assistance</h1>
        <p>Quick tyre change, puncture repair, and spare tyre installation at your location</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-7" data-aos="fade-right">
            <div class="service-card">
                <div class="service-image-wrapper">
                    <img src="pic/flattyre.jpg" alt="Flat Tyre Assistance" class="service-main-image" onerror="this.src='https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=1170&q=80'">
                    <div class="image-overlay"><h3><i class="fas fa-tire-flat"></i> Flat Tyre Repair & Replacement</h3></div>
                </div>

                <form action="process_booking.php" method="POST" id="bookingForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> Full Name</label>
                                <input type="text" class="form-control" name="full_name" placeholder="Enter your full name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" class="form-control" name="phone" placeholder="Enter mobile number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-car"></i> Vehicle Type</label>
                                <select class="form-select" name="vehicle_type" required>
                                    <option value="">Select Vehicle Type</option>
                                    <option value="car">Car (4 Wheeler)</option>
                                    <option value="suv">SUV / MUV</option>
                                    <option value="bike">Bike (2 Wheeler)</option>
                                    <option value="auto">Auto Rickshaw</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> Vehicle Number</label>
                                <input type="text" class="form-control" name="vehicle_number" placeholder="Enter vehicle registration number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-car-side"></i> Vehicle Model</label>
                                <input type="text" class="form-control" name="vehicle_model" placeholder="e.g., Maruti Suzuki Swift">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-calendar-alt"></i> Service Date</label>
                                <input type="date" class="form-control" name="service_date" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-map-marker-alt"></i> Location</label>
                                <div class="location-input-wrapper">
                                    <i class="fas fa-location-dot"></i>
                                    <input type="text" class="form-control" id="locationInput" name="location" placeholder="Enter your current location" required>
                                    <button type="button" class="detect-location-btn" onclick="detectLocation()">Detect</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-tire"></i> Tyre Issue Type</label>
                                <select class="form-select" name="tyre_issue" required>
                                    <option value="">Select Issue Type</option>
                                    <option value="puncture">Puncture Repair</option>
                                    <option value="flat">Complete Flat Tyre</option>
                                    <option value="spare">Spare Tyre Installation</option>
                                    <option value="burst">Tyre Burst</option>
                                    <option value="pressure">Low Air Pressure</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-spare"></i> Do you have spare tyre?</label>
                                <select class="form-select" name="has_spare_tyre">
                                    <option value="yes">Yes, I have a spare tyre</option>
                                    <option value="no">No, need new tyre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-align-left"></i> Additional Details</label>
                                <textarea class="form-control" name="issue_description" rows="4" placeholder="Please describe the issue in detail..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="note-section">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong>
                        <p>Our technician will arrive with necessary tools within 10-15 minutes. Spare tyre available on request at additional cost.</p>
                    </div>

                    <button type="submit" class="btn btn-primary submit-btn">
                        <i class="fas fa-phone-alt me-2"></i> Request Tyre Assistance
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5" data-aos="fade-left">
            <div class="service-card">
                <h4 class="mb-3"><i class="fas fa-star-of-life text-primary me-2"></i> Service Includes</h4>
                <div class="feature-item"><i class="fas fa-tire"></i><span>Puncture Repair (up to 3 punctures)</span></div>
                <div class="feature-item"><i class="fas fa-tools"></i><span>Spare Tyre Installation</span></div>
                <div class="feature-item"><i class="fas fa-gauge-high"></i><span>Air Pressure Check & Refill</span></div>
                <div class="feature-item"><i class="fas fa-car"></i><span>All Vehicle Types Supported</span></div>
                <div class="feature-item"><i class="fas fa-clock"></i><span>15-20 Min Response Time</span></div>

                <hr class="my-4" style="border-color: var(--border);">

                <h4 class="mb-3"><i class="fas fa-tag text-primary me-2"></i> Pricing</h4>
                <div class="d-flex justify-content-between py-2"><span>Puncture Repair</span><span class="text-primary fw-bold">₹199</span></div>
                <div class="d-flex justify-content-between py-2"><span>Spare Tyre Installation</span><span class="text-primary fw-bold">₹299</span></div>
                <div class="d-flex justify-content-between py-2"><span>New Tyre (per tyre)</span><span class="text-primary fw-bold">From ₹2500</span></div>
                <div class="mt-3 p-3 bg-primary bg-opacity-10 rounded-3">
                    <div class="text-center"><i class="fas fa-gift fa-2x text-primary mb-2"></i><h5 class="mb-0">First Service Offer</h5><p class="mb-0"><span class="h3 text-primary">30% OFF</span> on puncture repair</p></div>
                </div>
            </div>

            <div class="service-card mt-4">
                <h4 class="mb-3"><i class="fas fa-phone-alt text-primary me-2"></i> Need Immediate Help?</h4>
                <div class="text-center"><i class="fas fa-headset fa-3x text-primary mb-3"></i><h2 class="mb-2" style="font-family: monospace;">1800-123-4567</h2><p class="mb-3">24/7 Emergency Helpline</p><a href="tel:18001234567" class="btn btn-outline-primary w-100"><i class="fas fa-phone-alt me-2"></i>Call Now</a></div>
            </div>
        </div>
    </div>
</div>

<button class="sos-btn" id="sosBtn"><i class="fas fa-exclamation-triangle"></i><span>SOS</span></button>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 900, once: true, offset: 80 });
    window.addEventListener('scroll', () => { document.querySelector('.navbar').style.boxShadow = window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)'; });
    document.getElementById('sosBtn').addEventListener('click', () => { if (confirm('🚨 EMERGENCY SOS 🚨\n\nDo you need immediate roadside assistance?')) { alert('Emergency services have been notified! Help is on the way.'); } });
    function detectLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                    .then(response => response.json())
                    .then(data => { const area = data.address.suburb || data.address.neighbourhood || ""; const city = data.address.city || data.address.town || ""; document.getElementById('locationInput').value = area ? `${area}, ${city}` : city; })
                    .catch(() => document.getElementById('locationInput').value = "Location detected");
            }, () => alert("Please enable location access."));
        } else alert("Geolocation not supported.");
    }
</script>
</body>
</html>
<?php include_once 'footer.php'; ?>