<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];

// ---- Safe table checking ----
function table_exists($conn, $table) {
    static $cache = [];
    if (isset($cache[$table])) return $cache[$table];
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    $exists = ($result && $result->num_rows > 0);
    $cache[$table] = $exists;
    return $exists;
}

$bookings_exist = table_exists($conn, 'bookings');

// ---- Handle cancellation (via GET) ----
if (isset($_GET['cancel']) && $bookings_exist) {
    $bid = (int)$_GET['cancel'];
    // Only cancel if booking belongs to this user and is pending
    $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $bid, $user_id);
    $stmt->execute();
    $stmt->close();
    header("location: my_services.php");
    exit;
}

// ---- Fetch bookings ----
$bookings = [];
if ($bookings_exist) {
    $result = $conn->query("SELECT * FROM bookings WHERE user_id = $user_id ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Services - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            /* Solid dark background – no dependency on the image */
            background: #0a0a0f;
            font-family: 'Poppins', sans-serif;
            color: white;
            margin: 0;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            /* Subtle gradient overlay for depth */
            background: linear-gradient(135deg, rgba(13,110,253,0.03) 0%, rgba(0,0,0,0.8) 100%);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            padding-top: 80px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .page-header h1 {
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px rgba(13,110,253,0.5);
        }
        .table-custom {
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            overflow: hidden;
        }
        .table-custom th {
            background: rgba(13,110,253,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        .table-custom td {
            vertical-align: middle;
            border-color: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.9);
        }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-accepted { background: #17a2b8; }
        .badge-inprogress { background: #0d6efd; }
        .badge-completed { background: #198754; }
        .badge-cancelled { background: #6c757d; }
        .btn-sm i { font-size: 0.9rem; }
        .empty-message {
            background: rgba(0,0,0,0.5);
            padding: 40px;
            text-align: center;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-tools me-2"></i>MY SERVICES</h1>
            <p>Your booked services and packages</p>
        </div>

        <?php if (!$bookings_exist): ?>
            <div class="empty-message">
                <i class="fas fa-info-circle fa-3x mb-3" style="color:#0d6efd;"></i>
                <h4>No booking system available yet</h4>
                <p>The booking feature is currently being set up. Please check back later.</p>
            </div>
        <?php elseif (count($bookings) == 0): ?>
            <div class="empty-message">
                <i class="fas fa-clipboard-list fa-3x mb-3" style="color:#0d6efd;"></i>
                <h4>No services booked yet</h4>
                <p>You haven't booked any services. <a href="services.php">Browse services</a> to book one.</p>
            </div>
        <?php else: ?>
            <div class="table-custom table-responsive">
                <table class="table table-dark table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b):
                            $statusClass = 'badge-'.strtolower($b['status']);
                        ?>
                        <tr>
                            <td>#<?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['item_name']) ?> (<?= $b['item_type'] ?>)</td>
                            <td><?= $b['booking_date'] ?> (<?= $b['time_slot'] ?>)</td>
                            <td>₹<?= $b['amount'] ?></td>
                            <td>
                                <span class="badge bg-<?= $b['payment_status'] == 'paid' ? 'success' : 'warning' ?>">
                                    <?= $b['payment_status'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $statusClass ?>"><?= $b['status'] ?></span>
                            </td>
                            <td>
                                <?php if ($b['status'] == 'pending'): ?>
                                    <a href="?cancel=<?= $b['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?')">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="home.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Back to Home</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>