<?php
session_start();
include_once 'navbar.php';
require_once '../dbcon.php';

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $account=$_POST['account'];
    $password=$_POST['password'];

    $sql="INSERT INTO users_details (name,email,phone_number,accounttype,password) VALUES (?,?,?,?,?)";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ssiss",$name,$email,$phone,$account,$password);
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>

            body{
                height:100vh;
                margin:0;
                font-family:'Poppins',sans-serif;

                background-image:url('veh2(1).jpg');
                background-size:cover;
                background-position:center;
                background-repeat:no-repeat;
                background-attachment:fixed;

                display:flex;
                justify-content:center;
                align-items:center;
                position:relative;
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

            .register-header img{
                width:80px;
            }

            .register-header h2{
                font-family:'Orbitron',sans-serif;
                margin:0;
                letter-spacing:5px;
            }


            /* Register box (smaller) */
            .register-box{
                position:relative;
                width:370px;
                padding:28px;
                border-radius:15px;
                background:rgba(0,0,0,0.65);
                backdrop-filter:blur(10px);
                color:white;
                text-align:center;
                box-shadow:0 0 30px rgba(0,0,0,0.8);
                animation:fadeIn 1.5s ease;
            }

            /* Logo smaller */
            .logo img{
                width:90px;
                margin-bottom:5px;
                animation:float 3s ease-in-out infinite;
            }

            /* Heading */
            .register-box h2{
                font-family:'Orbitron',sans-serif;
                margin-bottom:15px;
                letter-spacing:2px;
            }

            /* Input fields */
            .form-control{
                background:rgba(255,255,255,0.05);
                border:1px solid rgba(255,255,255,0.2);
                color:white;
                height:38px;
            }

            .form-control:focus{
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

            /* Reduce spacing */
            .mb-3{
                margin-bottom:12px !important;
            }

            /* Button */
            .btn-blue{
                background:#0d6efd;
                border:none;
                font-weight:600;
                transition:0.3s;
                padding:8px;
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
        <div class="register-box">
            <div class="register-header">
                <img src="../image/logo.jpg.jpeg" alt="Roadside Companion Logo">
                <h2>REGISTER</h2>
            </div>
            <form method="POST" action="register.php">
                <div class="mb-3 text-start">
                    <label>Username</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="mb-3 text-start">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control">
                </div>
                <div class="mb-3 text-start">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label">Account Type</label>
                            <select class="form-select" name="account" id="account">
                                <option value="">--SELECT--</option>
                                <option value="customer">Customer</option>
                                <option value="service-provider">Service-Provider</option>
                            </select>
                            <small class="error" id="accountError"></small>
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
        <script>
            function togglePassword(){
                let pass = document.getElementById("password");
                let eye = document.getElementById("eye");

                if(pass.type === "password"){
                    pass.type="text";
                    eye.classList.remove("bi-eye-slash");
                    eye.classList.add("bi-eye");
                }
                else{
                    pass.type="password";
                    eye.classList.remove("bi-eye");
                    eye.classList.add("bi-eye-slash");
                }
            }

            function toggleConfirmPassword(){
                let pass = document.getElementById("confirmPassword");
                let eye = document.getElementById("eye2");

                if(pass.type === "password"){
                    pass.type="text";
                    eye.classList.remove("bi-eye-slash");
                    eye.classList.add("bi-eye");
                }
                else{
                    pass.type="password";
                    eye.classList.remove("bi-eye");
                    eye.classList.add("bi-eye-slash");
                }
            }

        </script>

    </body>
</html>


<?php
// include_once 'footer.php';
?>