<?php
session_start();
require_once '../dbcon.php';
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'service-provider') {
    header('location: ../login.php');
    exit;
}

$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mechanic_id = $_SESSION['id'];

// Check that this booking is assigned to this mechanic
$job = $conn->query("SELECT b.*, u.name AS user_name, u.phone_number, u.location AS user_address
                     FROM bookings b
                     JOIN users_details u ON b.user_id = u.id
                     WHERE b.id = $booking_id AND b.mechanic_id = $mechanic_id")->fetch_assoc();

if (!$job) die("Job not found or not assigned to you.");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Job Details #<?= $job['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h5>Booking #<?= $job['id'] ?> Details</h5></div>
        <div class="card-body">
            <p><strong>Customer:</strong> <?= htmlspecialchars($job['user_name']) ?> (<?= htmlspecialchars($job['phone_number']) ?>)</p>
            <p><strong>Service:</strong> <?= htmlspecialchars($job['item_name']) ?> (<?= $job['item_type'] ?>)</p>
            <p><strong>Date / Time:</strong> <?= $job['booking_date'] ?> <?= $job['time_slot'] ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($job['user_address'] ?? 'N/A') ?></p>
            <p><strong>Amount:</strong> ₹<?= $job['amount'] ?></p>
            <p><strong>Payment Status:</strong> <span class="badge bg-<?= $job['payment_status']=='paid'?'success':'warning' ?>"><?= $job['payment_status'] ?></span></p>
            <p><strong>Job Status:</strong> <?= $job['status'] ?></p>
            <a href="service_home.php?page=assigned" class="btn btn-secondary">Back to Jobs</a>
            <a href="tel:<?= $job['phone_number'] ?>" class="btn btn-success"><i class="fas fa-phone"></i> Call Customer</a>
        </div>
    </div>
</div>
</body>
</html>