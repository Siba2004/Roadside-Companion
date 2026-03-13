<!-- ============================================================
     FOOTER
============================================================ -->
<style>
    .footer {
    background: #0b1220;
    color: white;
    padding: 60px 0 30px;
    
}

    .footer-title {
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        position: relative;
    }

    .footer-title::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 35px;
        height: 2px;
        background: var(--accent);
    }

    .footer-links { list-style: none; padding: 0; }
    .footer-links li { margin-bottom: 10px; }

    .footer-links a {
        color: rgba(255,255,255,0.75);
        text-decoration: none;
        font-size: 0.9rem;
        transition: 0.2s;
    }

    .footer-links a:hover { color: white; padding-left: 5px; }

    .social-links { display: flex; gap: 10px; }

    .social-links a {
        width: 36px;
        height: 36px;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: 0.2s;
    }

    .social-links a:hover { background: var(--accent); transform: translateY(-2px); }

    .footer-bottom {
        border-top: 1px solid rgba(255,255,255,0.1);
        margin-top: 40px;
        padding-top: 20px;
        font-size: 0.85rem;
        color: rgba(255,255,255,0.6);
    }
</style>

<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <h5 class="footer-title">RoadSide Companion</h5>
                <p class="text-white-50 mb-4">Your trusted partner for roadside assistance. Available 24/7, 365 days a year.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <h5 class="footer-title">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="#services"><i class="fas fa-chevron-right me-2"></i>Services</a></li>
                    <li><a href="#why-us"><i class="fas fa-chevron-right me-2"></i>Why Us</a></li>
                    <li><a href="#reviews"><i class="fas fa-chevron-right me-2"></i>Reviews</a></li>
                    <li><a href="#about"><i class="fas fa-chevron-right me-2"></i>About Us</a></li>
                    <li><a href="login.php"><i class="fas fa-chevron-right me-2"></i>Login</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <h5 class="footer-title">Our Services</h5>
                <ul class="footer-links">
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Emergency Repairs</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Fuel Delivery</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Towing Service</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Battery Service</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Flat Tyre</a></li>
                </ul>
            </div>

            <div class="col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <h5 class="footer-title">Contact Info</h5>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt me-2"></i> 123 Service Road, Mumbai - 400001</li>
                    <li><i class="fas fa-phone me-2"></i> +91 12345 67890</li>
                    <li><i class="fas fa-envelope me-2"></i> support@roadsidecompanion.com</li>
                    <li><i class="fas fa-clock me-2"></i> 24/7 Emergency Support</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row">
                <div class="col-md-6">© 2026 RoadSide Companion. All rights reserved.</div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 text-decoration-none me-3">Terms of Service</a>
                    <a href="#" class="text-white-50 text-decoration-none">FAQ</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS (load once here at the end) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>