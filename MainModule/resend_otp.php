<?php
session_start();

$email=$_SESSION['otp_email'];

$otp=rand(1000,9999);

$_SESSION['otp']=$otp;

use PHPMailer\PHPMailer\PHPMailer;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'yourgmail@gmail.com';
$mail->Password = 'your_app_password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('yourgmail@gmail.com','Roadside Companion');

$mail->addAddress($email);

$mail->isHTML(true);

$mail->Subject = 'Resend OTP';

$mail->Body    = "<h2>Your new OTP is : $otp</h2>";

$mail->send();

header("location:verify_otp.php");
?>