<?php
session_start();

$error = "";

if(isset($_POST['verify'])){

// OTP expiry check
$expiry_time = 300; // 5 minutes

if(time() - $_SESSION['otp_time'] > $expiry_time){
    echo "<script>alert('OTP expired. Please request a new OTP'); window.location='register1.php';</script>";
    exit();
}

// collect OTP digits
$o1 = $_POST['o1'];
$o2 = $_POST['o2'];
$o3 = $_POST['o3'];
$o4 = $_POST['o4'];

$userotp = $o1.$o2.$o3.$o4;

// verify OTP
if($userotp == $_SESSION['otp']){

$_SESSION['email_verified'] = true;

header("location: register.php");
exit();

}else{

$error = "Invalid OTP";

}

}
?>

<html>
<head>

<link href="../bootstrap.min.css" rel="stylesheet">

<style>

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background-image:url('pic/register_bg.jpeg');
background-size:cover;

backdrop-filter:blur(4px);
}

.box{
background:rgba(0,0,0,0.65);
padding:30px;
border-radius:15px;
text-align:center;
color:white;
width:350px;
}

.otp{
width:50px;
height:50px;
font-size:22px;
text-align:center;
margin:5px;
border-radius:8px;
border:1px solid #ccc;
}

button{
margin-top:10px;
}

</style>

</head>

<body>

<div class="box">

<h3>Enter OTP</h3>

<?php if($error!=""){ echo "<p style='color:red;'>$error</p>"; } ?>

<form method="POST">

<input type="text" maxlength="1" name="o1" class="otp" id="otp1" required>
<input type="text" maxlength="1" name="o2" class="otp" id="otp2" required>
<input type="text" maxlength="1" name="o3" class="otp" id="otp3" required>
<input type="text" maxlength="1" name="o4" class="otp" id="otp4" required>

<br>

<button class="btn btn-primary" name="verify">Verify OTP</button>

</form>

<p style="margin-top:10px;">
Resend OTP in <span id="countdown">60</span> seconds
</p>

<button id="resendBtn" class="btn btn-warning" disabled onclick="window.location='resend_otp.php'">
Resend OTP
</button>

</div>

<script>

// RESEND TIMER
let timer = 60;

let interval = setInterval(function(){

timer--;

document.getElementById("countdown").innerText = timer;

if(timer <= 0){

clearInterval(interval);

document.getElementById("resendBtn").disabled = false;

document.getElementById("countdown").innerText = "0";

}

},1000);


// AUTO FOCUS OTP BOXES

const inputs = document.querySelectorAll(".otp");

inputs.forEach((input,index)=>{

input.addEventListener("keyup",(e)=>{

if(input.value.length === 1 && index < inputs.length-1){

inputs[index+1].focus();

}

if(e.key === "Backspace" && index > 0){

inputs[index-1].focus();

}

});

});

</script>

</body>
</html>