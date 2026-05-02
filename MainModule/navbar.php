<?php
require_once '../dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>RoadSide Companion - Corporate</title>
    
    <!-- CRITICAL INLINE STYLES - PREVENT WHITE FLICKER COMPLETELY -->
    <style>
        /* Force immediate dark background - NO white flash possible */
        html, body {
            margin: 0;
            padding: 0;
            background: #0a0a0f !important;
            min-height: 100vh;
        }
        
        body {
            background: #0a0a0f !important;
            background-attachment: fixed;
            color: white;
            opacity: 1;
            visibility: visible;
            padding-top: 80px;
        }
        
        /* Hide content briefly but maintain dark background - prevents FOUC */
        .corporate-container {
            opacity: 0;
            animation: fadeInContent 0.25s ease-out forwards;
        }
        
        @keyframes fadeInContent {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Navbar - instant dark background matching homepage */
        .navbar { 
            background: #0a0a0f !important;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.8);
        }
        
        /* Ensure no white flash from any element */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    </style>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0d6efd;
            --dark-blue: #0b5ed7;
            --accent: #0d6efd;
            --success: #20B2AA;
            --warning: #FFA500;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #0a0a0f;
            background-attachment: fixed;
            color: white;
            padding-top: 80px;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(13,110,253,0.03) 0%, rgba(0,0,0,0.8) 100%);
            z-index: 0;
            pointer-events: none;
        }

        /* ===== NAVBAR STYLES (exactly like homepage) ===== */
        .navbar { 
            background: #0a0a0f;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.8);
            transition: box-shadow 0.2s ease;
        }

        .navbar-brand { 
            font-weight: 600; 
            font-size: 1.5rem; 
            color: white !important; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            font-family: 'Orbitron', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .navbar-brand i { 
            color: var(--primary); 
            font-size: 1.5rem;
        }
        
        .nav-link { 
            color: rgba(255,255,255,0.8) !important; 
            font-weight: 400; 
            margin: 7px 12px; 
            text-transform: uppercase; 
            font-size: 0.75rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover { 
            color: var(--primary) !important; 
            transform: translateY(-2px);
        }

        /* Location display */
        #locationText {
            font-size: 9px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
            color: rgba(255,255,255,0.7);
            margin-top: 2px;
        }

        /* Dropdown menu styling */
        .dropdown-menu {
            background: #1a1a2a;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            border-radius: 10px;
            padding: 10px 0;
        }
        
        .dropdown-item {
            color: rgba(255,255,255,0.8);
            transition: all 0.2s ease;
            padding: 8px 20px;
            font-size: 0.85rem;
        }
        
        .dropdown-item:hover {
            background: rgba(13,110,253,0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .dropdown-divider {
            border-top-color: rgba(255,255,255,0.1);
            margin: 8px 0;
        }
        
        /* Button styling - matching homepage */
        .btn-primary {
            background: var(--primary);
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 0 15px var(--primary);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
            background: transparent;
            border-radius: 5px;
            transition: 0.3s;
            padding: 8px 20px;
        }
        
        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 0 15px var(--primary);
            transform: scale(1.05);
        }

        
        /* Prevent any white flash from images */
        img {
            background-color: #0a0a0f;
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .navbar {
                padding: 10px 0;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .nav-link {
                font-size: 0.7rem;
                margin: 5px 8px;
            }
            
            #locationText {
                max-width: 100px;
                font-size: 8px;
            }
            
            .btn-primary, .btn-outline-primary {
                padding: 6px 15px;
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR – exactly matching homepage design -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="home.php">
            <img src="../image/logo.jpg.jpeg" alt="Roadside Companion Logo" style="height: 50px; width: auto; margin-right: 7px; margin-top: -10px; margin-bottom: -10px;">
        RoadSide Companion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="services.php">Services</a>
                </li>
                <li class="nav-item text-center">
                    <a class="nav-link" href="#" onclick="getLocation(); return false;">
                        <i class="fas fa-map-marker-alt"></i> Your Location
                    </a>
                    <small id="locationText" class="text-light"></small>
                </li>
                
                <?php if(isset($_SESSION['name'])){ ?>
                <li class="nav-item dropdown ms-2">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user me-2"></i>
                        Hello <?php echo htmlspecialchars($_SESSION['name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-circle me-2"></i> Your Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="my_services.php">
                                <i class="fas fa-tools me-2"></i> My Services
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
                <?php } else { ?>
                <li class="nav-item ms-2">
                    <a class="btn btn-primary" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>LOGIN
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    (function() {
        // Initialize AOS with optimized settings to prevent flicker
        AOS.init({
            duration: 700,
            once: true,
            offset: 60,
            throttleDelay: 50,
            disable: false
        });
        
        // Ensure corporate container becomes visible smoothly
        setTimeout(function() {
            const container = document.querySelector('.corporate-container');
            if (container) {
                container.style.opacity = '1';
            }
        }, 10);
        
        // Navbar shadow on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.style.boxShadow = window.scrollY > 50 
                    ? '0 5px 25px rgba(0,0,0,0.9)' 
                    : '0 2px 15px rgba(0,0,0,0.8)';
            }
        });
    })();

    // ===== LOCATION FUNCTION (exactly as in homepage) =====
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocation not supported by your browser.");
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;
        
        // Show loading text
        const locationText = document.getElementById("locationText");
        if (locationText) {
            locationText.innerHTML = "📍 Locating...";
        }

        fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lon + "&zoom=18&addressdetails=1")
        .then(response => response.json())
        .then(data => {
            var area = data.address.suburb || data.address.neighbourhood || data.address.village || "";
            var city = data.address.city || data.address.town || data.address.state || "";
            if (locationText) {
                locationText.innerHTML = "📍 " + (area ? area + ", " : "") + city;
            }
        })
        .catch(error => {
            console.error("Location error:", error);
            if (locationText) {
                locationText.innerHTML = "📍 Location unavailable";
            }
        });
    }

    function showError(error) {
        let message = "";
        switch(error.code) {
            case error.PERMISSION_DENIED:
                message = "Location permission denied.";
                break;
            case error.POSITION_UNAVAILABLE:
                message = "Location information unavailable.";
                break;
            case error.TIMEOUT:
                message = "Location request timeout.";
                break;
            default:
                message = "Unknown location error.";
        }
        console.error(message);
        const locationText = document.getElementById("locationText");
        if (locationText) {
            locationText.innerHTML = "📍 Location unavailable";
        }
    }

    // Smooth scroll for anchor links (if any)
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', e => {
            const hash = link.getAttribute('href');
            if (hash && hash !== '#') {
                const target = document.querySelector(hash);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
</script>

</body>
</html>