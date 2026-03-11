<?php
include_once 'navbar.php';
require_once '../dbcon.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two Wheeler Services - RoadSide Companion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* ── DESIGN TOKENS (exactly same as four-wheeler) ── */
        :root {
            --primary:   #0d6efd;
            --dark-blue: #0b5ed7;
            --accent:    #0d6efd;
            --success:   #20B2AA;
            --warning:   #FFA500;
            --gray:      rgba(255,255,255,0.8);
            --light-bg:  transparent;
            --border:    rgba(255,255,255,0.2);
            --icon-bg:   rgba(13,110,253,0.1);
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

        .page-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            color: white;
        }

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

        /* ── PAGE HEADER ── */
        .page-header {
            margin-top: 80px;
            padding: 30px 0;
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 8px;
            text-shadow: 0 0 15px rgba(13,110,253,0.5);
        }
        
        .page-header p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .breadcrumb {
            background: transparent;
            justify-content: center;
            margin-top: 15px;
        }
        
        .breadcrumb-item {
            color: rgba(255,255,255,0.7);
        }
        
        .breadcrumb-item a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: white;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: rgba(255,255,255,0.4);
        }

        section { 
            padding: 40px 0; 
            position: relative;
        }
        
        .section-title { 
            font-family: 'Orbitron', sans-serif;
            font-size: 2rem; 
            font-weight: 700; 
            color: white; 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            margin-bottom: 2px; 
            text-shadow: 0 0 10px rgba(13,110,253,0.3);
        }
        
        .section-subtitle { 
            color: rgba(255,255,255,0.8); 
            margin-bottom: 30px; 
        }

        /* Service Card Styles (identical to four-wheeler) */
        .service-card {
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

        .service-card.clickable-card {
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(13,110,253,0.4);
        }

        .service-card-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .service-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .service-card:hover .service-card-image img {
            transform: scale(1.1);
        }

        .service-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 0 15px rgba(13,110,253,0.5);
            z-index: 2;
        }

        .service-card-content {
            padding: 20px;
        }

        .service-card-title {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .service-card-desc {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin-bottom: 15px;
            line-height: 1.5;
            min-height: 65px;
        }

        .service-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .original-price {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            text-decoration: line-through;
            margin-right: 8px;
        }

        .offer-price {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .discount-badge {
            background: rgba(255,193,7,0.2);
            color: #ffc107;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(255,193,7,0.3);
        }

        .service-card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .service-link {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .service-link i {
            transition: transform 0.3s;
        }

        .service-card:hover .service-link i {
            transform: translateX(5px);
        }

        .service-rating {
            color: rgba(255,255,255,0.8);
            font-size: 0.85rem;
        }

        .service-rating i {
            margin-right: 3px;
        }

        /* service-detail-card (exact same) */
        .service-detail-card {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
            margin-bottom: 30px;
        }

        .service-main-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--primary);
            box-shadow: 0 0 30px rgba(13,110,253,0.3);
            margin-bottom: 30px;
        }

        .service-icon-large {
            width: 80px;
            height: 80px;
            background: rgba(13,110,253,0.1);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .service-icon-large i {
            font-size: 2.5rem;
            color: var(--primary);
        }

        /* feature boxes */
        .feature-box {
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 25px 20px;
            text-align: center;
            height: 100%;
            transition: 0.3s;
        }
        
        .feature-box:hover {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(13,110,253,0.3);
            transform: translateY(-5px);
        }
        
        .feature-box i {
            font-size: 2.2rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        .feature-box h4 {
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .feature-box p {
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* testimonial cards */
        .testimonial-card {
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 25px;
            height: 100%;
            transition: 0.3s;
        }
        
        .testimonial-card:hover {
            border-color: var(--primary);
            box-shadow: 0 0 20px rgba(13,110,253,0.2);
        }
        
        .testimonial-stars {
            color: var(--warning);
            margin-bottom: 15px;
        }
        
        .testimonial-text {
            color: rgba(255,255,255,0.9);
            font-style: italic;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid var(--primary);
        }
        
        .author-info h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 1rem;
        }
        
        .author-info p {
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        /* cta */
        .cta-box {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 50px;
            text-align: center;
            margin: 30px 0;
        }
        
        .cta-box h2 {
            font-family: 'Orbitron', sans-serif;
            color: white;
            font-size: 2.2rem;
            margin-bottom: 20px;
        }
        
        .cta-box p {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        /* back home button */
        .back-home-btn {
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            box-shadow: 0 5px 20px rgba(13,110,253,0.4);
            z-index: 999;
            cursor: pointer;
            transition: 0.3s;
            border: 2px solid rgba(255,255,255,0.3);
            text-decoration: none;
        }
        
        .back-home-btn:hover {
            background: var(--dark-blue);
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(13,110,253,0.6);
            color: white;
        }


        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.8rem; }
            .section-title { font-size: 1.6rem; }
            .service-detail-card { padding: 25px; }
            .cta-box { padding: 30px 20px; }
            .cta-box h2 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>
    <div class="page-container">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="container">
                <h1 data-aos="fade-down">Two Wheeler Services</h1>
                <p data-aos="fade-down" data-aos-delay="100">Specialized care for bikes & scooters – from puncture repair to performance tuning</p>
                <nav aria-label="breadcrumb" data-aos="fade-down" data-aos-delay="200">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Two Wheeler Services</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- MAIN SERVICE DETAIL SECTION (two-wheeler theme) -->
        <section>
            <div class="container">
                <div class="service-detail-card" data-aos="fade-up">
                    <div class="row">
                        <div class="col-lg-6">
                            <img src="https://images.unsplash.com/photo-1558981285-6f0c94958bb6?auto=format&fit=crop&w=600&q=80" alt="Two Wheeler Service" class="service-main-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-icon-large">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <h2 class="section-title text-center mb-4">Complete Bike & Scooter Care</h2>
                            <p style="color: rgba(255,255,255,0.9); line-height: 1.8; margin-bottom: 25px;">
                                From roadside breakdowns to regular maintenance, our two-wheeler experts have you covered. 
                                We offer quick puncture repair, chain lubrication, brake adjustment, and emergency fuel delivery 
                                – all at transparent prices. Certified mechanics with bike-specific tools.
                            </p>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">15 Min Response</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">Two-Wheeler Pros</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">Original Spares</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">Roadside & Workshop</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ALL TWO WHEELER SERVICES (3 rows, 9 cards, with 2-3 different from four-wheeler) -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">All Two Wheeler Services</h2>
                    <p class="section-subtitle">Everything your bike or scooter needs, on the go</p>
                </div>
                
                <!-- Row 1 -->
                <div class="row g-4 mb-4">
                    <!-- Card 1: Puncture Repair (different from four-wheeler) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <a href="service-puncture-repair.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1609636606920-e479546ac1d8?auto=format&fit=crop&w=600&q=80" alt="Puncture Repair">
                                    <div class="service-badge">Popular</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Puncture Repair</h3>
                                    <p class="service-card-desc">On-spot tyre puncture fix, tube or tubeless, using professional tools.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹150</span>
                                            <span class="offer-price">₹99</span>
                                        </div>
                                        <span class="discount-badge">SAVE 34%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.9</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Card 2: Fuel Delivery (same concept, but two-wheeler pricing) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <a href="service-fuel-delivery-bike.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.plus.unsplash.com/premium_photo-1664303847962-586ccfd28c27?auto=format&fit=crop&w=600&q=80" alt="Fuel Delivery Bike">
                                    <div class="service-badge">24/7</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Fuel Delivery</h3>
                                    <p class="service-card-desc">Emergency petrol/diesel delivery for bikes & scooters.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹130/L</span>
                                            <span class="offer-price">₹120/L</span>
                                        </div>
                                        <span class="discount-badge">SAVE 8%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.8</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Card 3: Chain Lubrication (different) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <a href="service-chain-lube.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1625047509168-a7026f36de04?auto=format&fit=crop&w=600&q=80" alt="Chain Lubrication">
                                    <div class="service-badge">Essential</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Chain Lubrication</h3>
                                    <p class="service-card-desc">Complete chain cleaning and lubrication for smooth ride.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹350</span>
                                            <span class="offer-price">₹199</span>
                                        </div>
                                        <span class="discount-badge">SAVE 43%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.7</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="row g-4 mb-4">
                    <!-- Card 4: Brake & Clutch Adjustment (different) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <a href="service-brake-adjust.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&w=600&q=80" alt="Brake Adjustment">
                                    <div class="service-badge">Safety</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Brake & Clutch Adj.</h3>
                                    <p class="service-card-desc">Adjust brakes, clutch free play, and cable lubrication.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹300</span>
                                            <span class="offer-price">₹199</span>
                                        </div>
                                        <span class="discount-badge">SAVE 34%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.8</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Card 5: Battery Jump-Start (two-wheeler specific) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <a href="service-bike-jump.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1619642751034-765dfdf87c9f?auto=format&fit=crop&w=600&q=80" alt="Bike Jump Start">
                                    <div class="service-badge">Instant</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Battery Jump-Start</h3>
                                    <p class="service-card-desc">Quick boost for dead battery – portable jump-starter.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹300</span>
                                            <span class="offer-price">₹249</span>
                                        </div>
                                        <span class="discount-badge">SAVE 17%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.9</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Card 6: Towing for bikes (different) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <a href="service-bike-towing.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1603745713042-00b57d8d9e2a?auto=format&fit=crop&w=600&q=80" alt="Bike Towing">
                                    <div class="service-badge">24/7</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Bike Towing</h3>
                                    <p class="service-card-desc">Reliable towing to nearest garage or home.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹600</span>
                                            <span class="offer-price">₹450</span>
                                        </div>
                                        <span class="discount-badge">SAVE 25%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.8</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="row g-4">
                    <!-- Card 7: Oil Change (two-wheeler) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <a href="service-bike-oil.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1597170346290-7e8e07a2e8de?auto=format&fit=crop&w=600&q=80" alt="Bike Oil Change">
                                    <div class="service-badge">Maintenance</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Oil Change</h3>
                                    <p class="service-card-desc">Engine oil replacement with premium grade oil.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹700</span>
                                            <span class="offer-price">₹550</span>
                                        </div>
                                        <span class="discount-badge">SAVE 21%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.9</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Card 8: Carburetor/Injector Cleaning (different) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <a href="service-carb-cleaning.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1625047509168-a7026f36de04?auto=format&fit=crop&w=600&q=80" alt="Carb Cleaning">
                                    <div class="service-badge">Performance</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Carb / Injector Clean</h3>
                                    <p class="service-card-desc">Deep cleaning for smooth engine performance.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹550</span>
                                            <span class="offer-price">₹399</span>
                                        </div>
                                        <span class="discount-badge">SAVE 27%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.6</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Card 9: Spark Plug Replacement (different) -->
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <a href="service-spark-plug.html" style="text-decoration: none; display: block; height: 100%;">
                            <div class="service-card clickable-card">
                                <div class="service-card-image">
                                    <img src="https://images.unsplash.com/photo-1612825173281-9a193378527e?auto=format&fit=crop&w=600&q=80" alt="Spark Plug">
                                    <div class="service-badge">Quick Fix</div>
                                </div>
                                <div class="service-card-content">
                                    <h3 class="service-card-title">Spark Plug Replacement</h3>
                                    <p class="service-card-desc">Diagnose and replace faulty spark plug.</p>
                                    <div class="service-price-row">
                                        <div>
                                            <span class="original-price">₹300</span>
                                            <span class="offer-price">₹199</span>
                                        </div>
                                        <span class="discount-badge">SAVE 34%</span>
                                    </div>
                                    <div class="service-card-footer">
                                        <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                        <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.8</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- KEY FEATURES (two-wheeler oriented) -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">Why Two-Wheeler Riders Trust Us</h2>
                    <p class="section-subtitle">We speak bike & scooter language</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                        <div class="feature-box">
                            <i class="fas fa-motorcycle"></i>
                            <h4>Bike Specialists</h4>
                            <p>Mechanics trained specifically for two-wheelers – from scooters to superbikes.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                        <div class="feature-box">
                            <i class="fas fa-tachometer-alt"></i>
                            <h4>Quick Turnaround</h4>
                            <p>Average 20-minute response, most repairs done on the spot.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                        <div class="feature-box">
                            <i class="fas fa-hand-holding-heart"></i>
                            <h4>Genuine Spares</h4>
                            <p>We use authentic parts with 3-month warranty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS (bike owners) -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">Happy Riders</h2>
                    <p class="section-subtitle">Real stories from two-wheeler owners</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testimonial-text">"Chain snapped on highway. They arrived in 15 mins with tools and fixed it temporarily, then towed to workshop. Lifesavers!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Arjun Raj">
                                <div class="author-info"><h5>Arjun Raj</h5><p>Royal Enfield Rider</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testimonial-text">"Fuel delivery at midnight – reasonable price and the guy even checked my tyre pressure. Highly recommended!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Neha Sharma">
                                <div class="author-info"><h5>Neha Sharma</h5><p>Activa Scooter</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                            <p class="testimonial-text">"Regular service at home – oil change, chain lube, brake check. Saved me a trip to the garage. Professional work."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Vikram Joshi">
                                <div class="author-info"><h5>Vikram Joshi</h5><p>Pulsar Owner</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
    </script>
</body>
</html>
<?php 
include_once 'footer.php'; 
?>