<?php
session_start();
include_once 'navbar.php';
require_once '../dbcon.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=trim($_POST['name']);
    $email=trim($_POST['email']);
    $phone=trim($_POST['phone']);
    $account=trim($_POST['account']);
    $password=trim($_POST['password']);
    $cpassword=trim($_POST['confirmPassword']);

    if($name=="" || $email=="" || $phone=="" || $account=="" || $password=="" || $cpassword==""){
        echo "<script>
                alert('All fields are required');
                window.location='register.php';
                </script>";
        exit();
    }

    if($password != $cpassword){
        echo "<script>
                alert('Passwords do not match');
                window.location='register.php';
                </script>";
        exit();
    }

    $data="SELECT * FROM users_details WHERE email=? OR phone_number=?";
    $stmt=$conn->prepare($data);
    $stmt->bind_param("ss",$email,$phone);
    $stmt->execute();
    $result=$stmt->get_result();
    if($result->num_rows > 0){
        echo "<script>
                alert('Email or phone number already exists');
                window.location='register.php';
                </script>";
        exit();
    }

    $hashedPassword=password_hash($password,PASSWORD_DEFAULT);


    $sql="INSERT INTO users_details (name,email,phone_number,accounttype,password) VALUES (?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sssss",$name,$email,$phone,$account,$hashedPassword);
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

<html>
    <head>
        <title>Roadside Companion Register</title>
        <link href="../bootstrap.min.css" rel="stylesheet">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
        body {
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url('pic/register_bg.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            padding-top: 70px;       /* fixed navbar height */
            padding-bottom: 20px;
            box-sizing: border-box;
            overflow-x: hidden;       /* no horizontal scroll */
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.65);
            z-index: 0;
        }

        /* Register box – comfortable size, no navbar overlap */
        .register-box {
            position: relative;
            width: 360px;
            padding: 18px;
            border-radius: 15px;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(10px);
            color: white;
            text-align: center;
            box-shadow: 0 0 25px rgba(0,0,0,0.8);
            animation: fadeIn 1.5s ease;
            z-index: 1;
            margin: 20px auto 0 auto;  /* top margin clears navbar */
        }

        /* Header */
        .register-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .register-header img {
            width: 60px;
            animation: float 3s ease-in-out infinite;
        }

        .register-header h2 {
            font-family: 'Orbitron', sans-serif;
            margin: 0;
            letter-spacing: 5px;
            font-size: 1.7rem;
        }

        /* Input fields – slightly larger */
        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            height: 34px;
            font-size: 0.9rem;
            padding: 5px 10px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.05);
            border-color: #0d6efd;
            box-shadow: 0 0 8px #0d6efd;
            color: white;
        }

        .form-select option {
            background: #111;
            color: white;
        }

        /* Password wrapper (eye icon) */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 35px;
        }

        .password-wrapper i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ccc;
            z-index: 10;
            font-size: 0.9rem;
        }

        /* Spacing between fields */
        .mb-3 {
            margin-bottom: 6px !important;
        }

        /* Labels */
        label {
            font-size: 0.85rem;
            margin-bottom: 2px;
            display: block;
            text-align: left;
        }

        /* Button */
        .btn-blue {
            background: #0d6efd;
            border: none;
            font-weight: 500;
            padding: 6px;
            font-size: 0.9rem;
            transition: 0.3s;
            width: 100%;
            margin-top: 5px;
        }

        .btn-blue:hover {
            background: #0b5ed7;
            box-shadow: 0 0 15px #0d6efd;
            transform: scale(1.02);
        }

        /* Links */
        .links {
            margin-top: 6px;
            font-size: 0.8rem;
        }

        .links a {
            color: #0d6efd;
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        /* Error message */
        .error {
            font-size: 0.7rem;
            color: #ff6b6b;
            display: block;
            margin-top: 1px;
        }

        /* Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(60px); /* start lower */
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 576px) {
            body {
                padding-top: 60px;
            }
            .register-box {
                width: 90%;
                padding: 14px;
                margin-top: 10px;
            }
            .register-header img {
                width: 50px;
            }
            .register-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-box">
        <div class="register-header">
            <img src="../image/logo.jpg.jpeg" alt="Roadside Companion Logo">
            <h2>REGISTER</h2>
        </div>
        <form method="POST" action="register.php">
            <div class="mb-3 text-start">
                <label>Username</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <form method="POST" action="register.php" id="regForm" onsubmit="return validate()">
                <div class="mb-3 text-start">
                    <label>Username</label>
                    <input type="text" name="name" class="form-control">
                    <label class="error" id="usernameError"></label>
                </div>
                <div class="mb-3 text-start">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control">
                    <label class="error" id="emailError"></label>
                </div>
                <div class="mb-3 text-start">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                    <label class="error" id="phoneError"></label>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Account Type</label>
                            <select class="form-select" name="account" id="account">
                                <option value="">--SELECT--</option>
                                <option value="customer">Customer</option>
                                <option value="service-provider">Service-Provider</option>
                            </select>
                            <label class="error" id="accountError"></label>
                    </div>
                <div class="mb-3 text-start password-box">
                    <label>Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <i class="bi bi-eye-slash" onclick="togglePassword()" id="eye"></i>
                    <label class="error" id="passwordError"></label>
                </div>
                <div class="mb-3 text-start password-box">
                    <label>Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="form-control">
                    <i class="bi bi-eye-slash" onclick="toggleConfirmPassword()" id="eye2"></i>
                    <label class="error" id="confirmPasswordError"></label>
                </div>
                
                <button type="submit" name="register" class="btn btn-blue w-100">Register</button>
                <div class="links">
                    Already have an account? <a href="login.php">Login</a>
                </div>
            </form>
        </div>
        <script src="./register-validator.js"></script>

    </body>
</html>
<?php
// include_once 'footer.php';
?>