<?php
session_start();
require_once '../dbcon.php';
include_once 'navbar.php';

if($_SERVER['REQUEST_METHOD']=="POST"){
    $login_id=$_POST['login_id'];
    $password=$_POST['password'];

    $qry="SELECT * FROM users_details WHERE (email=? OR phone_number=?) AND password=?";
    $stmt=$conn->prepare($qry);
    $stmt->bind_param("sss",$login_id,$login_id,$password);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows==1){
        $data=$result->fetch_assoc();
        $_SESSION['name']=$data['name'];
        $_SESSION['id']=$data['id'];
        header("location:home.php");
        exit();
    }else {
        $_SESSION['login_error'] = "Incorrect Login_id or Password !";
        header("location:login.php");
    }
}
?>
<html>
   <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RoadSide Companion</title>
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
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, rgb(255, 255, 255) 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
        }

        /* Navbar spacing fix */
        .navbar {
            margin-bottom: 30px !important;
            position: relative !important;
            z-index: 1000;
            width: 100%;
        }

        /* Add padding to account for fixed navbar if it's fixed */
        .navbar.fixed-top {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
        }
        
        /* If navbar is fixed, add padding to body */
        body.has-fixed-navbar {
            padding-top: 80px; /* Adjust based on your navbar height */
        }

        .login-wrapper {
            max-width: 1100px;
            width: 100%;
            margin: 20px auto 40px; /* Reduced top margin to account for navbar */
            padding: 0 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        /* Left Side - Image Container (45%) */
        .login-left {
            flex: 0 0 45%;
            background: linear-gradient(135deg, var(--primary), var(--dark-blue));
            min-width: 300px;
            position: relative;
            overflow: hidden;
        }

        .login-left-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.4;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            background: linear-gradient(135deg, rgba(11,79,108,0.9), rgba(10,36,114,0.9));
        }

        .login-left h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .login-left p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .testimonial {
            background: rgba(255,255,255,0.1);
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
            backdrop-filter: blur(5px);
            border-left: 4px solid var(--warning);
        }

        .testimonial-text {
            font-style: italic;
            font-size: 1rem;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .author-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--warning);
        }

        .author-info h5 {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .author-info p {
            font-size: 0.85rem;
            margin: 0;
            opacity: 0.8;
        }

        .rating-badge {
            margin-top: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255,255,255,0.1);
            padding: 15px 20px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
        }

        .stars {
            color: var(--warning);
            font-size: 1.2rem;
        }

        /* Quick Stats */
        .quick-stats {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-top: 30px;
        }

        .stat-item {
            text-align: center;
            flex: 1;
            background: rgba(255,255,255,0.1);
            padding: 15px 10px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
        }

        .stat-item h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .stat-item p {
            font-size: 0.8rem;
            margin: 0;
            opacity: 0.9;
        }

        /* Right Side - Login Form (55%) */
        .login-right {
            flex: 0 0 55%;
            padding: 70px 50px;
            background: white;
            min-width: 350px;
        }

        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo i {
            font-size: 3rem;
            color: var(--primary);
            background: #F0F7FF;
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .brand-logo h3 {
            color: var(--primary);
            font-weight: 800;
            font-size: 1.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-header h2 {
            color: var(--primary);
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .login-header p {
            color: var(--gray-corporate);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            color: var(--primary);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .input-group {
            border: 2px solid var(--border-corporate);
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s;
        }

        .input-group:focus-within {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(27,152,245,0.1);
        }

        .input-group-text {
            background: #F0F7FF;
            border: none;
            color: var(--primary);
            padding: 15px 20px;
        }

        .form-control {
            border: none;
            padding: 15px 20px;
            font-size: 1rem;
        }

        .form-control:focus {
            box-shadow: none;
            outline: none;
        }

        /* Login Options Tabs */
        .login-options {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            background: #F0F7FF;
            padding: 5px;
            border-radius: 10px;
        }

        .login-option {
            flex: 1;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            color: var(--gray-corporate);
            transition: 0.3s;
        }

        .login-option.active {
            background: var(--primary);
            color: white;
        }

        .login-option i {
            margin-right: 8px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .remember-me input {
            accent-color: var(--primary);
            width: 18px;
            height: 18px;
        }

        .remember-me label {
            color: var(--gray-corporate);
            font-size: 0.95rem;
        }

        .forgot-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            background: var(--primary);
            color: white;
            border: none;
            padding: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 10px;
            width: 100%;
            transition: 0.3s;
            margin-bottom: 20px;
            font-size: 1rem;
        }

        .btn-login:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(11,79,108,0.3);
        }

        .social-login {
            text-align: center;
            margin-top: 25px;
        }

        .social-login p {
            color: var(--gray-corporate);
            margin-bottom: 15px;
            position: relative;
            font-size: 0.9rem;
        }

        .social-login p::before,
        .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: var(--border-corporate);
        }

        .social-login p::before {
            left: 0;
        }

        .social-login p::after {
            right: 0;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icon {
            width: 50px;
            height: 50px;
            border: 2px solid var(--border-corporate);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.3rem;
            transition: 0.3s;
        }

        .social-icon:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .register-link {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--border-corporate);
        }

        .register-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Footer Styling */
        .main-footer {
            background: var(--primary);
            color: white;
            padding: 50px 0 20px;
            margin-top: 50px;
            width: 100%;
        }

        .footer-content {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .footer-title {
            color: var(--warning);
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-links a:hover {
            color: var(--warning);
            padding-left: 5px;
        }

        .contact-info {
            list-style: none;
            padding: 0;
        }

        .contact-info li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
            transition: 0.3s;
        }

        .social-links a:hover {
            background: var(--warning);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .error{
            color:red;
            display: block;
        }

        @media (max-width: 992px) {
            .login-left {
                flex: 0 0 100%;
            }
            
            .login-right {
                flex: 0 0 100%;
                padding: 50px 30px;
            }
            
            .navbar {
                margin-bottom: 20px !important;
            }
            
            body.has-fixed-navbar {
                padding-top: 70px;
            }
        }

        @media (max-width: 576px) {
            .login-right {
                padding: 40px 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .quick-stats {
                flex-direction: column;
                gap: 10px;
            }
            
            .navbar {
                margin-bottom: 15px !important;
            }
            
            body.has-fixed-navbar {
                padding-top: 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar is already included at the top via include_once 'navbar.php' -->
    
    <div class="login-wrapper">
        <div class="login-card" data-aos="fade-up">
            <!-- Left Side - Image with Testimonial (45%) -->
            <div class="login-left">
                <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80" 
                    alt="Happy Customer" class="login-left-image">
                <div class="login-left-content">
                    <h2>Welcome Back!</h2>
                    <p>Access your account to continue enjoying our premium roadside assistance services</p>
                    
                    <div class="testimonial">
                        <p class="testimonial-text">
                            "RoadSide Companion has been a lifesaver! Their quick response and professional service got me back on the road in minutes. Highly recommended!"
                        </p>
                        <div class="testimonial-author">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Rajesh Kumar" class="author-img">
                            <div class="author-info">
                                <h5>Rajesh Kumar</h5>
                                <p>Customer since 2024</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rating-badge">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <strong>4.8/5 Rating</strong>
                            <p style="margin: 0; font-size: 0.9rem; opacity: 0.8;">From 10,000+ reviews</p>
                        </div>
                    </div>

                    <div class="quick-stats">
                        <div class="stat-item">
                            <h3>24/7</h3>
                            <p>Support</p>
                        </div>
                        <div class="stat-item">
                            <h3>15min</h3>
                            <p>Response</p>
                        </div>
                        <div class="stat-item">
                            <h3>500+</h3>
                            <p>Experts</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form (55%) -->
            <div class="login-right">
                <div class="brand-logo">
                    <i class="fas fa-tools"></i>
                    <h3>RoadSide Companion</h3>
                </div>

                <div class="login-header">
                    <h2>Login to Account</h2>
                    <p>Enter your credentials to access your account</p>
                </div>
                
                <!-- Login Options Tabs -->
                <div class="login-options">
                    <div class="login-option active" onclick="switchLoginOption('email')" id="emailOption">
                        <i class="fas fa-envelope"></i> Email
                    </div>
                    <div class="login-option" onclick="switchLoginOption('phone')" id="phoneOption">
                        <i class="fas fa-phone"></i> Phone
                    </div>
                </div>
                
                <form action="login.php" method="POST" id="regForm" onsubmit="return validate(event)">
                    <?php
                        if(isset($_SESSION['login_error'])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['login_error'] . '</div>';
                            unset($_SESSION['login_error']);
                        }
                    ?>
                    <!-- Email Field (shown by default) -->
                    <div id="emailField" class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" placeholder="Enter your email address" name="login_id">
                        </div>
                        <label class="error" id="login_idError"></label>
                    </div>
                    
                    <!-- Phone Field (hidden by default) -->
                    <div id="phoneField" class="form-group" style="display: none;">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" class="form-control" placeholder="Enter your phone number" name="login_id">
                        </div>
                        <label class="error" id="login_idError"></label>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" placeholder="Enter your password" id="passwordField" name="password">
                            <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </span>
                        </div>
                        <label class="error" id="passwordError"></label>
                    </div>
                    
                    <div class="remember-forgot">
                        <div class="remember-me">
                            <input type="checkbox" id="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                    
                    <div class="social-login">
                        <p>Or continue with</p>
                        <div class="social-icons">
                            <a href="#" class="social-icon"><i class="fab fa-google"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-apple"></i></a>
                        </div>
                    </div>
                    
                    <div class="register-link">
                        Don't have an account? <a href="register.php">Create Account</a>
                    </div>
                </form>

                <div style="text-align: center; margin-top: 25px;">
                    <a href="#" class="text-muted" style="font-size: 0.85rem; text-decoration: none;">Need help? Contact Support</a>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000 });
        
        // Check if navbar is fixed and add padding to body
        document.addEventListener('DOMContentLoaded', function() {
            var navbar = document.querySelector('.navbar');
            if (navbar) {
                if (window.getComputedStyle(navbar).position === 'fixed') {
                    document.body.classList.add('has-fixed-navbar');
                }
            }
        });
        
        function togglePassword() {
            var passwordField = document.getElementById("passwordField");
            var toggleIcon = document.getElementById("toggleIcon");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.remove("fa-eye");
                toggleIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.remove("fa-eye-slash");
                toggleIcon.classList.add("fa-eye");
            }
        }

        function switchLoginOption(option) {
            var emailField = document.getElementById("emailField");
            var phoneField = document.getElementById("phoneField");
            var emailOption = document.getElementById("emailOption");
            var phoneOption = document.getElementById("phoneOption");
            
            if (option === 'email') {
                emailField.style.display = "block";
                phoneField.style.display = "none";
                emailOption.classList.add("active");
                phoneOption.classList.remove("active");
            } else {
                emailField.style.display = "none";
                phoneField.style.display = "block";
                phoneOption.classList.add("active");
                emailOption.classList.remove("active");
            }
        }
        
        function validate(event) {
            event.preventDefault();
            // Add your validation logic here
            return true;
        }
    </script>
    <script src="login-validation.js"></script>
</body>
</html>

<?php
// Include the footer file (if you have a separate footer.php file)
include_once 'footer.php';
?>