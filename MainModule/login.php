<?php
session_start();
require_once '../dbcon.php';
?>
<html>
    <head>
        <title>login page</title>
        <style>
            .error{
                color:red;
                display: block;
            }
        </style>
    </head>
    <body>
        <?php include_once 'navbar.php'; ?>

        <div>
    <h1 class="text-center text-light">Login to Your Account</h1>
    <form action="login.php" method="post" id="loginForm" onsubmit="validate(event)" class="w-50 mx-auto p-4 border rounded bg-transparent shadow">
        <?php
        if(isset($_SESSION['login_error'])){
            echo '<div class="error-message text-center text-danger">'.$_SESSION['login_error'].'</div>';
            unset($_SESSION['login_error']);
        }?>
        Email/PhoneNumber :
        <input type="text" name="login_id"><br>
        <label class="error" id="login_idError"></label><br>
        Password : 
        <input type="password" name="password"><br>
        <label class="error" id="passwordError"></label><br>
        <br>
        <input type="submit" value="Login" class="btn btn-primary btn-center">
        <p class="text-center text-danger">If not register<a href="register.php" class="text-success">Register-Here</a></p>
    </form>
</div>
<script src="login-validation.js"></script>
    </body>
</html>

<?php
include_once 'footer.php';
if($_SERVER['REQUEST_METHOD']=="POST"){
    $login_id=$_POST['login_id'];
    $password=$_POST['password'];

    $qry="SELECT * FROM login_validation_details WHERE (email=? OR phone_number=?) AND password=?";
    $stmt=$conn->prepare($qry);
    $stmt->bind_param("sss",$login_id,$login_id,$password);
    $stmt->execute();
    $result=$stmt->get_result();
    if($result->num_rows==1){
        $data=$result->fetch_assoc();
        session_start();
        $_SESSION['name']=$data['name'];
        $_SESSION['user_id']=$data['id'];
        header("location:home.php");
    }else {
        $_SESSION['login_error'] = "Incorrect Login_id or Password !";
        header("location: login.php");
    }
}
?>