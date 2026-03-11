<?php
include_once 'navbar.php';
require_once '../dbcon.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Four Wheeler Services - RoadSide Companion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* ── DESIGN TOKENS ── */
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

        /* ── BASE - Matching homepage background ── */
        * { font-family: 'Poppins', sans-serif; }
        
        body { 
            background: url('vehiclebg.png') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            color: white;
            position: relative;
        }
        
        /* Dark overlay */
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

        /* Main container */
        .page-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            color: white;
        }

        /* ── BUTTONS ── */
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
            margin-top: 60px;
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

        /* ── SECTION HEADINGS ── */
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

/* ── SERVICE DETAIL CARD ── */
/* Service Card Styles */
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

/* Responsive adjustments */
@media (max-width: 768px) {
    .service-card-image {
        height: 160px;
    }
    
    .service-card-title {
        font-size: 1rem;
    }
    
    .service-card-desc {
        font-size: 0.8rem;
        min-height: 50px;
    }
    
    .offer-price {
        font-size: 1.2rem;
    }
    
    .original-price {
        font-size: 0.8rem;
    }
}


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

        /* ── FEATURE BOXES ── */
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

        /* ── PRICING CARDS ── */
        .pricing-card {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            height: 100%;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .pricing-card:hover {
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(13,110,253,0.3);
            transform: translateY(-5px);
        }
        
        .pricing-card.popular {
            border: 2px solid var(--primary);
            transform: scale(1.02);
        }
        
        .popular-badge {
            position: absolute;
            top: 15px;
            right: -30px;
            background: var(--primary);
            color: white;
            padding: 5px 30px;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .pricing-icon {
            width: 70px;
            height: 70px;
            background: rgba(13,110,253,0.1);
            border: 2px solid var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .pricing-icon i {
            font-size: 2rem;
            color: var(--primary);
        }
        
        .pricing-card h3 {
            color: white;
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .price span {
            font-size: 1rem;
            color: rgba(255,255,255,0.6);
            font-weight: 400;
        }
        
        .pricing-features {
            list-style: none;
            padding: 0;
            margin: 0 0 25px;
            text-align: left;
        }
        
        .pricing-features li {
            padding: 8px 0;
            color: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
        }
        
        .pricing-features li i {
            color: var(--primary);
            margin-right: 10px;
            font-size: 0.9rem;
        }

        /* ── SERVICE LIST ── */
        .service-list {
            list-style: none;
            padding: 0;
        }
        
        .service-list li {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.9);
            font-size: 1rem;
        }
        
        .service-list li:last-child {
            border-bottom: none;
        }
        
        .service-list li i {
            color: var(--primary);
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .service-list li .service-name {
            flex: 1;
        }
        
        .service-list li .service-price {
            color: var(--primary);
            font-weight: 600;
        }

        /* ── TESTIMONIAL CARDS ── */
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

        /* ── CTA SECTION ── */
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

        /* ── BACK TO HOME BUTTON ── */
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

        /* ── ANIMATIONS ── */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.8rem;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .service-detail-card {
                padding: 25px;
            }
            
            .cta-box {
                padding: 30px 20px;
            }
            
            .cta-box h2 {
                font-size: 1.6rem;
            }
            
            .pricing-card.popular {
                transform: scale(1);
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="container">
                <h1 data-aos="fade-down">Four Wheeler Services</h1>
                <p data-aos="fade-down" data-aos-delay="100">Complete care for your car - from emergency repairs to routine maintenance</p>
                <nav aria-label="breadcrumb" data-aos="fade-down" data-aos-delay="200">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Four Wheeler Services</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- MAIN SERVICE DETAIL SECTION -->
        <section>
            <div class="container">
                <div class="service-detail-card" data-aos="fade-up">
                    <div class="row">
                        <div class="col-lg-6">
                            <img src="pic/car.jpeg" alt="Four Wheeler Service" class="service-main-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-icon-large">
                                <i class="fas fa-car"></i>
                            </div>
                            <h2 class="section-title text-center mb-4">Complete Car Care</h2>
                            <p style="color: rgba(255,255,255,0.9); line-height: 1.8; margin-bottom: 25px;">
                                Our four wheeler services are designed to provide comprehensive care for your vehicle. 
                                From emergency roadside assistance to routine maintenance, our certified mechanics ensure 
                                your car stays in top condition. We use genuine parts and provide transparent pricing with 
                                no hidden charges.
                            </p>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">24/7 Availability</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">Certified Mechanics</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">Genuine Parts</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size: 1.2rem;"></i>
                                        <span style="color: white;">30 Min Response</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ALL FOUR WHEELER SERVICES -->
<section>
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">All Four Wheeler Services</h2>
            <p class="section-subtitle">Comprehensive list of services we offer for your car</p>
        </div>
        
        <!-- Service Cards Row 1 -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Emergency Repairs -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <a href="service-emergency-repairs.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=600&q=80" alt="Emergency Repairs">
                            <div class="service-badge">24/7</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Emergency Repairs</h3>
                            <p class="service-card-desc">Immediate on-spot repairs for breakdowns, engine issues, and mechanical failures</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹700</span>
                                    <span class="offer-price">₹450(Expected)</span>
                                </div>
                                <span class="discount-badge">SAVE 38%</span>
                            </div>
                            <div class="service-card-footer">
                                <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.8</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Card 2: Fuel Delivery -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <a href="service-fuel-delivery.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1591886960571-74d43a9d4166?auto=format&fit=crop&w=600&q=80" alt="Fuel Delivery">
                            <div class="service-badge">Fast</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Fuel Delivery</h3>
                            <p class="service-card-desc">Emergency fuel delivery when you run out of gas on the road. Petrol & diesel available</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹110 per liter</span>
                                    <span class="offer-price">₹105</span>
                                </div>
                                <span class="discount-badge">SAVE 4%</span>
                            </div>
                            <div class="service-card-footer">
                                <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.9</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Card 3: Flat Tyre -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <a href="service-flat-tyre.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&w=600&q=80" alt="Flat Tyre">
                            <div class="service-badge">Popular</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Flat Tyre Assistance</h3>
                            <p class="service-card-desc">Quick tyre change, puncture repair, and spare tyre installation at your location</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹300</span>
                                    <span class="offer-price">₹200</span>
                                </div>
                                <span class="discount-badge">SAVE 18%</span>
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
        
        <!-- Service Cards Row 2 -->
        <div class="row g-4 mb-4">
            <!-- Card 4: Towing Services -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <a href="service-towing.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1603745713042-00b57d8d9e2a?auto=format&fit=crop&w=600&q=80" alt="Towing Services">
                            <div class="service-badge">24/7</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Towing Services</h3>
                            <p class="service-card-desc">Professional towing to nearest garage or your preferred service center. All vehicle types</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹1000</span>
                                    <span class="offer-price">₹800</span>
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
            
            <!-- Card 5: Battery Services -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <a href="service-battery.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1625047509168-a7026f36de04?auto=format&fit=crop&w=600&q=80" alt="Battery Services">
                            <div class="service-badge">Warranty</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Battery Services</h3>
                            <p class="service-card-desc">Jump start, battery testing, replacement with genuine batteries & 1-year warranty</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹500 for Full Charge</span>
                                    <span class="offer-price">₹450</span>
                                </div>
                                <span class="discount-badge">SAVE 4%</span>
                            </div>
                            <div class="service-card-footer">
                                <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.9</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Card 6: Lockout Assistance -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <a href="service-lockout.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1558008258-3256797b43f3?auto=format&fit=crop&w=600&q=80" alt="Lockout Assistance">
                            <div class="service-badge">Fast</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Lockout Assistance</h3>
                            <p class="service-card-desc">Car lockout service - key extraction, lock opening, and replacement keys</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹200</span>
                                    <span class="offer-price">$180</span>
                                </div>
                                <span class="discount-badge">SAVE 3%</span>
                            </div>
                            <div class="service-card-footer">
                                <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.6</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Service Cards Row 3 -->
        <div class="row g-4">
            <!-- Card 7: Jump Starts -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <a href="service-jump-start.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1619642751034-765dfdf87c9f?auto=format&fit=crop&w=600&q=80" alt="Jump Starts">
                            <div class="service-badge">Instant</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Jump Starts</h3>
                            <p class="service-card-desc">Quick battery jump start service to get your vehicle running again in minutes</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹300</span>
                                    <span class="offer-price">₹270</span>
                                </div>
                                <span class="discount-badge">SAVE 6%</span>
                            </div>
                            <div class="service-card-footer">
                                <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.8</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Card 8: AC Service -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <a href="service-ac.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1612825173281-9a193378527e?auto=format&fit=crop&w=600&q=80" alt="AC Service">
                            <div class="service-badge">Seasonal</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">AC Service</h3>
                            <p class="service-card-desc">Car AC repair, gas refill, cooling check, and complete AC maintenance</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹1000(Expeted)</span>
                                    <span class="offer-price">₹850</span>
                                </div>
                                <span class="discount-badge">SAVE 5%</span>
                            </div>
                            <div class="service-card-footer">
                                <span class="service-link">View Details <i class="fas fa-arrow-right"></i></span>
                                <span class="service-rating"><i class="fas fa-star text-warning"></i> 4.7</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Card 9: Oil Change -->
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <a href="service-oil-change.php" style="text-decoration: none; display: block; height: 100%;">
                    <div class="service-card clickable-card">
                        <div class="service-card-image">
                            <img src="https://images.unsplash.com/photo-1597170346290-7e8e07a2e8de?auto=format&fit=crop&w=600&q=80" alt="Oil Change">
                            <div class="service-badge">Popular</div>
                        </div>
                        <div class="service-card-content">
                            <h3 class="service-card-title">Oil Change</h3>
                            <p class="service-card-desc">Engine oil change, filter replacement, and complete lubrication service</p>
                            <div class="service-price-row">
                                <div>
                                    <span class="original-price">₹2000(Expected)</span>
                                    <span class="offer-price">₹1500</span>
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
        </div>
    </div>
</section>

        <!-- KEY FEATURES -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">Why Choose Our Car Services</h2>
                    <p class="section-subtitle">We provide the best care for your vehicle</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                        <div class="feature-box">
                            <i class="fas fa-clock"></i>
                            <h4>24/7 Emergency</h4>
                            <p>Round-the-clock assistance for any car emergency, anytime, anywhere.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                        <div class="feature-box">
                            <i class="fas fa-user-cog"></i>
                            <h4>Expert Mechanics</h4>
                            <p>Certified professionals with years of experience in car repair.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                        <div class="feature-box">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Warranty</h4>
                            <p>6-month warranty on all repairs and genuine parts used.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CUSTOMER TESTIMONIALS -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">What Car Owners Say</h2>
                    <p class="section-subtitle">Trusted by thousands of satisfied customers</p>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"My car broke down on the highway at midnight. They reached within 20 minutes and fixed the issue. Amazing service!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Rahul Mehta">
                                <div class="author-info">
                                    <h5>Rahul Mehta</h5>
                                    <p>Honda City Owner</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="testimonial-text">"Great experience with battery replacement service. The mechanic was professional and the price was very reasonable."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Priya Singh">
                                <div class="author-info">
                                    <h5>Priya Singh</h5>
                                    <p>Hyundai i20 Owner</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="testimonial-card">
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <p class="testimonial-text">"Towing service was quick and hassle-free. They took my car to the nearest service center. Highly recommended!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/52.jpg" alt="Vikram Sharma">
                                <div class="author-info">
                                    <h5>Vikram Sharma</h5>
                                    <p>Ford EcoSport Owner</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BACK TO HOME BUTTON -->
        <a href="index.html" class="back-home-btn" title="Back to Home">
            <i class="fas fa-home"></i>
        </a>

        

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Init AOS scroll animations
        AOS.init({ duration: 900, once: true, offset: 80 });

        // Navbar shadow on scroll
        window.addEventListener('scroll', () => {
            document.querySelector('.navbar').style.boxShadow =
                window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)';
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener('click', e => {
                const target = document.querySelector(link.getAttribute('href'));
                if (target) { 
                    e.preventDefault(); 
                    target.scrollIntoView({ behavior: 'smooth' }); 
                }
            });
        });
    </script>
</body>
</html>