<?php
include_once 'navbar.php';
require_once '../dbcon.php';
?>

<div class="banner-slider-container">
    <div class="container-fluid px-4">
        <div class="banner-slider">
            <div class="slider-track" id="sliderTrack">
                <!-- Slide 1: Emergency Service Offer -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Emergency Service">
                    <div class="slide-content">
                        <div class="slide-text">
                            <span class="offer-badge">🎉 FLAT 30% OFF</span>
                            <h2>Emergency<br>Roadside Service</h2>
                            <p>24/7 assistance at your fingertips. First service at 30% off!</p>
                            <button class="btn-offer">Book Now <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                        <div class="slide-image">
                            <i class="fas fa-ambulance"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 2: Battery Service Offer -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1625047509168-a7026f36de04?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Battery Service">
                    <div class="slide-content">
                        <div class="slide-text">
                            <span class="offer-badge">⚡ 20% OFF</span>
                            <h2>Battery<br>Boosting Service</h2>
                            <p>Jump start or replace battery. Get 20% off on battery service</p>
                            <button class="btn-offer">Avail Offer <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                        <div class="slide-image">
                            <i class="fas fa-car-battery"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 3: Fuel Delivery Offer -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1591886960571-74d43a9d4166?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Fuel Delivery">
                    <div class="slide-content">
                        <div class="slide-text">
                            <span class="offer-badge">⛽ FREE DELIVERY</span>
                            <h2>Emergency<br>Fuel Delivery</h2>
                            <p>Running out of fuel? Get free delivery on first order</p>
                            <button class="btn-offer">Order Now <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                        <div class="slide-image">
                            <i class="fas fa-gas-pump"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 4: Flat Tyre Offer -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1580273916550-e323be2ae537?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Flat Tyre">
                    <div class="slide-content">
                        <div class="slide-text">
                            <span class="offer-badge">🛞 FLAT 25% OFF</span>
                            <h2>Flat Tyre<br>Assistance</h2>
                            <p>Quick tyre change or repair. Special discount for members</p>
                            <button class="btn-offer">Get Help <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                        <div class="slide-image">
                            <i class="fas fa-tire"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Slide 5: Towing Service Offer -->
                <div class="slide">
                    <img src="https://images.unsplash.com/photo-1603745713042-00b57d8d9e2a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" alt="Towing">
                    <div class="slide-content">
                        <div class="slide-text">
                            <span class="offer-badge">🚛 SAVE 40%</span>
                            <h2>Towing<br>Services</h2>
                            <p>Professional towing at best prices. Limited period offer</p>
                            <button class="btn-offer">Call Now <i class="fas fa-arrow-right ms-2"></i></button>
                        </div>
                        <div class="slide-image">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Dots -->
            <div class="slider-dots" id="sliderDots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
            
            <!-- Navigation Arrows -->
            <div class="slider-arrow left" id="prevSlide">
                <i class="fas fa-chevron-left"></i>
            </div>
            <div class="slider-arrow right" id="nextSlide">
                <i class="fas fa-chevron-right"></i>
            </div>
            
            <!-- Progress Bar -->
            <div class="slider-progress">
                <div class="progress-bar" id="progressBar"></div>
            </div>
        </div>
    </div>
</div>

<!-- Four Wheelers Services -->
<section id="services" class="service-section">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Comprehensive roadside assistance for all vehicle types</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3 class="service-title">Four Wheelers Services</h3>
                    <ul class="service-list row">
                        <div class="col-md-6">
                            <li><i class="fas fa-check-circle"></i> Emergency Roadside Repairs</li>
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
            </div>
            
            <div class="col-lg-6" data-aos="fade-left">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <h3 class="service-title">Two Wheelers Services</h3>
                    <ul class="service-list row">
                        <div class="col-md-6">
                            <li><i class="fas fa-check-circle"></i> Emergency Roadside Repairs</li>
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
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-section">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">We provide the best roadside assistance experience</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="feature-title">Faster Service</h4>
                    <p class="feature-text">Average response time under 20 minutes with real-time tracking</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h4 class="feature-title">Quality Services</h4>
                    <p class="feature-text">Certified mechanics and verified service providers</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-car-battery"></i>
                    </div>
                    <h4 class="feature-title">Genuine Parts</h4>
                    <p class="feature-text">100% authentic spare parts with warranty</p>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h4 class="feature-title">Affordable Prices</h4>
                    <p class="feature-text">Transparent pricing with no hidden charges</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews & Feedback Section -->
<section class="reviews-section">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2 class="section-title">What Our Customers Say</h2>
            <p class="section-subtitle">Trusted by thousands of happy customers</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="review-card">
                    <div class="text-center">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Reviewer" class="reviewer-img">
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="review-text">
                            "Excellent service! My car broke down on the highway, and they reached within 15 minutes. 
                            Fixed the issue quickly and charged a fair price."
                        </p>
                        <h5 class="reviewer-name">Rajesh Kumar</h5>
                        <p class="reviewer-role">Car Owner</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="review-card">
                    <div class="text-center">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Reviewer" class="reviewer-img">
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="review-text">
                            "The battery boosting service for my bike was super quick. The mechanic was professional 
                            and even checked other parts. Highly recommended!"
                        </p>
                        <h5 class="reviewer-name">Priya Sharma</h5>
                        <p class="reviewer-role">Bike Rider</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="review-card">
                    <div class="text-center">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Reviewer" class="reviewer-img">
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="review-text">
                            "Fuel delivery service saved me during a late-night emergency. Easy to use app and 
                            transparent pricing. Will definitely use again."
                        </p>
                        <h5 class="reviewer-name">Amit Patel</h5>
                        <p class="reviewer-role">SUV Owner</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="#" class="btn btn-outline-primary btn-lg">
                View All Reviews <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="why-choose-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h2 class="section-title">About RoadSide Companion</h2>
                <p class="lead mb-4">
                    We're on a mission to make roadside assistance accessible, reliable, and affordable for everyone.
                </p>
                <p class="text-muted mb-4">
                    Founded in 2024, RoadSide Companion has grown to become one of the most trusted vehicle breakdown 
                    assistance platforms. We connect stranded motorists with professional mechanics and service providers 
                    within minutes, ensuring you're never alone on the road.
                </p>
                <div class="row g-4 mt-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-users fa-2x text-primary me-3"></i>
                            <div>
                                <h3 class="h2 mb-0 fw-bold">10K+</h3>
                                <p class="text-muted mb-0">Happy Customers</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tools fa-2x text-primary me-3"></i>
                            <div>
                                <h3 class="h2 mb-0 fw-bold">500+</h3>
                                <p class="text-muted mb-0">Service Providers</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1530046339160-ce3e530c7d2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" 
                         alt="About Us" class="img-fluid rounded-4 shadow-lg">
                    <div class="position-absolute bottom-0 end-0 bg-white p-4 rounded-4 shadow m-4">
                        <div class="d-flex align-items-center">
                            <div class="sos-button-small me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #FF6B6B, #FF8E53); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">24/7 Emergency</p>
                                <h5 class="mb-0 fw-bold">1800-123-4567</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SOS Emergency Button -->
<div class="sos-button">
    <i class="fas fa-exclamation-triangle"></i>
    <span>SOS</span>
</div>


<?php
include_once 'footer.php';
?>