<?php
session_start();
require_once '../dbcon.php';


if($_SERVER['REQUEST_METHOD']=="POST"){
    $type=$_POST['usertype'];
    $login_id=$_POST['login_id'];
    $password=$_POST['password'];
    $type=$_POST['account'];
    $qry="SELECT * FROM users_details WHERE (email=? OR phone_number=?) AND password=? AND accounttype=?";
    $stmt=$conn->prepare($qry);
    $stmt->bind_param("ssss",$login_id,$login_id,$password,$type);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows==1){
        $data=$result->fetch_assoc();
        $_SESSION['name']=$data['name'];
        $_SESSION['id']=$data['id'];
        $_SESSION['type']=$data['accounttype'];
        $stmt->close();
        $conn->close();
        if($data['accounttype']=='customer'){
            header("location: home.php");
        }elseif($data['accounttype']=='service-provider'){
            header("location: ../ServiceProvider/service_home.php");
        }elseif($data['type']=='administrator'){
            header("location: ../Admin/admin_home.php");
        }exit();
    }else{
        $_SESSION['login_error'] = "Incorrect Login_id or Password !";
        header("location:login.php");
    }
}
?>
<?php include_once 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roadside Companion Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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

        body{
            height:100vh;
            margin:0;
            font-family:'Poppins',sans-serif;
            background:url('vehiclebg.png') no-repeat center center/cover;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        /* Dark overlay */
            body::before{
            content:"";
            position:absolute;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.65);
        }

        /* Login box */
        .login-box{
            position:relative;
            width:360px;
            padding:28px;
            border-radius:15px;
            background:rgba(0,0,0,0.65);
            backdrop-filter:blur(10px);
            color:white;
            text-align:center;
            box-shadow:0 0 30px rgba(0,0,0,0.8);
            animation:fadeIn 1.5s ease;
        }

        /* Logo */
        .logo img{
            width:90px;
            margin-bottom:5px;
            animation:float 3s ease-in-out infinite;
        }

        /* Heading */
        .login-box h2{
            font-family:'Orbitron',sans-serif;
            margin-bottom:15px;
            letter-spacing:2px;
        }

        /* Inputs */
        .form-control{
            background:rgba(255,255,255,0.05);
            border:1px solid rgba(255,255,255,0.2);
            color:white;
            height:38px;
        }

        /* Dropdown */
        .form-select{
            background-color:rgba(255,255,255,0.05);
            border:1px solid rgba(255,255,255,0.2);
            height:38px;
        }


        body{
            height:100vh;
            margin:0;
            font-family:'Poppins',sans-serif;

            background-image:url('image-32(2).jpg');
            background-size:cover;
            background-position:center;
            background-repeat:no-repeat;
            background-attachment:fixed;

            display:flex;
            justify-content:center;
            align-items:center;
        }
        /* Placeholder style */
        .form-select:invalid{
            color:#9aa0a6;
        }

        /* After selecting option */
        .form-select:valid{
            color:white;
        }

        .login-header{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:12px;
            margin-bottom:15px;
        }

        .login-header img{
            width:80px;
        }

        .login-header h2{
            font-family:'Orbitron',sans-serif;
            margin:0;
            letter-spacing:5px;
        }


        /* Dropdown options */
        .form-select option{
            background:#111;
            color:white;
        }

        /* Focus */
        .form-control:focus,
        .form-select:focus{
            background:rgba(255,255,255,0.05);
            border-color:#0d6efd;
            box-shadow:0 0 8px #0d6efd;
            color:white;
        }

        /* Password box */
        .password-box{
            position:relative;
        }

        .password-box input{
                padding-right:40px;
        }

        .password-box i{
            position:absolute;
            right:15px;
            top:70%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#ccc;
        }

        /* Spacing */
        .mb-3{
            margin-bottom:12px !important;
        }

        /* Button */
        .btn-blue{
            background:#0d6efd;
            border:none;
            font-weight:600;
            padding:8px;
            transition:0.3s;
        }

        .btn-blue:hover{
            background:#0b5ed7;
            box-shadow:0 0 15px #0d6efd;
            transform:scale(1.05);
        }

        /* Links */
        .links{
            margin-top:10px;
            font-size:13px;
        }

        .links a{
            color:#0d6efd;
            text-decoration:none;
        }

        .links a:hover{
            text-decoration:underline;  
        }

        /* Animations */

        @keyframes float{
            0%{transform:translateY(0px);}
            50%{transform:translateY(-8px);}
            100%{transform:translateY(0px);}
        }

        @keyframes fadeIn{
            from{
                opacity:0;
                transform:translateY(30px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

    </style>

</head>

<body>
    <div class="login-box">
        <div class="login-header">
            <img src="logo.jpg.jpeg" alt="Roadside Companion Logo">
            <h2>LOGIN</h2>
        </div>
        <form method="POST" action="login.php" id="regForm" onsubmit="return validate(event)">
            <div class="mb-3 text-start">
                <label>User Type</label>
                <select name="usertype" class="form-select" required>
                    <option value="" selected disabled>-Select-User-Type-</option>
                    <option value="admin">Admin</option>
                    <option value="mechanic">Mechanic</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div class="mb-3 text-start">
                <label>Phone/Email</label>
                <input type="text" name="login_id" class="form-control"><br>
                <label class="error" id="login_idError"></label>

            </div>

            <div class="mb-3 text-start password-box">
                <label>Password</label>
                <input type="password" id="password" name="password" class="form-control"><br>
                <label class="error" id="login_idError"></label><br>
                <i class="bi bi-eye-slash" onclick="togglePassword()" id="eye"></i>
            </div>

            <button type="submit" name="login" class="btn btn-blue w-100">Login</button>

            <div class="links">
                <a href="#">Forgot Password?</a><br>
                Don't have an account? <a href="register.php">Sign Up</a>
            </div>
        </form>
    </div>

    <script>
        function validate(e){
            let error=false;
            let form=document.getElementById('loginForm');
            let login_id=form.elements['login_id'].value
            let password=form.elements['password'].value

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
            
            if(login_id===""){
                login_idError.innerHTML="Please enter your email or phone number"
                error=true
            }else{
                login_idError.innerHTML=""
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
            if(passErrMsg === ""){
                passwordError.innerHTML = ""
            } else {
                passwordError.innerHTML = passErrMsg
            }
            if(error){
                e.preventDefault();
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
    <?php include_once 'navbar.php'; ?>
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
                            <input type="text" class="form-control" placeholder="Enter your email address" name="login_id">
                        </div>
                        <label class="error" id="login_idError"></label>
                    </div>
                    
                    <!-- Phone Field (hidden by default) -->
                    <div id="phoneField" class="form-group" style="display: none;">
                        <label class="form-label">Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" class="form-control" placeholder="Enter your phone number" name="login_id">
                        </div>
                        <!-- <label class="error" id="login_idError"></label> -->
                    </div>

                    <div class="form-group">
                        <label class="form-label">User Type</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                            <select class="form-control" name="account" id="account">
                                <option value="">-- SELECT ACCOUNT TYPE --</option>
                                <option value="administrator">Administrator</option>
                                <option value="customer">Customer</option>
                                <option value="service-provider">Service Provider</option>
                            </select>
                        </div>
                        <small class="error" id="accountError"></small>
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
            //event.preventDefault();
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
