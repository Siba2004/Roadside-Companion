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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Four Wheeler Services - RoadSide Companion</title>

    <!-- CRITICAL INLINE STYLES - PREVENT WHITE FLICKER -->
    <style>
        /* Force immediate dark background - eliminates white flash completely */
        html, body {
            margin: 0;
            padding: 0;
            background-color: #0a0a0f !important;
            min-height: 100vh;
        }
        
        body {
            background: #0a0a0f !important;
            background-attachment: fixed;
            color: white;
            opacity: 1;
            visibility: visible;
        }
        
        /* Hide content briefly but maintain dark background - prevents FOUC */
        .page-container {
            opacity: 0;
            animation: fadeInContent 0.25s ease-out forwards;
        }
        
        @keyframes fadeInContent {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Ensure all cards have opaque background immediately */
        .service-card, .service-detail-card, .feature-box, .testimonial-card {
            background: rgba(20, 20, 30, 0.95) !important;
        }
    </style>

    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        /* Your existing CSS styles remain the same */
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
            --card-bg:   rgba(20, 20, 30, 0.95);
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
            pointer-events: none;
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
            margin-top: 0px;
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
        .breadcrumb-item { color: rgba(255,255,255,0.7); }
        .breadcrumb-item a { color: var(--primary); text-decoration: none; }
        .breadcrumb-item.active { color: white; }
        .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); }

        section { padding: 40px 0; position: relative; }

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
        .section-subtitle { color: rgba(255,255,255,0.8); margin-bottom: 30px; }

        .service-card {
            background: rgba(20, 20, 30, 0.95);
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
            background: #1a1a2a;
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
            top: 15px; right: 15px;
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
        .service-card-footer { display: flex; justify-content: space-between; align-items: center; }
        .service-link {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .service-link i { transition: transform 0.3s; }
        .service-card:hover .service-link i { transform: translateX(5px); }
        .service-rating { color: rgba(255,255,255,0.8); font-size: 0.85rem; }
        .service-rating i { margin-right: 3px; }

        .service-detail-card {
            background: rgba(20, 20, 30, 0.95);
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
            width: 80px; height: 80px;
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
            background: rgba(20,20,30,0.95);
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
            background: rgba(20,20,30,0.95);
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
            width: 50px; height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid var(--primary);
            object-fit: cover;
        }
        .author-info h5 { color: white; font-weight: 600; margin-bottom: 5px; font-size: 1rem; }
        .author-info p { color: rgba(255,255,255,0.7); font-size: 0.8rem; margin-bottom: 0; }

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

        /* Prevent any white flash from images loading */
        img {
            background-color: #1a1a2a;
            color: #1a1a2a;
        }

        @media (max-width: 768px) {
            .page-header h1 { font-size: 1.8rem; }
            .section-title { font-size: 1.6rem; }
            .service-detail-card { padding: 25px; }
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
                <h1 data-aos="fade-down" data-aos-duration="600">Four Wheeler Services</h1>
                <p data-aos="fade-down" data-aos-delay="100" data-aos-duration="600">Complete care for your car - from emergency repairs to routine maintenance</p>
                <nav aria-label="breadcrumb" data-aos="fade-down" data-aos-delay="200" data-aos-duration="600">
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
                <div class="service-detail-card" data-aos="fade-up" data-aos-duration="700">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <img src="pic/car.jpeg" alt="Four Wheeler Service" class="service-main-image" loading="eager">
                        </div>
                        <div class="col-lg-6">
                            <div class="service-icon-large"><i class="fas fa-car"></i></div>
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

        <!-- ALL FOUR WHEELER SERVICES (Dynamic from Database) -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up" data-aos-duration="600">
                    <h2 class="section-title">All Four Wheeler Services</h2>
                    <p class="section-subtitle">Comprehensive list of services we offer for your car</p>
                </div>
                
                <?php if (empty($services)): ?>
                    <div class="alert alert-info text-center">No services available at the moment. Please check back later.</div>
                <?php else: ?>
                    <?php foreach ($services_chunks as $chunk): ?>
                        <div class="row g-4 mb-4">
                            <?php foreach ($chunk as $index => $service): ?>
                                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($index * 50); ?>" data-aos-duration="600">
                                    <a href="book_service.php?service=<?php echo urlencode($service['service_name']); ?>&price=<?php echo $service['offer_price']; ?>" style="text-decoration: none; display: block; height: 100%;">
                                        <div class="service-card clickable-card">
                                            <div class="service-card-image">
                                                <img src="<?php echo htmlspecialchars($service['image_path'] ?: 'services_pics/four_wheeler_services_pics/default.jpg'); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>" loading="lazy">
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
                                                            <span class="original-price">₹<?php echo number_format($service['original_price']); ?></span>
                                                        <?php endif; ?>
                                                        <span class="offer-price">₹<?php echo number_format($service['offer_price']); ?></span>
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
                <div class="text-center" data-aos="fade-up" data-aos-duration="600">
                    <h2 class="section-title">Why Choose Our Car Services</h2>
                    <p class="section-subtitle">We provide the best care for your vehicle</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="100" data-aos-duration="600">
                        <div class="feature-box"><i class="fas fa-clock"></i><h4>24/7 Emergency</h4><p>Round-the-clock assistance for any car emergency, anytime, anywhere.</p></div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="200" data-aos-duration="600">
                        <div class="feature-box"><i class="fas fa-user-cog"></i><h4>Expert Mechanics</h4><p>Certified professionals with years of experience in car repair.</p></div>
                    </div>
                    <div class="col-md-4" data-aos="zoom-in" data-aos-delay="300" data-aos-duration="600">
                        <div class="feature-box"><i class="fas fa-shield-alt"></i><h4>Warranty</h4><p>6-month warranty on all repairs and genuine parts used.</p></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CUSTOMER TESTIMONIALS -->
        <section>
            <div class="container">
                <div class="text-center" data-aos="fade-up" data-aos-duration="600">
                    <h2 class="section-title">What Car Owners Say</h2>
                    <p class="section-subtitle">Trusted by thousands of satisfied customers</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100" data-aos-duration="600">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testimonial-text">"My car broke down on the highway at midnight. They reached within 20 minutes and fixed the issue. Amazing service!"</p>
                            <div class="testimonial-author"><img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Rahul Mehta"><div class="author-info"><h5>Rahul Mehta</h5><p>Honda City Owner</p></div></div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                            <p class="testimonial-text">"Great experience with battery replacement service. The mechanic was professional and the price was very reasonable."</p>
                            <div class="testimonial-author"><img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Priya Singh"><div class="author-info"><h5>Priya Singh</h5><p>Hyundai i20 Owner</p></div></div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300" data-aos-duration="600">
                        <div class="testimonial-card">
                            <div class="testimonial-stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i></div>
                            <p class="testimonial-text">"Towing service was quick and hassle-free. They took my car to the nearest service center. Highly recommended!"</p>
                            <div class="testimonial-author"><img src="https://randomuser.me/api/portraits/men/52.jpg" alt="Vikram Sharma"><div class="author-info"><h5>Vikram Sharma</h5><p>Ford EcoSport Owner</p></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Admin Quick Actions (Visible only to admin users) -->
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
    <div class="admin-buttons">
        <a href="admin/manage_four_wheeler_services.php" class="btn btn-primary rounded-pill">
            <i class="fas fa-plus"></i> Add Service
        </a>
        <a href="admin/manage_four_wheeler_services.php" class="btn btn-outline-primary rounded-pill">
            <i class="fas fa-edit"></i> Manage Services
        </a>
    </div>
    <?php endif; ?>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        (function() {
            // Initialize AOS with optimized settings
            AOS.init({
                duration: 700,
                once: true,
                offset: 60,
                throttleDelay: 50,
                disable: false
            });
            
            // Ensure page container becomes visible after a very short delay
            setTimeout(function() {
                document.querySelector('.page-container').style.opacity = '1';
            }, 10);
        })();

        // Navbar shadow on scroll with null check
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.style.boxShadow = window.scrollY > 50 ? '0 2px 20px rgba(0,0,0,0.8)' : '0 2px 15px rgba(0,0,0,0.5)';
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
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