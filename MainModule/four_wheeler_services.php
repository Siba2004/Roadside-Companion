<?php
include_once 'navbar.php';
require_once '../dbcon.php';

// Fetch all active four wheeler services
$query = "SELECT * FROM four_wheeler_services WHERE status = 'active' ORDER BY display_order ASC, id ASC";
$result = mysqli_query($conn, $query);
$services = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $services[] = $row;
    }
}

// Group services into rows of 3 for better display
$services_chunks = array_chunk($services, 3);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Four Wheeler Services - RoadSide Companion</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
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
            --card-bg:   rgba(20, 20, 30, 0.85);
        }

        * { font-family: 'Poppins', sans-serif; }

        body {
            background: #0a0a0f;
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
            background: linear-gradient(135deg, rgba(13,110,253,0.03) 0%, rgba(0,0,0,0.8) 100%);
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

        .page-header {
            margin-top: 10px;
            padding: 2px 0;
            text-align: center;
        }
        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 2px;
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
            margin-top: 20px;
        }
        .breadcrumb-item { color: rgba(255,255,255,0.7); }
        .breadcrumb-item a { color: var(--primary); text-decoration: none; }
        .breadcrumb-item.active { color: white; }
        .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

        section { padding: 10px 0; position: relative; }

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
        .section-subtitle { color: rgba(255,255,255,0.8); margin-bottom: 40px; }

        .service-card {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
        }
        .service-card.clickable-card { cursor: pointer; }
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
        .service-card:hover .service-card-image img { transform: scale(1.1); }
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
        .service-card-content { padding: 20px; }
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
        .offer-price { color: white; font-size: 1.5rem; font-weight: 700; }
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
            text-decoration: none;
        }
        .service-link i { transition: transform 0.3s; }
        .service-card:hover .service-link i { transform: translateX(5px); }
        .service-rating { color: rgba(255,255,255,0.8); font-size: 0.85rem; }
        .service-rating i { margin-right: 3px; }

        .service-detail-card {
            background: var(--card-bg);
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
        .service-icon-large i { font-size: 2.5rem; color: var(--primary); }

        .feature-box {
            background: var(--card-bg);
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
        .feature-box i { font-size: 2.2rem; color: var(--primary); margin-bottom: 15px; }
        .feature-box h4 { color: white; font-weight: 600; font-size: 1.2rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .feature-box p { color: rgba(255,255,255,0.8); font-size: 0.9rem; margin-bottom: 0; }

        .testimonial-card {
            background: var(--card-bg);
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
        .testimonial-stars { color: var(--warning); margin-bottom: 15px; }
        .testimonial-text { color: rgba(255,255,255,0.9); font-style: italic; margin-bottom: 20px; line-height: 1.6; }
        .testimonial-author { display: flex; align-items: center; }
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid var(--primary);
        }
        .author-info h5 { color: white; font-weight: 600; margin-bottom: 5px; font-size: 1rem; }
        .author-info p { color: rgba(255,255,255,0.7); font-size: 0.8rem; margin-bottom: 0; }

        .cta-box {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 50px;
            text-align: center;
            margin: 30px 0;
        }
        .cta-box h2 { font-family: 'Orbitron', sans-serif; color: white; font-size: 2.2rem; margin-bottom: 20px; }
        .cta-box p { color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 30px; }

        .admin-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .admin-buttons .btn {
            margin: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.8rem; }
            .section-title { font-size: 1.6rem; }
            .service-detail-card { padding: 25px; }
            .cta-box { padding: 30px 20px; }
            .cta-box h2 { font-size: 1.6rem; }
            .service-card-image { height: 160px; }
            .service-card-title { font-size: 1rem; }
            .service-card-desc { font-size: 0.8rem; min-height: 50px; }
            .offer-price { font-size: 1.2rem; }
            .original-price { font-size: 0.8rem; }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="container">
                <h1 data-aos="fade-down">Four Wheeler Services</h1>
                <p data-aos="fade-down" data-aos-delay="100">Specialized care for cars & SUVs – from puncture repair to engine diagnostics</p>
            </div>
        </div>

        <!-- MAIN SERVICE DETAIL SECTION -->
        <section>
            <div class="container">
                <div class="service-detail-card" data-aos="fade-up">
                    <div class="row">
                        <div class="col-lg-6">
                            <img src="https://images.unsplash.com/photo-1486006920555-c77dcf18193c?auto=format&fit=crop&w=600&q=80" alt="Four Wheeler Service" class="service-main-image">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-icon-large"><i class="fas fa-car"></i></div>
                            <h2 class="section-title text-center mb-4">Complete Car & SUV Care</h2>
                            <p style="color: rgba(255,255,255,0.9); line-height: 1.8; margin-bottom: 25px;">
                                From roadside breakdowns to regular maintenance, our four-wheeler experts have you covered. 
                                We offer quick puncture repair, engine diagnostics, brake servicing, and emergency fuel delivery 
                                – all at transparent prices. Certified mechanics with car-specific tools.
                            </p>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size:1.2rem;"></i>
                                        <span style="color:white;">30 Min Response</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size:1.2rem;"></i>
                                        <span style="color:white;">Car Specialists</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size:1.2rem;"></i>
                                        <span style="color:white;">Genuine Spares</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-2" style="font-size:1.2rem;"></i>
                                        <span style="color:white;">Roadside & Workshop</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ALL FOUR WHEELER SERVICES (Dynamic from Database) -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">All Four Wheeler Services</h2>
                    <p class="section-subtitle">Everything your car or SUV needs, on the go</p>
                </div>
                
                <?php if (empty($services)): ?>
                    <div class="alert alert-info text-center">No services available at the moment. Please check back later.</div>
                <?php else: ?>
                    <?php foreach ($services_chunks as $chunk): ?>
                        <div class="row g-4 mb-4">
                            <?php foreach ($chunk as $service): ?>
                                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                                    <a href="book_service.php?service=<?php echo urlencode($service['service_name']); ?>&price=<?php echo $service['offer_price']; ?>&type=four_wheeler" style="text-decoration: none; display: block; height: 100%;">
                                        <div class="service-card clickable-card">
                                            <div class="service-card-image">
                                                <img src="<?php echo htmlspecialchars($service['image_path'] ?: 'services_pics/four_wheeler_services_pics/default.jpg'); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                                                <?php if ($service['badge_text']): ?>
                                                    <div class="service-badge"><?php echo htmlspecialchars($service['badge_text']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="service-card-content">
                                                <h3 class="service-card-title"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                                <p class="service-card-desc"><?php echo htmlspecialchars($service['description']); ?></p>
                                                <div class="service-price-row">
                                                    <div>
                                                        <?php if ($service['original_price'] > 0): ?>
                                                            <span class="offer-price">₹<?php echo number_format($service['offer_price']); ?></span>
                                                        <?php endif; ?>
                                                        <span class="original-price">₹<?php echo number_format($service['original_price']); ?></span>
                                                    </div>
                                                    <?php if ($service['discount_percent'] > 0): ?>
                                                        <span class="discount-badge">SAVE <?php echo $service['discount_percent']; ?>%</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="service-card-footer">
                                                    <span class="service-link">Book Now <i class="fas fa-arrow-right"></i></span>
                                                    <span class="service-rating">
                                                        <i class="fas fa-star text-warning"></i> <?php echo number_format($service['rating'], 1); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- KEY FEATURES -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">Why Car Owners Trust Us</h2>
                    <p class="section-subtitle">We speak car & SUV language</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100">
                        <div class="feature-box">
                            <i class="fas fa-car"></i>
                            <h4>Car Specialists</h4>
                            <p>Mechanics trained specifically for four-wheelers – from hatchbacks to SUVs.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200">
                        <div class="feature-box">
                            <i class="fas fa-tachometer-alt"></i>
                            <h4>Quick Turnaround</h4>
                            <p>Average 30-minute response, most repairs done on the spot.</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300">
                        <div class="feature-box">
                            <i class="fas fa-hand-holding-heart"></i>
                            <h4>Genuine Spares</h4>
                            <p>We use authentic parts with 6-month warranty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up">
                    <h2 class="section-title">Happy Drivers</h2>
                    <p class="section-subtitle">Real stories from car owners</p>
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
                            <p class="testimonial-text">"Engine overheated on the highway. They arrived in 20 mins with coolant and got me running. Lifesavers!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rajesh Kumar">
                                <div class="author-info">
                                    <h5>Rajesh Kumar</h5>
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
                            <p class="testimonial-text">"Flat tyre at midnight – reasonable price and the guy even checked my spare tyre. Highly recommended!"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Anita Desai">
                                <div class="author-info">
                                    <h5>Anita Desai</h5>
                                    <p>Maruti Suzuki Swift</p>
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
                            <p class="testimonial-text">"Regular service at home – oil change, brake check, filter replacement. Saved me a trip to the garage. Professional work."</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Suresh Raina">
                                <div class="author-info">
                                    <h5>Suresh Raina</h5>
                                    <p>Hyundai Creta Owner</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CALL TO ACTION -->
        <section>
            <div class="container">
                <div class="cta-box" data-aos="zoom-in">
                    <h2>Need Emergency Assistance?</h2>
                    <p>Our team is ready 24/7 to help you get back on the road</p>
                    <a href="tel:+911234567890" class="btn btn-primary btn-lg">
                        <i class="fas fa-phone-alt"></i> Call Now: +91 1234567890
                    </a>
                </div>
            </div>
        </section>
    </div>

    <!-- Admin Quick Actions (Visible only to admin users) -->
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="admin-buttons">
        <a href="admin/add_service.php?type=four_wheeler" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus"></i> Add Service
        </a>
        <a href="admin/manage_services.php?type=four_wheeler" class="btn btn-outline-primary rounded-pill">
            <i class="fas fa-edit"></i> Manage Services
        </a>
    </div>
    <?php endif; ?>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 900,
                once: true,
                offset: 80,
                throttleDelay: 50
            });
            window.addEventListener('load', AOS.refresh);
        });

        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.style.boxShadow = window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)';
            }
        });

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
<?php include_once 'footer.php'; ?>