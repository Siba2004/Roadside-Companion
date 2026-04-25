<?php
include_once 'navbar.php';
require_once '../dbcon.php';
?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoadSide Companion</title>

    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts - Matching login/register -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* ── DESIGN TOKENS - Matching login/register ── */
        :root {
            --primary:   #0d6efd;
            --dark-blue: #0b5ed7;
            --accent:    #0d6efd;
            --success:   #20B2AA;
            --warning:   #FFA500;
            --gray:      #rgba(255,255,255,0.8);
            --light-bg:  transparent;
            --border:    rgba(255,255,255,0.2);
            --icon-bg:   rgba(13,110,253,0.1);
            --dark-overlay: rgba(6, 6, 6, 0.95);
        }

        /* ── BASE - Solid dark background + gradient overlay ── */
        * { font-family: 'Poppins', sans-serif; }
        
        body { 
            /* Changed from image background to solid dark base */
            background: #0a0a0f;            /* solid near‑black */
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            color: white;
            position: relative;
        }
        
        /* Dark overlay – subtle diagonal blue‑to‑black gradient */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(13,110,253,0.03) 0%, rgba(0,0,0,0.8) 100%);
            z-index: 0;
        }

        /* Main container */
        .home-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            color: white;
        }

        /* ── BUTTONS - Matching login/register ── */
        .btn-primary { 
            background: var(--primary); 
            border: none; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: .85rem; 
            letter-spacing: 1px;
            padding: 8px 20px;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-primary:hover { 
            background: var(--dark-blue); 
            box-shadow: 0 0 15px var(--primary);
            transform: scale(1.05);
        }
        .btn-outline-primary { 
            border: 2px solid var(--primary); 
            color: white; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: .85rem; 
            letter-spacing: 1px;
            background: transparent;
            border-radius: 5px;
            transition: 0.3s;
        }
        .btn-outline-primary:hover { 
            background: var(--primary); 
            color: white; 
            box-shadow: 0 0 15px var(--primary);
            transform: scale(1.05);
        }

        /* ── BANNER SLIDER ── */
        .banner-wrap { 
            margin-top: 65px; 
            background: transparent; 
            padding: 40px 0; 
        }
        .slider { 
            position: relative; 
            max-width: 1380px; 
            margin: 0 auto; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .slider-track { 
            display: flex; 
            transition: transform .3s ease-in-out; 
        }

        /* Each slide */
        .slide { 
            min-width: 100%; 
            position: relative; 
        }
        .slide img { 
            width: 100%; 
            height: 400px; 
            object-fit: cover; 
            display: block; 
        }

        /* For Emergency slide */
        .slide:nth-child(1) img {
            object-position: 60% center;
        }
        /* For Battery slide */
        .slide:nth-child(2) img {
            object-position: 80% center;
        }
        /* For Fuel slide */
        .slide:nth-child(3) img {
            object-position: 55% center;
        }
        /* For Flat Tyre slide */
        .slide:nth-child(4) img {
            object-position: 90% center;
        }
        /* For Towing slide */
        .slide:nth-child(5) img {
            object-position: 60% center;
        }
        .slide-overlay {
            position: absolute; 
            inset: 0;
            background: rgba(0,0,0,0.45);
            backdrop-filter: blur(0px);
            color: white; 
            display: flex; 
            align-items: center; 
            padding: 80px;
        }
        .slide-text { 
            max-width: 55%; 
        }
        .slide-text h2 { 
            font-family: 'Orbitron', sans-serif;
            font-size: 2.2rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 10px; 
            color: white;
        }
        .slide-text p  { 
            font-size: 1.1rem; 
            opacity: .9; 
            margin-bottom: 20px; 
            color: rgba(255,255,255,0.9);
        }

        .badge-offer { 
            display: inline-block; 
            background: var(--warning); 
            color: white; 
            padding: 6px 18px; 
            border-radius: 30px; 
            font-weight: 700; 
            margin-bottom: 15px; 
        }
        .btn-slide { 
            background: var(--primary); 
            color: white; 
            border: none; 
            padding: 10px 25px; 
            font-weight: 600; 
            text-transform: uppercase; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: .3s; 
        }
        .btn-slide:hover { 
            background: var(--dark-blue); 
            box-shadow: 0 0 15px var(--primary);
            transform: scale(1.05);
            color: white;
        }

        /* Decorative icon */
        .slide-icon { 
            position: absolute; 
            right: 60px; 
            top: 50%; 
            transform: translateY(-50%); 
            font-size: 10rem; 
            color: rgba(255,255,255,.1); 
            pointer-events: none; 
        }

        /* Dots, arrows, progress */
        .slider-dots { 
            position: absolute; 
            bottom: 15px; 
            left: 50%; 
            transform: translateX(-50%); 
            display: flex; 
            gap: 8px; 
            z-index: 10; 
        }
        .dot { 
            width: 11px; 
            height: 11px; 
            background: rgba(255,255,255,.3); 
            border-radius: 50%; 
            cursor: pointer; 
            transition: .3s; 
        }
        .dot.active { 
            background: var(--primary); 
            transform: scale(1.2);
            box-shadow: 0 0 10px var(--primary);
        }

        .slider-arrow { 
            position: absolute; 
            top: 50%; 
            transform: translateY(-50%); 
            width: 38px; 
            height: 38px; 
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            color: white; 
            cursor: pointer; 
            z-index: 10; 
            transition: .3s; 
            border: 1px solid rgba(255,255,255,0.2);
        }
        .slider-arrow:hover { 
            background: var(--primary); 
            color: white; 
        }
        .slider-arrow.left  { left: 15px; }
        .slider-arrow.right { right: 15px; }

        .slider-progress { 
            position: absolute; 
            bottom: 0; 
            left: 0; 
            width: 100%; 
            height: 4px; 
            background: rgba(255,255,255,.1); 
            z-index: 10; 
        }
        .progress-fill   { 
            height: 100%; 
            background: var(--primary); 
            width: 0%; 
            box-shadow: 0 0 10px var(--primary);
        }

        /* ── SHARED SECTION HEADINGS ── */
        section { 
            padding: 35px 0; 
            position: relative;
        }
        .section-title    { 
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem; 
            font-weight: 700; 
            color: white; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 10px; 
            text-shadow: 0 0 10px rgba(13,110,253,0.3);
        }
        .section-subtitle { 
            color: rgba(255,255,255,0.8); 
            margin-bottom: 40px; 
        }

        /* ── ICON SHARED ── */
        .icon-box { 
            width: 58px; 
            height: 58px; 
            background: rgba(13,110,253,0.1); 
            border: 2px solid var(--primary);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-bottom: 18px; 
            border-radius: 5px;
            transition: 0.3s;
        }
        .icon-box i { 
            font-size: 1.5rem; 
            color: var(--primary); 
        }

        /* ── SERVICE CARDS ── */
        .service-card { 
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            padding: 35px; 
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            height: 100%; 
            transition: .3s; 
            position: relative; 
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
        }
        .service-card:hover { 
            border-color: var(--primary); 
            box-shadow: 0 0 30px rgba(13,110,253,0.3);
            transform: translateY(-5px);
        }

        /* Service Image*/
        .service-image {
            max-height: 300px;
            width: auto;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(13,110,253,0.3);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .service-image:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(13,110,253,0.5);
        }
        .service-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        .service-header .icon-box {
            margin-right: 10px;
        }

        .clickable-card {
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
        }
        .clickable-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary) !important;
            box-shadow: 0 0 30px rgba(181, 184, 188, 0.4);
        }
        a .card-title,
        a .check-list li,
        a .check-list li i {
            color: white;
        }
        a .check-list li i {
            color: var(--primary);
        }
        .clickable-card::after {
            content: '↗';
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.2rem;
            color: var(--primary);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .clickable-card:hover::after {
            opacity: 0.7;
        }

        .card-title { 
            color: white; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: 1rem; 
            letter-spacing: 1px; 
            margin-bottom: 15px; 
        }
        .check-list { 
            list-style: none; 
            padding: 0; 
        }
        .check-list li { 
            padding: 5px 0; 
            display: flex; 
            align-items: center; 
            font-size: .9rem; 
            color: rgba(255,255,255,0.9);
        }
        .check-list li i { 
            color: var(--primary); 
            margin-right: 10px; 
        }

        /* Enhanced Assist Card Styles with Images */
        .assist-card {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
        }
        .assist-card.clickable-card {
            cursor: pointer;
        }
        .assist-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(13,110,253,0.4);
        }
        .assist-card-image {
            position: relative;
            height: 160px;
            overflow: hidden;
        }
        .assist-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .assist-card:hover .assist-card-image img {
            transform: scale(1.1);
        }
        .assist-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 0 15px rgba(13,110,253,0.5);
            z-index: 2;
        }
        .assist-card-content {
            padding: 20px 15px 15px;
            text-align: center;
            position: relative;
        }
        .assist-card-content .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(13,110,253,0.1);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -40px auto 12px;
            position: relative;
            z-index: 3;
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .assist-card:hover .assist-card-content .icon-box {
            background: var(--primary);
            transform: rotate(360deg);
        }
        .assist-card:hover .assist-card-content .icon-box i {
            color: white;
        }
        .assist-card-content .icon-box i {
            font-size: 1.3rem;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        .assist-card-content .card-title {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 1rem;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .assist-card-content p {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 15px;
            min-height: 70px;
        }
        .assist-card-footer {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 12px;
        }
        .assist-link {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .assist-link i {
            transition: transform 0.3s;
        }
        .assist-card:hover .assist-link i {
            transform: translateX(5px);
        }

        @media (max-width: 768px) {
            .assist-card-image {
                height: 130px;
            }
            .assist-card-content {
                padding: 15px 10px 10px;
            }
            .assist-card-content .icon-box {
                width: 45px;
                height: 45px;
                margin-top: -35px;
            }
            .assist-card-content .icon-box i {
                font-size: 1.1rem;
            }
            .assist-card-content .card-title {
                font-size: 0.9rem;
            }
            .assist-card-content p {
                font-size: 0.8rem;
                min-height: 60px;
            }
        }

        /* Why Choose Us - Card */
        .feature-card {
            text-align: center;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            height: 100%;
            transition: 0.3s;
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .card-image-wrapper {
            width: 100%;
            height: 200px;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
        }
        .feature-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s ease;
        }
        .feature-card:hover .feature-image {
            transform: scale(1.1);
        }
        .feature-card:hover {
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(13,110,253,0.3);
            transform: translateY(-6px);
        }
        .card-title {
            margin-bottom: 5px;
            margin-top: 10px;
        }
        .feature-card p {
            color: rgba(255,255,255,0.8);
            padding: 0 15px;
            margin-bottom: 13px;
        }
        
        /* ── REVIEW CARDS ── */
        .review-card { 
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            padding: 30px; 
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            text-align: center; 
            height: 100%; 
            transition: .3s; 
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
        }
        .review-card:hover { 
            border-color: var(--primary); 
            box-shadow: 0 0 30px rgba(13,110,253,0.3);
            transform: translateY(-5px);
        }
        .reviewer-img  { 
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-bottom: 10px; 
            border: 2px solid var(--primary);
        }
        .review-stars  { 
            color: var(--warning); 
            margin-bottom: 10px; 
        }
        .reviewer-name { 
            color: white; 
            font-weight: 600; 
            font-size: .85rem; 
            text-transform: uppercase; 
            margin-bottom: 2px; 
        }
        .reviewer-role { 
            font-size: .8rem; 
            color: rgba(255,255,255,0.7); 
        }
        .review-card p {
            color: rgba(255,255,255,0.9);
        }

        /* ── SOS BUTTON ── */
        .sos-btn { 
            position: fixed; 
            bottom: 30px; 
            right: 30px; 
            width: 70px; 
            height: 70px; 
            background: #DC2626; 
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
            box-shadow: 0 0 30px rgba(220,38,38,0.6);
        }

        /* About section text */
        .text-muted {
            color: rgba(255,255,255,0.8) !important;
        }
        .lead {
            color: white;
        }
        .position-absolute small {
            color: rgba(255,255,255,0.8) !important;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .slide img     { height: 200px; }
            .slide-overlay { padding: 20px; }
            .slide-text    { max-width: 100%; }
            .slide-text h2 { font-size: 1.4rem; }
            .slide-icon    { display: none; }
        }

        /* Animation for logo */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        .navbar-brand i {
            animation: float 3s ease-in-out infinite;
        }
        .shadow-lg {
            box-shadow: 0 0 30px rgba(0,0,0,0.8) !important;
        }
        .fw-bold {
            color: white;
        }
    </style>
</head>
<body>
<div class="home-container">

<!-- BANNER SLIDER -->
<div class="banner-wrap">
    <div class="container-fluid px-5">
        <div class="slider">

            <div class="slider-track" id="sliderTrack">

                <div class="slide">
                    <img src="pic\image-2.jpg" alt="Emergency Service">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <span class="badge-offer">🎉 FLAT 30% OFF</span>
                            <h2>Emergency<br>Roadside Service</h2>
                            <p>24/7 assistance at your fingertips. First service at 30% off!</p>
                            <button class="btn-slide">Book Now <i class="fas fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>
                </div>

                <div class="slide">
                    <img src="pic\battery.jpg" alt="Battery Service">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <span class="badge-offer">⚡ 20% OFF</span>
                            <h2>Battery<br>Boosting Service</h2>
                            <p>Jump start or replace battery. Get 20% off on battery service.</p>
                            <button class="btn-slide">Avail Offer <i class="fas fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>
                </div>

                <div class="slide">
                    <img src="pic/fuel.jpg" alt="Fuel Delivery">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <span class="badge-offer">⛽ FREE DELIVERY</span>
                            <h2>Emergency<br>Fuel Delivery</h2>
                            <p>Running out of fuel? Get free delivery on your first order.</p>
                            <button class="btn-slide">Order Now <i class="fas fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>
                </div>

                <div class="slide">
                    <img src="pic/f2.jpg" alt="Flat Tyre">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <span class="badge-offer">🛞 25% OFF</span>
                            <h2>Flat Tyre<br>Assistance</h2>
                            <p>Quick tyre change or repair. Special discount for members.</p>
                            <button class="btn-slide">Get Help <i class="fas fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>
                </div>

                <div class="slide">
                    <img src="pic/tow.jpg" alt="Towing">
                    <div class="slide-overlay">
                        <div class="slide-text">
                            <span class="badge-offer">🚛 SAVE 40%</span>
                            <h2>Towing<br>Services</h2>
                            <p>Professional towing at best prices. Limited period offer.</p>
                            <button class="btn-slide">Call Now <i class="fas fa-arrow-right ms-1"></i></button>
                        </div>
                    </div>
                </div>

            </div><!-- /slider-track -->

            <div class="slider-dots" id="sliderDots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>

            <div class="slider-arrow left"  id="prevSlide"><i class="fas fa-chevron-left"></i></div>
            <div class="slider-arrow right" id="nextSlide"><i class="fas fa-chevron-right"></i></div>

            <div class="slider-progress">
                <div class="progress-fill" id="progressFill"></div>
            </div>

        </div><!-- /slider -->
    </div>
</div>

<!-- SERVICES -->
<section id="services">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">Our Popular Services</h2>
            <p class="section-subtitle">Comprehensive roadside assistance for all vehicle types</p>
        </div>
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-6" data-aos="fade-right">
                <a href="four_wheeler_services.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="text-center">
                            <img src="pic/car.jpeg" alt="Four Wheeler" class="service-image">
                            <div class="service-header">
                                <h3 class="card-title">Four Wheeler Services</h3>
                            </div>
                        </div>
                        <ul class="check-list row">
                            <div class="col-md-6">
                                <li><i class="fas fa-check-circle"></i> Emergency Repairs</li>
                                <li><i class="fas fa-check-circle"></i> Fuel Delivery</li>
                                <li><i class="fas fa-check-circle"></i> Flat Tyre</li>
                                <li><i class="fas fa-check-circle"></i> Towing Services</li>
                            </div>
                            <div class="col-md-6">
                                <li><i class="fas fa-check-circle"></i> Battery Services</li>
                                <li><i class="fas fa-check-circle"></i> Lockout Assistance</li>
                                <li><i class="fas fa-check-circle"></i> Jump Starts</li>
                                <li><i class="fas fa-check-circle"></i> AC Service</li>
                            </div>
                        </ul>
                    </div>
                </a>
            </div>

            <div class="col-lg-6" data-aos="fade-left">
                <a href="two_wheeler_services.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="text-center">
                            <img src="pic/bike.jpeg" alt="Two Wheeler" class="service-image">
                            <div class="service-header">
                                <h3 class="card-title">Two Wheeler Services</h3>
                            </div>
                        </div>
                        <ul class="check-list row">
                            <div class="col-md-6">
                                <li><i class="fas fa-check-circle"></i> Emergency Repairs</li>
                                <li><i class="fas fa-check-circle"></i> Fuel Delivery</li>
                                <li><i class="fas fa-check-circle"></i> Flat Tyre</li>
                                <li><i class="fas fa-check-circle"></i> Towing Services</li>
                            </div>
                            <div class="col-md-6">
                                <li><i class="fas fa-check-circle"></i> Battery Boosting</li>
                                <li><i class="fas fa-check-circle"></i> Parts Replacement</li>
                                <li><i class="fas fa-check-circle"></i> Chain Repair</li>
                                <li><i class="fas fa-check-circle"></i> Brake Service</li>
                            </div>
                        </ul>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- EMERGENCY ROADSIDE ASSISTANCE -->
<section id="emergency-assistance">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">Emergency Roadside Assistance</h2>
            <p class="section-subtitle">Rapid on-ground help when you need it most — day or night</p>
        </div>
        <div class="row g-4">

            <div class="col-md-3" data-aos="fade-up" data-aos-delay="100">
                <a href="service-on-site-repairs.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="assist-card clickable-card">
                        <div class="assist-card-image">
                            <img src="Emergency_roadside_pics/onsite.jpeg" alt="On-Site Repairs">
                            <div class="assist-badge">24/7</div>
                        </div>
                        <div class="assist-card-content">
                            <h4 class="card-title">On-Site Repairs</h4>
                            <p class="small">Minor mechanical issues fixed right at your breakdown spot — no garage visit needed.</p>
                            <div class="assist-card-footer">
                                <span class="assist-link">Get Help <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3" data-aos="fade-up" data-aos-delay="200">
                <a href="service-towing.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="assist-card clickable-card">
                        <div class="assist-card-image">
                            <img src="Emergency_roadside_pics/tow.jpeg" alt="Towing Service">
                            <div class="assist-badge">Fast</div>
                        </div>
                        <div class="assist-card-content">
                            <h4 class="card-title">Towing Service</h4>
                            <p class="small">Safe and swift towing to the nearest verified garage or your preferred service centre.</p>
                            <div class="assist-card-footer">
                                <span class="assist-link">Get Help <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3" data-aos="fade-up" data-aos-delay="300">
                <a href="service-battery-jump.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="assist-card clickable-card">
                        <div class="assist-card-image">
                            <img src="Emergency_roadside_pics/jumpsark.jpeg" alt="Battery Jump Start">
                            <div class="assist-badge">Instant</div>
                        </div>
                        <div class="assist-card-content">
                            <h4 class="card-title">Battery Jump Start</h4>
                            <p class="small">Instant battery boosting or on-spot replacement so you're back on the road in minutes.</p>
                            <div class="assist-card-footer">
                                <span class="assist-link">Get Help <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-3" data-aos="fade-up" data-aos-delay="400">
                <a href="service-fuel-delivery.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="assist-card clickable-card">
                        <div class="assist-card-image">
                            <img src="Emergency_roadside_pics/fuel.jpeg" alt="Fuel Delivery">
                            <div class="assist-badge">24/7</div>
                        </div>
                        <div class="assist-card-content">
                            <h4 class="card-title">Fuel Delivery</h4>
                            <p class="small">Stranded with an empty tank? We deliver fuel directly to your location — fast and hassle-free.</p>
                            <div class="assist-card-footer">
                                <span class="assist-link">Get Help <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</section>

<!-- WHY CHOOSE US -->
<section id="why-us">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">We provide the best roadside assistance experience</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="feature-card">
                    <div class="card-image-wrapper">
                        <img src="pic/why-choose/fast.jpeg" alt="Faster Service" class="feature-image">
                    </div>
                    <h4 class="card-title">Faster Service</h4>
                    <p class="small">Average response time under 20 minutes with real-time tracking</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="feature-card">
                    <div class="card-image-wrapper">
                        <img src="pic/why-choose/quality.jpeg" alt="Quality Services" class="feature-image">
                    </div>
                    <h4 class="card-title">Quality Services</h4>
                    <p class="small">Certified mechanics and verified service providers</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="feature-card">
                    <div class="card-image-wrapper">
                        <img src="pic/why-choose/parts.jpeg" alt="Genuine Parts" class="feature-image">
                    </div>
                    <h4 class="card-title">Genuine Parts</h4>
                    <p class="small">100% authentic spare parts with warranty</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="feature-card">
                    <div class="card-image-wrapper">
                        <img src="pic/why-choose/affordable.jpeg" alt="Affordable Prices" class="feature-image">
                    </div>
                    <h4 class="card-title">Affordable Prices</h4>
                    <p class="small">Transparent pricing with no hidden charges</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- REVIEWS -->
<section id="reviews">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle">Trusted by thousands of happy customers</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="review-card">
                    <img src="pic/reviewpic/revimg2.avif" alt="Rajesh Kumar Patra" class="reviewer-img">
                    <div class="review-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p class="small mb-3">"My Bike broke down on the highway and they reached within 15 minutes. Fixed the issue quickly and charged a fair price."</p>
                    <h5 class="reviewer-name">Rajesh Kumar</h5>
                    <p class="reviewer-role">Bike Rider</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="review-card">
                    <img src="pic/reviewpic/revimg.avif" alt="Soumya Rani Nayak" class="reviewer-img">
                    <div class="review-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p class="small mb-3">"Battery boosting service for my car was super quick. The mechanic was professional and even checked other parts. Highly recommended!"</p>
                    <h5 class="reviewer-name">Priya Sharma</h5>
                    <p class="reviewer-role">Car Owner</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="review-card">
                    <img src="pic/reviewpic/revimg3.avif" alt="Amit Patel" class="reviewer-img">
                    <div class="review-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                    <p class="small mb-3">"Fuel delivery service saved me during a late-night emergency. Easy to use and transparent pricing. Will definitely use again."</p>
                    <h5 class="reviewer-name">Amit Patel</h5>
                    <p class="reviewer-role">SUV Owner</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="#" class="btn btn-outline-primary btn-lg">View All Reviews <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section id="about">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="section-title">About RoadSide Companion</h2>
                <p class="lead mb-3">We're on a mission to make roadside assistance accessible, reliable, and affordable for everyone.</p>
                <p class="text-muted mb-4">Founded in 2024, RoadSide Companion connects stranded motorists with professional mechanics and service providers within minutes — ensuring you're never alone on the road.</p>
                <div class="row g-3">
                    <div class="col-6 d-flex align-items-center">
                        <i class="fas fa-users fa-2x text-primary me-3"></i>
                        <div>
                            <h3 class="h2 fw-bold mb-0">10K+</h3>
                            <small class="text-muted">Happy Customers</small>
                        </div>
                    </div>
                    <div class="col-6 d-flex align-items-center">
                        <i class="fas fa-tools fa-2x text-primary me-3"></i>
                        <div>
                            <h3 class="h2 fw-bold mb-0">500+</h3>
                            <small class="text-muted">Service Providers</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="position-relative">
                    <img src="pic/why-choose/bottom.jpeg" alt="About Us" class="img-fluid rounded-4 shadow-lg" style="border: 1px solid rgba(255,255,255,0.1);">
                    <div class="position-absolute bottom-0 end-0 bg-white p-3 rounded-4 shadow m-3" style="background: rgba(0,0,0,0.8) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="d-flex align-items-center">
                            <div style="width:45px;height:45px;background:var(--primary);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;" class="me-3">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">24/7 Emergency</small>
                                <strong style="color: white;">1800-123-4567</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SOS BUTTON -->
<button class="sos-btn" id="sosBtn">
    <i class="fas fa-exclamation-triangle"></i>
    <span>SOS</span>
</button>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 900, once: true, offset: 80 });

    window.addEventListener('scroll', () => {
        document.querySelector('.navbar').style.boxShadow =
            window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)';
    });

    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', e => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        });
    });

    document.getElementById('sosBtn').addEventListener('click', () => {
        if (confirm('🚨 EMERGENCY SOS 🚨\n\nDo you need immediate roadside assistance?'))
            alert('Emergency services have been notified! Help is on the way.');
    });

    // ── SLIDER ──
    const track        = document.getElementById('sliderTrack');
    const dots         = document.querySelectorAll('.dot');
    const progressFill = document.getElementById('progressFill');
    const TOTAL        = dots.length;
    const INTERVAL     = 2000;
    let current = 0, autoTimer, progTimer, progStart;

    function goTo(index) {
        current = (index + TOTAL) % TOTAL;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === current));
        resetProgress();
    }

    function resetProgress() {
        progressFill.style.width = '0%';
        clearInterval(progTimer);
        progStart = Date.now();
        progTimer = setInterval(() => {
            progressFill.style.width = Math.min(((Date.now() - progStart) / INTERVAL) * 100, 100) + '%';
        }, 50);
    }

    function startAuto() { autoTimer = setInterval(() => goTo(current + 1), INTERVAL); resetProgress(); }
    function stopAuto()  { clearInterval(autoTimer); clearInterval(progTimer); }

    document.getElementById('nextSlide').addEventListener('click', () => { stopAuto(); goTo(current + 1); startAuto(); });
    document.getElementById('prevSlide').addEventListener('click', () => { stopAuto(); goTo(current - 1); startAuto(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { stopAuto(); goTo(i); startAuto(); }));

    let touchX = 0;
    let touchActive = false;

    track.addEventListener('touchstart', e => { 
        touchX = e.touches[0].clientX; 
        touchActive = true;
        stopAuto();
    });
    track.addEventListener('touchmove', e => { e.preventDefault(); });
    track.addEventListener('touchend', e => {
        if (touchActive) {
            const diff = touchX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) {
                goTo(diff > 0 ? current + 1 : current - 1);
            }
            touchActive = false;
            startAuto();
        }
    });

    document.querySelectorAll('.slide').forEach((slide, index) => {
        slide.addEventListener('click', function(e) {
            if (e.target.closest('.slider-dots') || e.target.closest('.slider-arrow')) return;
            window.location.href = '/services.php';
        });
        slide.style.cursor = 'pointer';
    });

    goTo(0);
    startAuto();

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
        if(error.code == 1) alert("Location permission denied.");
        else if(error.code == 2) alert("Location unavailable.");
        else if(error.code == 3) alert("Location request timeout.");
    }
</script>
</body>
</html>
<?php include_once 'footer.php'; ?>