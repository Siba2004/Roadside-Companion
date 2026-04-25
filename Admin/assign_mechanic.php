<?php
session_start();
require_once '../dbcon.php';

// ---- AUTHENTICATION ----
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit();
}

// ---- GET PARAMETERS ----
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$emergency_id = isset($_GET['emergency']) ? (int)$_GET['emergency'] : 0;

if (($booking_id <= 0 && $emergency_id <= 0) || ($booking_id > 0 && $emergency_id > 0)) {
    die("Invalid request.");
}

// ---- HANDLE ASSIGNMENT ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mechanic_id'])) {
    $mechanic_id = (int)$_POST['mechanic_id'];

    if ($booking_id > 0) {
        $stmt = $conn->prepare("UPDATE bookings SET mechanic_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $mechanic_id, $booking_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Mechanic assigned to booking #' . $booking_id];
        header("location: admin_home.php?page=bookings");
        exit;
    } elseif ($emergency_id > 0) {
        $stmt = $conn->prepare("UPDATE emergency_requests SET mechanic_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $mechanic_id, $emergency_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Mechanic assigned to emergency #' . $emergency_id];
        header("location: admin_home.php?page=emergency");
        exit;
    }
}

// ---- FETCH ALL MECHANICS (service-providers) ----
$mechanics = $conn->query("SELECT id, name, phone_number, status FROM users_details WHERE accounttype='service-provider'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Mechanic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7fc; font-family: 'Segoe UI', sans-serif; }
        .container { max-width: 600px; margin: 80px auto; }
        .card { border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .card-header { background: #0d6efd; color: white; border-radius: 15px 15px 0 0; }
        .list-group-item:hover { background: #f0f4ff; cursor: pointer; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-user-cog"></i> Assign Mechanic</h4>
            <small><?= $booking_id ? "Booking #$booking_id" : "Emergency #$emergency_id" ?></small>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="list-group">
                    <?php while ($m = $mechanics->fetch_assoc()): ?>
                    <label class="list-group-item">
                        <input class="form-check-input me-2" type="radio" name="mechanic_id" value="<?= $m['id'] ?>" required>
                        <strong><?= htmlspecialchars($m['name']) ?></strong>
                        <small class="text-muted ms-3"><?= htmlspecialchars($m['phone_number']) ?></small>
                        <span class="badge bg-<?= $m['status'] == 'available' ? 'success' : 'warning' ?> float-end"><?= $m['status'] ?></span>
                    </label>
                    <?php endwhile; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3"><i class="fas fa-check-circle"></i> Confirm Assignment</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>