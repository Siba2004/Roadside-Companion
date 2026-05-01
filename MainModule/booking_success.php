<?php
session_start();
require_once '../dbcon.php';

// ---- ONLY CUSTOMERS CAN BOOK ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

$message = $_SESSION['message'] ?? null;
if (!$message) {
    header('location: services.php');
    exit();
}

// Clear the message after displaying
unset($_SESSION['message']);

include_once 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f0f1a 100%);
            min-height: 100vh;
            color: white;
        }
        
        .success-container {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
        }
        
        .success-card {
            background: rgba(15, 20, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 40px;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .btn-home {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border: none;
            padding: 12px 30px;
            margin-top: 20px;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13,110,253,0.4);
        }
    </style>
</head>
<body>

<div class="success-container">
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Booking Successful!</h2>
        <p class="mt-3"><?= htmlspecialchars($message['text']) ?></p>
        <p class="mt-2">A confirmation has been sent to your registered mobile number.</p>
        <a href="my_services.php" class="btn btn-home">
            <i class="fas fa-eye me-2"></i>View My Bookings
        </a>
        <div class="mt-3">
            <a href="services.php" class="text-white-50">Book Another Service</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include_once 'footer.php'; ?>