<?php
session_start();
require_once '../dbcon.php';

// ---- ONLY CUSTOMERS CAN BOOK ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

// Check if booking data and mechanic exist
if (!isset($_SESSION['pending_booking']) || !isset($_SESSION['selected_mechanic'])) {
    header('location: services.php');
    exit();
}

$user_id = $_SESSION['id'];
$booking_data = $_SESSION['pending_booking'];
$mechanic_id = $_SESSION['selected_mechanic'];

// Get mechanic details from users_details table
$stmt = $conn->prepare("SELECT * FROM users_details WHERE id = ? AND accounttype = 'service-provider'");
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();
$mechanic = $result->fetch_assoc();
$stmt->close();

if (!$mechanic) {
    header('location: services.php');
    exit();
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Insert booking into database
    $booking_date = $booking_data['booking_date'];
    $time_slot = $booking_data['time_slot'];
    $location = $booking_data['location'];
    $vehicle_type = $booking_data['vehicle_type'];
    $vehicle_number = $booking_data['vehicle_number'];
    $vehicle_model = $booking_data['vehicle_model'];
    $special_requests = $booking_data['special_requests'];
    $contact_phone = $booking_data['contact_phone'];
    $service_name = $booking_data['service_name'];
    $price = $booking_data['price'];
    
    // Check if bookings table has required columns
    $col_check = $conn->query("SHOW COLUMNS FROM bookings");
    $existing_cols = [];
    while ($col = $col_check->fetch_assoc()) {
        $existing_cols[] = $col['Field'];
    }
    
    // Build insert query based on existing columns
    $columns = ["user_id", "item_name", "item_type", "booking_date", "time_slot", "amount", "payment_status", "status"];
    $values = ["?", "?", "'service'", "?", "?", "?", "'pending'", "'confirmed'"];
    $params = [$user_id, $service_name, $booking_date, $time_slot, $price];
    $types = "isssd";
    
    if (in_array('location', $existing_cols)) {
        $columns[] = "location";
        $values[] = "?";
        $params[] = $location;
        $types .= "s";
    }
    
    if (in_array('mechanic_id', $existing_cols)) {
        $columns[] = "mechanic_id";
        $values[] = "?";
        $params[] = $mechanic_id;
        $types .= "i";
    }
    
    if (in_array('vehicle_type', $existing_cols)) {
        $columns[] = "vehicle_type";
        $values[] = "?";
        $params[] = $vehicle_type;
        $types .= "s";
    }
    
    if (in_array('vehicle_number', $existing_cols)) {
        $columns[] = "vehicle_number";
        $values[] = "?";
        $params[] = $vehicle_number;
        $types .= "s";
    }
    
    if (in_array('vehicle_model', $existing_cols)) {
        $columns[] = "vehicle_model";
        $values[] = "?";
        $params[] = $vehicle_model;
        $types .= "s";
    }
    
    if (in_array('special_requests', $existing_cols)) {
        $columns[] = "special_requests";
        $values[] = "?";
        $params[] = $special_requests;
        $types .= "s";
    }
    
    if (in_array('contact_phone', $existing_cols)) {
        $columns[] = "contact_phone";
        $values[] = "?";
        $params[] = $contact_phone;
        $types .= "s";
    }
    
    $sql = "INSERT INTO bookings (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Update mechanic availability
        $availability_check = $conn->query("SHOW TABLES LIKE 'mechanic_availability'");
        if ($availability_check && $availability_check->num_rows > 0) {
            // Mark mechanic as busy
            $update_stmt = $conn->prepare("INSERT INTO mechanic_availability (mechanic_id, status, booking_time) VALUES (?, 'busy', NOW())");
            $update_stmt->bind_param("i", $mechanic_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
        
        // Clear sessions
        unset($_SESSION['pending_booking']);
        unset($_SESSION['selected_mechanic']);
        
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Booking confirmed! Mechanic has been notified and will reach you shortly.'];
        header('location: booking_success.php');
        exit();
    } else {
        $message = '<div class="alert alert-danger">Booking failed: ' . $conn->error . '</div>';
    }
    $stmt->close();
}

include_once 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f0f1a 100%);
            color: white;
        }
        
        .main-wrapper {
            padding: 35px 20px 60px;
        }
        
        .confirm-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .booking-card, .mechanic-card {
            background: rgba(15, 20, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .mechanic-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 15px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .btn-confirm {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(40,167,69,0.4);
        }
        
        .btn-back {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            padding: 10px 25px;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="confirm-container">
        <div class="text-center mb-4">
            <h2><i class="fas fa-clipboard-list me-2"></i>Confirm Your Booking</h2>
            <p>Review your details before confirming</p>
        </div>
        
        <?= $message ?>
        
        <div class="row">
            <div class="col-lg-6">
                <div class="booking-card">
                    <h4><i class="fas fa-info-circle me-2"></i>Booking Details</h4>
                    <div class="detail-row">
                        <span>Service:</span>
                        <strong><?= htmlspecialchars($booking_data['service_name']) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Amount:</span>
                        <strong class="text-primary">₹<?= number_format($booking_data['price']) ?> + GST</strong>
                    </div>
                    <div class="detail-row">
                        <span>Date & Time:</span>
                        <strong><?= htmlspecialchars($booking_data['booking_date']) ?> at <?= htmlspecialchars($booking_data['time_slot']) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Location:</span>
                        <strong><?= htmlspecialchars($booking_data['location']) ?></strong>
                    </div>
                    <div class="detail-row">
                        <span>Vehicle:</span>
                        <strong><?= htmlspecialchars($booking_data['vehicle_type']) ?> - <?= htmlspecialchars($booking_data['vehicle_model']) ?></strong>
                    </div>
                    <?php if (!empty($booking_data['vehicle_number'])): ?>
                    <div class="detail-row">
                        <span>Vehicle Number:</span>
                        <strong><?= htmlspecialchars($booking_data['vehicle_number']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="detail-row">
                        <span>Contact:</span>
                        <strong><?= htmlspecialchars($booking_data['contact_phone']) ?></strong>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="mechanic-card">
                    <div class="text-center">
                        <div class="mechanic-avatar">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <h4><?= htmlspecialchars($mechanic['name']) ?></h4>
                        <p><i class="fas fa-phone me-2"></i><?= htmlspecialchars($mechanic['phone_number']) ?></p>
                        <p><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($mechanic['email']) ?></p>
                        <?php if (!empty($mechanic['location'])): ?>
                        <p><i class="fas fa-map-marker-alt me-2"></i><?= htmlspecialchars($mechanic['location']) ?></p>
                        <?php endif; ?>
                        <div class="mt-3">
                            <span class="badge bg-success p-2">
                                <i class="fas fa-check-circle"></i> Available Now
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form method="post" class="text-center mt-4">
            <button type="submit" class="btn btn-confirm btn-lg">
                <i class="fas fa-check-circle me-2"></i>Confirm Booking
            </button>
            <a href="search_mechanic.php" class="btn btn-back btn-lg ms-3">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include_once 'footer.php'; ?>