<?php
session_start();
require_once '../dbcon.php';

// ---- ONLY CUSTOMERS CAN BOOK ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$service_name = isset($_GET['service']) ? $_GET['service'] : '';
$price = isset($_GET['price']) ? floatval($_GET['price']) : 0;

if (empty($service_name) || $price <= 0) {
    header('location: services.php');
    exit();
}

$table_check = $conn->query("SHOW TABLES LIKE 'bookings'");
$table_exists = ($table_check && $table_check->num_rows > 0);
$column_check = $table_exists ? $conn->query("SHOW COLUMNS FROM bookings LIKE 'location'") : false;
$location_col_exists = ($column_check && $column_check->num_rows > 0);

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $table_exists) {
    $booking_date = $_POST['booking_date'] ?? '';
    $time_slot = $_POST['time_slot'] ?? '';
    $location = trim($_POST['location'] ?? '');

    if (empty($booking_date) || empty($time_slot) || empty($location)) {
        $message = '<div class="alert alert-danger">All fields are required.</div>';
    } else {
        if ($location_col_exists) {
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, item_name, item_type, booking_date, time_slot, amount, payment_status, status, location)
                                    VALUES (?, ?, 'service', ?, ?, ?, 'pending', 'pending', ?)");
            $stmt->bind_param("isssds", $user_id, $service_name, $booking_date, $time_slot, $price, $location);
        } else {
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, item_name, item_type, booking_date, time_slot, amount, payment_status, status)
                                    VALUES (?, ?, 'service', ?, ?, ?, 'pending', 'pending')");
            $stmt->bind_param("isssd", $user_id, $service_name, $booking_date, $time_slot, $price);
        }
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Booking successful! We will assign a mechanic soon.'];
            header('location: my_services.php');
            exit;
        } else {
            $message = '<div class="alert alert-danger">Booking failed. Please try again.</div>';
        }
        $stmt->close();
    }
}

// Generate time slots (24h, every 30 minutes)
$time_slots = [];
for ($h = 0; $h < 24; $h++) {
    $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
    $time_slots[] = "$hour:00";
    $time_slots[] = "$hour:30";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #0a0a0f;                /* solid dark base */
            font-family: 'Poppins', sans-serif;
            color: white;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(13,110,253,0.03) 0%, rgba(0,0,0,0.8) 100%);
            z-index: 0;
        }
        .booking-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        .booking-card {
            background: rgba(20,20,30,0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 35px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.7);
        }
        .booking-card h3 {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
            text-shadow: 0 0 10px rgba(13,110,253,0.5);
        }
        .service-info {
            background: rgba(13,110,253,0.12);
            border: 1px solid rgba(13,110,253,0.35);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .service-info h5 {
            color: #fff;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .service-info .price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0d6efd;
        }
        .form-control, .form-select {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15);
            border-color: #0d6efd;
            box-shadow: 0 0 10px #0d6efd;
            color: white;
        }
        .form-select option {
            background: #1a1a2e;
            color: white;
        }
        label {
            font-weight: 500;
            margin-bottom: 6px;
            color: rgba(255,255,255,0.9);
        }
        .btn-book {
            background: #0d6efd;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-book:hover {
            background: #0b5ed7;
            box-shadow: 0 10px 20px rgba(13,110,253,0.4);
            transform: translateY(-2px);
        }
        .text-white-50 { color: rgba(255,255,255,0.6) !important; }
        .text-white-50:hover { color: white !important; }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="booking-card">
            <h3><i class="fas fa-calendar-check me-2"></i>Book Service</h3>
            <?php if (!$table_exists): ?>
                <div class="alert alert-warning">Booking system not available yet.</div>
            <?php else: ?>
                <?= $message ?>
                <div class="service-info">
                    <h5><?= htmlspecialchars($service_name) ?></h5>
                    <div class="price">₹<?= number_format($price, 2) ?></div>
                </div>
                <form method="post">
                    <div class="mb-3">
                        <label>Preferred Date</label>
                        <input type="date" name="booking_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Preferred Time (any 30‑min slot)</label>
                        <select name="time_slot" class="form-select" required>
                            <option value="">-- Select Time --</option>
                            <?php foreach ($time_slots as $slot): ?>
                            <option value="<?= $slot ?>"><?= $slot ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Location / Address</label>
                        <input type="text" name="location" class="form-control" placeholder="Enter your current location" required>
                    </div>
                    <button type="submit" class="btn btn-book">Confirm Booking</button>
                </form>
            <?php endif; ?>
            <div class="text-center mt-3">
                <a href="services.php" class="text-white-50">← Back to Services</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>