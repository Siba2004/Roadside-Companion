<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up">
                <h5 class="footer-title">RoadSide Companion</h5>
                <p class="text-white-50 mb-4">
                    Your trusted partner for roadside assistance. Available 24/7, 365 days a year.
                </p>
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
                    <li><a href="./home.php"><i class="fas fa-chevron-right me-2"></i>Home</a></li>
                    <li><a href="#services"><i class="fas fa-chevron-right me-2"></i>Services</a></li>
                    <li><a href="#location"><i class="fas fa-chevron-right me-2"></i>Your Location</a></li>
                    <li><a href="#contact"><i class="fas fa-chevron-right me-2"></i>Contact Us</a></li>
                    <li><a href="about.php"><i class="fas fa-chevron-right me-2"></i>About Us</a></li>
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
                <div class="col-md-6 text-md-start">
                    <p class="mb-0">© 2026 RoadSide Companion. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white-50 text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-white-50 text-decoration-none me-3">Terms of Service</a>
                    <a href="#" class="text-white-50 text-decoration-none">FAQ</a>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="./bootstrap/bootstrap.bundle.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS Animation Library -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if(target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Navbar background change on scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if(window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.05)';
        }
    });
    
    // SOS Button click handler
    document.querySelector('.sos-button').addEventListener('click', function() {
        if(confirm('🚨 EMERGENCY SOS 🚨\n\nDo you need immediate roadside assistance?')) {
            alert('Emergency services have been notified! Help is on the way. Your location is being shared.');
        }
    });

    // Flipkart Style Banner Slider
const sliderTrack = document.getElementById('sliderTrack');
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const prevBtn = document.getElementById('prevSlide');
const nextBtn = document.getElementById('nextSlide');
const progressBar = document.getElementById('progressBar');

let currentIndex = 0;
const slideCount = slides.length;
let intervalTime = 3000; // 3 seconds
let slideInterval;
let progressInterval;
let progressWidth = 0;

// Function to update slider position
function updateSlider() {
    sliderTrack.style.transform = `translateX(-${currentIndex * 100}%)`;
    
    // Update dots
    dots.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentIndex);
    });
    
    // Reset progress
    progressWidth = 0;
    progressBar.style.width = '0%';
}

// Function to go to next slide
function nextSlide() {
    currentIndex = (currentIndex + 1) % slideCount;
    updateSlider();
}

// Function to go to previous slide
function prevSlide() {
    currentIndex = (currentIndex - 1 + slideCount) % slideCount;
    updateSlider();
}

// Function to start auto-slide
function startSlideShow() {
    slideInterval = setInterval(nextSlide, intervalTime);
    
    // Progress bar animation
    let startTime = Date.now();
    progressInterval = setInterval(() => {
        const elapsed = Date.now() - startTime;
        progressWidth = (elapsed / intervalTime) * 100;
        
        if (progressWidth <= 100) {
            progressBar.style.width = progressWidth + '%';
        } else {
            startTime = Date.now();
        }
    }, 50);
}

// Function to stop auto-slide
function stopSlideShow() {
    clearInterval(slideInterval);
    clearInterval(progressInterval);
}

// Event listeners for navigation
nextBtn.addEventListener('click', () => {
    stopSlideShow();
    nextSlide();
    startSlideShow();
});

prevBtn.addEventListener('click', () => {
    stopSlideShow();
    prevSlide();
    startSlideShow();
});

// Event listeners for dots
dots.forEach((dot, index) => {
    dot.addEventListener('click', () => {
        stopSlideShow();
        currentIndex = index;
        updateSlider();
        startSlideShow();
    });
});

// Pause on hover
sliderTrack.addEventListener('mouseenter', stopSlideShow);
sliderTrack.addEventListener('mouseleave', startSlideShow);

// Touch support for mobile
let touchStartX = 0;
let touchEndX = 0;

sliderTrack.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
    stopSlideShow();
});

sliderTrack.addEventListener('touchend', (e) => {
    touchEndX = e.changedTouches[0].clientX;
    const diff = touchStartX - touchEndX;
    
    if (Math.abs(diff) > 50) {
        if (diff > 0) {
            nextSlide();
        } else {
            prevSlide();
        }
    }
    
    startSlideShow();
});

// Initialize slider
updateSlider();
startSlideShow();
</script>
</body>
</html>