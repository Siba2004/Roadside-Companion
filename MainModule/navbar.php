<?php
require_once '../dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoadSide Companion - Corporate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Add Orbitron for navbar brand -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0B4F6C;
            --secondary: #3282B8;
            --accent: #1B98F5;
            --success: #20B2AA;
            --warning: #FFA500;
            --light-bg: #F8FBFE;
            --dark-blue: #0A2472;
            --gray-corporate: #4A5568;
            --border-corporate: #E2E8F0;
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: var(--light-bg);
            color: var(--gray-corporate);
            padding-top: 80px; /* space for fixed navbar */
        }

        /* ===== NAVBAR STYLES (exactly like homepage) ===== */
        .navbar { 
            background: linear-gradient(135deg,#0b1220,#0f1f3d);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 0;           /* reduced from 30px to match homepage */
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0,0,0,0.8);
        }

        .navbar-brand { 
            font-weight: 600; 
            font-size: 1.5rem; 
            color: white !important; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            font-family: 'Orbitron', sans-serif;  /* homepage font */
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
            font-size: .6rem;           /* small uppercase letters */
            letter-spacing: 1px;
            transition: 0.3s;
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
        }

        /* Keep your existing corporate styles below */
        /* ... (rest of your corporate page CSS) ... */
    </style>
</head>
<body>

<!-- NAVBAR – now identical to homepage -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="home.php">
            <i class="fas fa-tools me-2"></i>RoadSide Companion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                <li class="nav-item text-center">
                    <a class="nav-link" href="#" onclick="getLocation()">
                        <i class="fas fa-map-marker-alt"></i> Your Location
                    </a>
                    <small id="locationText" class="text-light"></small>
                </li>
               <?php if(isset($_SESSION['name'])){ ?>

<li class="nav-item dropdown ms-2">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
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
        <i class="fas fa-sign-in-alt me-2"></i>Login
    </a>
</li>

<?php } ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Your corporate page content goes here -->
<!-- ... -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 900, once: true, offset: 80 });

    // ===== LOCATION FUNCTION (exactly as in homepage) =====
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocation not supported.");
        }
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var lon = position.coords.longitude;

        fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lon)
        .then(response => response.json())
        .then(data => {
            var area = data.address.suburb || data.address.neighbourhood || "";
            var city = data.address.city || data.address.town || data.address.village || "";
            document.getElementById("locationText").innerHTML = "📍 " + area + ", " + city;
        });
    }

    function showError(error) {
        if (error.code == 1) {
            alert("Location permission denied.");
        } else if (error.code == 2) {
            alert("Location unavailable.");
        } else if (error.code == 3) {
            alert("Location request timeout.");
        }
    }

    // Optional: smooth scroll for anchor links (if any)
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', e => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        });
    });
</script>

</body>
</html>