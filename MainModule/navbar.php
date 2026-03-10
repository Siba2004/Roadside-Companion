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
        }

        /* Navbar */
        .navbar {
            background: white;
            padding: 15px 0;
            box-shadow: 0 2px 15px rgba(11, 79, 108, 0.05);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--primary) !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .navbar-brand i {
            color: var(--accent);
        }

        .nav-link {
            color: var(--gray-corporate) !important;
            font-weight: 600;
            margin: 0 15px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 4px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .btn-primary:hover {
            background: var(--dark-blue);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 4px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 1px;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #F8FBFE 0%, #FFFFFF 100%);
            padding: 130px 0 80px;
            position: relative;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .hero-title span {
            color: var(--accent);
            display: block;
        }

        .hero-text {
            color: var(--gray-corporate);
            font-size: 1rem;
            margin-bottom: 30px;
        }

        /* Corporate Banner */
        .corporate-banner {
            background: white;
            border-left: 6px solid var(--accent);
            border-radius: 0;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(11, 79, 108, 0.05);
        }

        .badge-corporate {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 2px;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        

        /* Service Cards - Corporate */
        .service-card {
            background: white;
            border-radius: 0;
            padding: 35px;
            box-shadow: 0 5px 20px rgba(11, 79, 108, 0.03);
            border: 1px solid var(--border-corporate);
            transition: 0.2s;
            height: 100%;
            position: relative;
            margin-bottom:30px;
        }

        .service-card:hover {
            border-color: var(--accent);
            box-shadow: 0 15px 30px rgba(11, 79, 108, 0.08);
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 0;
            background: var(--accent);
            transition: 0.3s;
        }

        .service-card:hover::before {
            height: 100%;
        }

        .service-icon {
            width: 60px;
            height: 60px;
            background: #F0F7FF;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
        }

        .service-icon i {
            font-size: 1.8rem;
            color: var(--accent);
        }

        .service-title {
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 1.1rem;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .service-list li {
            padding: 6px 0;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .service-list li i {
            color: var(--success);
            margin-right: 12px;
        }

        /* Feature Cards - Corporate */
        .feature-card {
            text-align: center;
            padding: 30px 25px;
            background: white;
            border: 1px solid var(--border-corporate);
            transition: 0.2s;
            height: 100%;
        }

        .feature-card:hover {
            border-color: var(--accent);
            background: #F8FBFE;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: #F0F7FF;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: var(--accent);
            font-size: 1.8rem;
        }

        .feature-title {
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.95rem;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .feature-text {
            font-size: 0.9rem;
        }

        /* Review Cards - Corporate */
        .review-card {
            background: white;
            padding: 30px;
            border: 1px solid var(--border-corporate);
            transition: 0.2s;
        }

        .review-card:hover {
            background: #F8FBFE;
            border-color: var(--accent);
        }

        .reviewer-img {
            width: 60px;
            height: 60px;
            border-radius: 0;
            object-fit: cover;
        }

        .review-rating {
            color: var(--warning);
        }

        .review-text {
            font-size: 0.95rem;
            color: var(--gray-corporate);
        }

        .reviewer-name {
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .reviewer-role {
            font-size: 0.8rem;
        }

/* Flipkart Style Banner Slider */
.banner-slider-container {
    background: linear-gradient(135deg, #0B4F6C 0%, #1B98F5 100%);
    padding: 20px 0;
    position: relative;
    overflow: hidden;
    /* Add this to your existing CSS */
    margin-top: 80px; /* Adjust based on your navbar height */
}


.banner-slider {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.slider-track {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.slide {
    min-width: 100%;
    position: relative;
}

.slide img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    display: block;
}

.slide-content {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, rgba(11,79,108,0.9) 0%, rgba(27,152,245,0.7) 100%);
    color: white;
    display: flex;
    align-items: center;
    padding: 40px;
}

.slide-text {
    max-width: 50%;
}

.slide-text h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.slide-text p {
    font-size: 1.2rem;
    margin-bottom: 20px;
    opacity: 0.9;
}

.slide-text .offer-badge {
    background: #FFA500;
    color: white;
    padding: 8px 20px;
    border-radius: 30px;
    font-weight: 700;
    display: inline-block;
    margin-bottom: 20px;
}

.slide-text .btn-offer {
    background: white;
    color: var(--primary);
    border: none;
    padding: 12px 30px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 4px;
    transition: 0.3s;
}

.slide-text .btn-offer:hover {
    background: #FFA500;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.slide-image {
    position: absolute;
    right: 40px;
    top: 50%;
    transform: translateY(-50%);
    max-width: 40%;
}

.slide-image i {
    font-size: 12rem;
    color: rgba(255,255,255,0.3);
}

/* Slider Navigation Dots */
.slider-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.dot {
    width: 12px;
    height: 12px;
    background: rgba(255,255,255,0.5);
    border-radius: 50%;
    cursor: pointer;
    transition: 0.3s;
}

.dot.active {
    background: white;
    transform: scale(1.2);
}

/* Slider Navigation Arrows */
.slider-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    cursor: pointer;
    z-index: 10;
    transition: 0.3s;
}

.slider-arrow:hover {
    background: white;
    color: var(--primary);
}

.slider-arrow.left {
    left: 20px;
}

.slider-arrow.right {
    right: 20px;
}

/* Timer Progress Bar */
.slider-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 4px;
    background: rgba(255,255,255,0.3);
    width: 100%;
    z-index: 10;
}

.progress-bar {
    height: 100%;
    background: #FFA500;
    width: 0%;
    transition: width linear;
}

/* Responsive */
@media (max-width: 768px) {
    .slide img {
        height: 200px;
    }
    
    .slide-content {
        padding: 20px;
    }
    
    .slide-text {
        max-width: 100%;
    }
    
    .slide-text h2 {
        font-size: 1.5rem;
    }
    
    .slide-text p {
        font-size: 1rem;
    }
    
    .slide-image {
        display: none;
    }
    
    .slider-arrow {
        width: 30px;
        height: 30px;
    }
}

        /* SOS Button - Corporate */
        .sos-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background: #DC2626;
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.2);
            z-index: 999;
        }

        .sos-button:hover {
            background: #B91C1C;
            transform: scale(1.05);
        }

        .sos-button i {
            font-size: 1.6rem;
            margin-bottom: 3px;
        }

        .sos-button span {
            font-size: 0.6rem;
        }

        /* Footer - Corporate */
        .footer {
            background: var(--primary);
            color: white;
            padding: 60px 0 30px;
            border-top: 4px solid var(--accent);
        }

        .footer-title {
            color: white;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.95rem;
            letter-spacing: 1px;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: var(--accent);
        }

        .footer-links a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .footer-links a:hover {
            color: white;
            padding-left: 8px;
        }

        .social-links a {
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: 0.2s;
        }

        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <!-- Same structure with corporate styling -->
     <!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-tools"></i>
            RoadSide Companion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./home.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#services">Our Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#location">Your Location</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact Us</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-primary" href="./login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
