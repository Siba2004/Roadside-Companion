<?php
session_start();
require_once '../dbcon.php';

if(isset($_POST['login'])){
    $login_id=trim($_POST['login_id']);
    $password=$_POST['password'];
    $type=$_POST['usertype'];

    $qry="SELECT * FROM users_details WHERE (email=? OR phone_number=?) AND accounttype=?";
    $stmt=$conn->prepare($qry);
    $stmt->bind_param("sss",$login_id,$login_id,$type);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows==1){
        $data=$result->fetch_assoc();
        if(password_verify($password,$data['password'])){
            $_SESSION['name']=$data['name'];
            $_SESSION['id']=$data['id'];
            $_SESSION['type']=$data['accounttype'];
            $stmt->close();
            $conn->close();
            if($data['accounttype']=='customer'){
                header("location: home.php");
            }elseif($data['accounttype']=='service-provider'){
                header("location: ../ServiceProvider/service_home.php");
            }elseif($data['accounttype']=='administrator'){
                header("location: ../Admin/admin_home.php");
            }exit();
        }else{
            $_SESSION['login_error'] = "Incorrect Login ID or Account Type";
            header("location:login.php");
            exit();
        }
    }else{
        $_SESSION['login_error'] = "Incorrect Login_id or Password !";
        header("location:login.php");
        exit();
    }
}
?>
<?php include_once 'navbar.php'; ?>
<html>
<head>

    <title>Roadside Companion Login</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body{
            height:100vh;
            margin:0;
            font-family:'Poppins',sans-serif;

            background-image:url('pic/login_bg.jpeg');
            background-size:cover;
            background-position:center;
            background-repeat:no-repeat;
            background-attachment:fixed;

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
            margin-top:20px;          /* space below navbar */
            z-index:1000;              /* above overlay and navbar */
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

        /* Password wrapper for eye icon */
        .password-wrapper{
            position:relative;
        }

        .password-wrapper input{
            padding-right:40px;
        }

        .password-wrapper i{
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            color:#ccc;
            z-index:10;
        }

        /* Spacing */
        .mb-3{
            margin-bottom:12px !important;
        }

        /* Error messages */
        .error{
            color:#ff6b6b;
            font-size:13px;
            display:block;
            margin-top:2px;
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
                transform:translateY(60px);   /* starts lower */
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }
        .error{
            color: red;
            font-size: 14px;
            margin-top: 3px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="login-header">
            <img src="../image/logo.jpg.jpeg" alt="Roadside Companion Logo">
            <h2>LOGIN</h2>
        </div>
        <?php
            if(isset($_SESSION['login_error'])){
                echo "<p style='color:red'>".$_SESSION['login_error']."</p>";
                unset($_SESSION['login_error']);
            }
        ?>
        <form method="POST" action="login.php" id="loginForm" onsubmit="return validateLogin()">
            <div class="mb-3 text-start">
                <label>User Type</label>
                <select name="usertype" class="form-select">
                    <option value="" selected disabled>-Select-User-Type-</option>
                    <option value="administrator">Admin</option>
                    <option value="service-provider">Mechanic</option>
                    <option value="customer">User</option>
                </select>
                <label class="error" id="usertypeError"></label>
            </div>

            <div class="mb-3 text-start">
                <label>Phone/Email</label>
                <input type="text" name="login_id" class="form-control">
                <label class="error" id="login_idError"></label>
            </div>

            <div class="mb-3 text-start">
                <label>Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control">
                    <i class="bi bi-eye-slash" onclick="togglePassword()" id="eye"></i>
                </div>
                <label class="error" id="passwordError"></label>
            </div>

            <button type="submit" name="login" class="btn btn-blue w-100">Login</button>

            <div class="links">
                <a href="#">Forgot Password?</a><br>
                Don't have an account? <a href="register1.php">Sign Up</a>
            </div>
        </form>
    </div>
    <script src="./login-validation.js"></script>
    </body>
</html>
<?php
// include_once 'footer.php';
?>



