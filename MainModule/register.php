<?php
session_start();
include_once 'navbar.php';
require_once '../dbcon.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=$_POST['fullname'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $accounttype=$_POST['account'];
    $password=$_POST['password'];

    $sql="INSERT INTO users_details (name,email,phone_number,accounttype,password) VALUES (?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssiss",$name,$email,$phone,$accounttype,$password);
    if($stmt->execute()){
        echo "<script>
        alert('Registration successful! Please login.');
        window.location='./login.php';</script>";
    }else{
        echo"<script>alert('Registration failed!');
        window.location='./register.php';</script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - RoadSide Companion</title>
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
            background: linear-gradient(135deg, #ffffff 100%);
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

        .register-wrapper {
            max-width: 1200px;
            width: 100%;
            margin: 20px auto 40px; /* Reduced top margin to account for navbar */
            padding: 0 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .register-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .register-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary), var(--dark-blue));
            padding: 60px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 300px;
        }

        .register-left h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .register-left p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.6;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            font-size: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .feature-list li i {
            color: var(--warning);
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .register-right {
            flex: 1;
            padding: 60px 40px;
            background: white;
            min-width: 300px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .register-header h3 {
            color: var(--primary);
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .register-header p {
            color: var(--gray-corporate);
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
            border-radius: 8px;
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
            padding: 12px 15px;
        }

        .form-control {
            border: none;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: none;
            outline: none;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: var(--border-corporate);
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            background: #dc2626;
            transition: 0.3s;
        }

        .terms-check {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }

        .terms-check input {
            margin-right: 10px;
            accent-color: var(--primary);
        }

        .terms-check label {
            color: var(--gray-corporate);
            font-size: 0.9rem;
        }

        .terms-check a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }

        .btn-register {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 8px;
            width: 100%;
            transition: 0.3s;
            margin-bottom: 20px;
        }

        .btn-register:hover {
            background: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(11,79,108,0.3);
        }

        .social-register {
            text-align: center;
            margin-top: 20px;
        }

        .social-register p {
            color: var(--gray-corporate);
            margin-bottom: 15px;
            position: relative;
        }

        .social-register p::before,
        .social-register p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: var(--border-corporate);
        }

        .social-register p::before {
            left: 0;
        }

        .social-register p::after {
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
            border-radius: 10px;
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

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--border-corporate);
        }

        .login-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
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
            max-width: 1200px;
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
            flex-wrap: wrap;
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

        .error {
            color: red;
            display: block;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        @media (max-width: 992px) {
            .register-left {
                flex: 0 0 100%;
            }
            
            .register-right {
                flex: 0 0 100%;
                padding: 40px 30px;
            }
            
            .navbar {
                margin-bottom: 20px !important;
            }
            
            body.has-fixed-navbar {
                padding-top: 70px;
            }
        }

        @media (max-width: 768px) {
            .register-card {
                flex-direction: column;
            }

            /* Dark overlay */
            body::before{
                content:"";
                position:absolute;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background:rgba(0,0,0,0.65);
                z-index:0;
            }

            .register-header{
                display:flex;
                align-items:center;
                justify-content:center;
                gap:12px;
                margin-bottom:15px;
            }
            
            .social-links {
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .register-right {
                padding: 30px 20px;
            }
            
            .register-left h2 {
                font-size: 2rem;
            }
            
            .navbar {
                margin-bottom: 15px !important;
            }
            
            body.has-fixed-navbar {
                padding-top: 60px;
            }
            
            .footer-title {
                text-align: center;
            }
            
            .footer-links, .contact-info {
                text-align: center;
            }
            
            .contact-info li {
                justify-content: center;
            }
            
            .social-links {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar is already included at the top via include_once 'navbar.php' -->
    
    <div class="register-wrapper">
        <div class="register-card" data-aos="fade-up">
            <!-- Left Side - Benefits -->
            <div class="register-left">
                <h2>Join RoadSide Companion</h2>
                <p>Get instant access to India's most trusted roadside assistance network</p>
                
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> 24/7 Emergency Assistance</li>
                    <li><i class="fas fa-check-circle"></i> 500+ Certified Mechanics</li>
                    <li><i class="fas fa-check-circle"></i> Real-time Service Tracking</li>
                    <li><i class="fas fa-check-circle"></i> Exclusive Member Discounts</li>
                    <li><i class="fas fa-check-circle"></i> 100% Genuine Parts Warranty</li>
                    <li><i class="fas fa-check-circle"></i> Zero Hidden Charges</li>
                </ul>
                
                <div style="margin-top: 40px;">
                    <p style="font-size: 0.9rem; opacity: 0.8;">Trusted by over 10,000+ happy customers</p>
                    <div style="display: flex; gap: 5px;">
                        <i class="fas fa-star" style="color: #FFA500;"></i>
                        <i class="fas fa-star" style="color: #FFA500;"></i>
                        <i class="fas fa-star" style="color: #FFA500;"></i>
                        <i class="fas fa-star" style="color: #FFA500;"></i>
                        <i class="fas fa-star" style="color: #FFA500;"></i>
                        <span style="margin-left: 10px;">4.8/5 Rating</span>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Registration Form -->
            <div class="register-right">
                <div class="register-header">
                    <h3>Create Account</h3>
                    <p>Fill in your details to get started</p>
                </div>
                
                <?php 
                if(isset($_SESSION['msg'])) {
                    echo '<div class="alert alert-info" role="alert">' . $_SESSION['msg'] . '</div>';
                    unset($_SESSION['msg']);
                }
                ?>
                
                <form action="register.php" method="POST" id="regForm" onsubmit="return validateForm(event)">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" placeholder="Enter your full name" name="fullname" id="fullname">
                        </div>
                        <small class="error" id="nameError"></small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" placeholder="Enter your email" name="email" id="email">
                        </div>
                        <small class="error" id="emailError"></small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" class="form-control" placeholder="Enter your phone number" name="phone" id="phone">
                        </div>
                        <small class="error" id="phoneError"></small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" placeholder="Create a password" name="password" id="password" onkeyup="checkPasswordStrength()">
                        </div>
                        <div class="password-strength">
                            <div class="strength-bar" id="strengthBar"></div>
                        </div>
                        <small class="error" id="passError"></small>
                        <small style="color: var(--gray-corporate);" id="passwordStrengthText">Password strength: </small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" placeholder="Confirm your password" name="cpassword" id="cpassword">
                        </div>
                        <small class="error" id="cpassError"></small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">User Type</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <select class="form-control" name="account" id="account">
                                <option value="">-- SELECT ACCOUNT TYPE --</option>
                                <!-- <option value="administrator">Administrator</option> -->
                                <option value="customer">Customer</option>
                                <option value="service-provider">Service Provider</option>
                            </select>
                        </div>
                        <small class="error" id="accountError"></small>
                    </div>
                    
                    <div class="terms-check">
                        <input type="checkbox" id="terms" name="terms">
                        <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                    </div>
                    <small class="error" id="termsError"></small>
                    
                    <button type="submit" class="btn-register">
                        <i class="fas fa-user-plus me-2"></i>Register Now
                    </button>
                    
                    <div class="social-register">
                        <p>Or register with</p>
                        <div class="social-icons">
                            <a href="#" class="social-icon"><i class="fab fa-google"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    
                    <div class="login-link">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </form>
            </div>
            <form method="POST">
                <div class="mb-3 text-start">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3 text-start">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3 text-start password-box">
                    <label>Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <i class="bi bi-eye-slash" onclick="togglePassword()" id="eye"></i>
                </div>
                <div class="mb-3 text-start password-box">
                    <label>Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                    <i class="bi bi-eye-slash" onclick="toggleConfirmPassword()" id="eye2"></i>
                </div>
                <button type="submit" name="register" class="btn btn-blue w-100">Register</button>
                <div class="links">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
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

        // Password strength checker
        function checkPasswordStrength() {
            var password = document.getElementById('password').value;
            var strengthBar = document.getElementById('strengthBar');
            var strengthText = document.getElementById('passwordStrengthText');
            
            var strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]+/)) strength += 25;
            if (password.match(/[A-Z]+/)) strength += 25;
            if (password.match(/[0-9]+/)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if (strength <= 25) {
                strengthBar.style.background = '#dc2626';
                strengthText.innerHTML = 'Password strength: Weak';
            } else if (strength <= 50) {
                strengthBar.style.background = '#f59e0b';
                strengthText.innerHTML = 'Password strength: Medium';
            } else if (strength <= 75) {
                strengthBar.style.background = '#10b981';
                strengthText.innerHTML = 'Password strength: Good';
            } else {
                strengthBar.style.background = '#059669';
                strengthText.innerHTML = 'Password strength: Strong';
            }
        }

        // Form validation
        function validateForm(event) {
            event.preventDefault();
            
            var isValid = true;
            
            // Reset errors
            document.querySelectorAll('.error').forEach(function(el) {
                el.innerHTML = '';
            });
            
            // Validate fullname
            var fullname = document.getElementById('fullname').value.trim();
            if (fullname === '') {
                document.getElementById('nameError').innerHTML = 'Full name is required';
                isValid = false;
            } else if (fullname.length < 3) {
                document.getElementById('nameError').innerHTML = 'Name must be at least 3 characters';
                isValid = false;
            }
            
            // Validate email
            var email = document.getElementById('email').value.trim();
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                document.getElementById('emailError').innerHTML = 'Email is required';
                isValid = false;
            } else if (!emailPattern.test(email)) {
                document.getElementById('emailError').innerHTML = 'Please enter a valid email';
                isValid = false;
            }
            
            // Validate phone
            var phone = document.getElementById('phone').value.trim();
            var phonePattern = /^[0-9]{10}$/;
            if (phone === '') {
                document.getElementById('phoneError').innerHTML = 'Phone number is required';
                isValid = false;
            }else if (phone.length < 10) {
                document.getElementById('phoneError').innerHTML = 'Phone number must be 10 digits';
                isValid = false;
            }else if (!phonePattern.test(phone)) {
                document.getElementById('phoneError').innerHTML = 'Please enter a valid 10-digit phone number';
                isValid = false;
            }
            
            // Validate password
            var password = document.getElementById('password').value;
            if (password === '') {
                document.getElementById('passError').innerHTML = 'Password is required';
                isValid = false;
            } else if (password.length < 8) {
                document.getElementById('passError').innerHTML = 'Password must be at least 8 characters';
                isValid = false;
            }
            
            // Validate confirm password
            var cpassword = document.getElementById('cpassword').value;
            if (cpassword === '') {
                document.getElementById('cpassError').innerHTML = 'Please confirm your password';
                isValid = false;
            } else if (password !== cpassword) {
                document.getElementById('cpassError').innerHTML = 'Passwords do not match';
                isValid = false;
            }
            
            // Validate account type
            var account = document.getElementById('account').value;
            if (account === '') {
                document.getElementById('accountError').innerHTML = 'Please select an account type';
                isValid = false;
            }
            
            // Validate terms
            var terms = document.getElementById('terms').checked;
            if (!terms) {
                document.getElementById('termsError').innerHTML = 'You must agree to the terms and conditions';
                isValid = false;
            }
            
            if (isValid) {
                document.getElementById('regForm').submit();
            }
            
            return false;
        }
    </script>
    <!-- <script src="./jsfiles/register-validator.js"></script> -->
</body>
</html>


<?php
// Include the footer file
include_once 'footer.php';
?>