<?php
session_start();
require_once '../dbcon.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if(isset($_POST['send_otp'])){

$email=$_POST['email'];
$otp=rand(1000,9999);

$_SESSION['otp_email']=$email;
$_SESSION['otp']=$otp;
$_SESSION['otp_time'] = time();

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;

$mail->Username = 'roadsidecompanionsu@gmail.com';
$mail->Password = 'xkumkrlsrnqaxtmh';

$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('roadsidecompanionsu@gmail.com', 'Roadside Companion');

$mail->addAddress($email);

$mail->Subject = "Roadside Companion Email Verification";

$mail->Body = "Your OTP is: $otp";

$mail->send();

$q="INSERT INTO email_otp(email,otp) VALUES (?,?)";
$stmt=$conn->prepare($q);
$stmt->bind_param("ss",$email,$otp);
$stmt->execute();

/* Send OTP Mail */

$subject="Your OTP Verification";
$message="Your OTP is: ".$otp;
$headers="From: roadsidecompanion@gmail.com";

mail($email,$subject,$message,$headers);

header("location: verify_otp.php");
exit();
}
?>

<html>
<head>
<title>Email Verification</title>
<link href="../bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">

<style>

body{
height:100vh;
margin:0;
font-family:'Poppins',sans-serif;
background-image:url('pic/otp_verify_bg.jpeg');
background-size:cover;
background-position:center;
display:flex;
justify-content:center;
align-items:center;
}

body::before{
content:"";
position:absolute;
width:100%;
height:100%;
background:rgba(0,0,0,0.65);
}

.box{
position:relative;
width:360px;
padding:25px;
background:rgba(0,0,0,0.65);
border-radius:15px;
color:white;
text-align:center;
}

.form-control{
background:rgba(255,255,255,0.05);
border:1px solid rgba(255,255,255,0.2);
color:white;
}

.btn-blue{
background:#0d6efd;
border:none;
}

</style>
</head>

<body>

<div class="box">

<h3>Email Verification</h3>

<form method="POST">

<label>Enter your Email</label>
<input type="email" name="email" class="form-control" required>

<br>

<button class="btn btn-blue w-100" name="send_otp">Send OTP</button>

</form>

</div>

</body>
</html>