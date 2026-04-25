<?php
session_start();
require_once '../dbcon.php';

// ---- AUTHENTICATION ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'service-provider') {
    header('location: ../login.php');
    exit();
}

$mechanic_id = $_SESSION['id'];

// ---- ERROR DISPLAY (remove after everything works) ----
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---- DATABASE CHECK ----
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

function table_exists($conn, $table) {
    static $cache = [];
    if (isset($cache[$table])) return $cache[$table];
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    $exists = ($result && $result->num_rows > 0);
    $cache[$table] = $exists;
    return $exists;
}

function column_exists($conn, $table, $column) {
    static $colCache = [];
    $key = "$table.$column";
    if (isset($colCache[$key])) return $colCache[$key];
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    $exists = ($result && $result->num_rows > 0);
    $colCache[$key] = $exists;
    return $exists;
}

// ---- CHANGE OWN STATUS (AJAX) ----
if (isset($_GET['action']) && $_GET['action'] === 'update_availability' && column_exists($conn, 'users_details', 'status')) {
    $new_status = $_GET['status'] ?? '';
    $allowed = ['available', 'busy', 'offline'];
    if (in_array($new_status, $allowed)) {
        $conn->query("UPDATE users_details SET status = '$new_status' WHERE id = $mechanic_id");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ---- UPDATE JOB STATUS (AJAX) ----
if (isset($_GET['action']) && $_GET['action'] === 'update_job_status' && isset($_GET['booking_id'], $_GET['status']) && table_exists($conn, 'bookings') && column_exists($conn, 'bookings', 'mechanic_id')) {
    $booking_id = (int)$_GET['booking_id'];
    $new_status = $_GET['status'];
    $allowed = ['accepted', 'in_progress', 'completed', 'cancelled'];
    if (in_array($new_status, $allowed)) {
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND mechanic_id = ?");
        $stmt->bind_param("sii", $new_status, $booking_id, $mechanic_id);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ---- PROFILE UPDATE ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name']);
    $new_phone = trim($_POST['phone_number']);
    $new_location = trim($_POST['location']);
    $new_password = $_POST['password'];

    if (!empty($new_password)) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users_details SET name = ?, phone_number = ?, location = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $new_name, $new_phone, $new_location, $hashed, $mechanic_id);
    } else {
        $stmt = $conn->prepare("UPDATE users_details SET name = ?, phone_number = ?, location = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_name, $new_phone, $new_location, $mechanic_id);
    }
    $stmt->execute();
    $stmt->close();

    $_SESSION['name'] = $new_name;
    $_SESSION['message'] = ['type' => 'success', 'text' => 'Profile updated.'];
    header("location: service_home.php?page=profile");
    exit;
}

// ---- FLASH MESSAGES ----
$message = '';
if (isset($_SESSION['message'])) {
    $msg = $_SESSION['message'];
    $message = '<div class="alert alert-'.$msg['type'].' alert-dismissible fade show">'
              .$msg['text']
              .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['message']);
}

// ---- FETCH MECHANIC DATA ----
$mech = $conn->query("SELECT * FROM users_details WHERE id = $mechanic_id")->fetch_assoc();

// ---- CURRENT STATUS ----
$current_status = 'available';
if (column_exists($conn, 'users_details', 'status')) {
    $current_status = $mech['status'] ?? 'available';
}

// ---- CHECK IF KEY COLUMNS EXIST ----
$bookings_exist = table_exists($conn, 'bookings');
$emergency_exist = table_exists($conn, 'emergency_requests');
$bookings_has_mechanic = $bookings_exist && column_exists($conn, 'bookings', 'mechanic_id');
$emergency_has_mechanic = $emergency_exist && column_exists($conn, 'emergency_requests', 'mechanic_id');

// ---- STATS (use only if columns exist) ----
$assigned_jobs = 0; $completed_today = 0; $earnings_today = 0; $earnings_week = 0; $earnings_month = 0;
if ($bookings_has_mechanic) {
    $assigned_jobs   = $conn->query("SELECT COUNT(*) AS cnt FROM bookings WHERE mechanic_id = $mechanic_id AND status IN ('pending','accepted','in_progress')")->fetch_object()->cnt;
    $completed_today = $conn->query("SELECT COUNT(*) AS cnt FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND DATE(created_at) = CURDATE()")->fetch_object()->cnt;
    $earnings_today  = $conn->query("SELECT COALESCE(SUM(amount),0) AS rev FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND DATE(created_at) = CURDATE()")->fetch_object()->rev;
    $earnings_week   = $conn->query("SELECT COALESCE(SUM(amount),0) AS rev FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)")->fetch_object()->rev;
    $earnings_month  = $conn->query("SELECT COALESCE(SUM(amount),0) AS rev FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())")->fetch_object()->rev;
}

$page = $_GET['page'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mechanic Dashboard - RoadSide Companion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7fc; font-family: 'Segoe UI', sans-serif; }
        .wrapper { display: flex; }
        .sidebar {
            min-width: 250px;
            background: linear-gradient(135deg, #0b1220, #0f1f3d);
            color: white;
            min-height: 100vh;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: 0.3s;
            font-weight: 500;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid #0d6efd;
        }
        .sidebar i { margin-right: 10px; width: 20px; }
        .content { flex: 1; padding: 20px 30px; }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
        }
        .stats-card .number { font-size: 2rem; font-weight: bold; }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .badge-emergency { background: #dc3545; color: white; }
        .badge-assigned { background: #0dcaf0; }
        .badge-progress { background: #0d6efd; color: white; }
        .badge-completed { background: #198754; }
        .badge-cancelled { background: #6c757d; }
        .availability-select { width: 150px; display: inline-block; margin-left: 10px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4><i class="fas fa-tools"></i> Mechanic Panel</h4>
            <div class="mt-2">
                <span class="badge bg-<?= $current_status == 'available' ? 'success' : ($current_status == 'busy' ? 'warning' : 'secondary') ?>"><?= ucfirst($current_status) ?></span>
            </div>
        </div>
        <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=assigned" class="<?= $page=='assigned'?'active':'' ?>"><i class="fas fa-clipboard-list"></i> My Assigned Jobs <span class="badge bg-danger"><?= $assigned_jobs ?></span></a>
        <a href="?page=emergency" class="<?= $page=='emergency'?'active':'' ?>"><i class="fas fa-exclamation-triangle"></i> Emergency Jobs</a>
        <a href="?page=history" class="<?= $page=='history'?'active':'' ?>"><i class="fas fa-history"></i> Job History</a>
        <a href="?page=earnings" class="<?= $page=='earnings'?'active':'' ?>"><i class="fas fa-rupee-sign"></i> Earnings</a>
        <a href="?page=notifications" class="<?= $page=='notifications'?'active':'' ?>"><i class="fas fa-bell"></i> Notifications <span class="badge bg-warning"><?= table_exists($conn, 'notifications') ? $conn->query("SELECT COUNT(*) AS cnt FROM notifications WHERE is_read=0")->fetch_object()->cnt : 0 ?></span></a>
        <a href="?page=profile" class="<?= $page=='profile'?'active':'' ?>"><i class="fas fa-user"></i> My Profile</a>
        <a href="../MainModule/logout.php" class="mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <?= $message ?>
        <div class="top-bar">
            <h2>Welcome, <?= htmlspecialchars($mech['name']) ?></h2>
            <div>
                <span>Status: </span>
                <select class="form-select availability-select" onchange="updateAvailability(this.value)">
                    <option value="available" <?= $current_status == 'available' ? 'selected' : '' ?>>Available</option>
                    <option value="busy" <?= $current_status == 'busy' ? 'selected' : '' ?>>Busy</option>
                    <option value="offline" <?= $current_status == 'offline' ? 'selected' : '' ?>>Offline</option>
                </select>
            </div>
        </div>

        <?php if ($page == 'dashboard'): ?>
            <div class="row">
                <div class="col-md-3"><div class="stats-card"><div class="number"><?= $assigned_jobs ?></div><div>Assigned Jobs</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?= $completed_today ?></div><div>Completed Today</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?= number_format($earnings_today) ?></div><div>Today's Earnings</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?= number_format($earnings_month) ?></div><div>Monthly Earnings</div></div></div>
            </div>
            <p>Use the sidebar to view assigned jobs, history, etc.</p>

        <?php elseif ($page == 'assigned'): ?>
            <h2>My Assigned Jobs</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Booking ID</th><th>Customer</th><th>Service</th><th>Address</th><th>Date/Time</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php if ($bookings_has_mechanic):
                            $jobs = $conn->query("SELECT b.*, u.name AS user_name, u.phone_number, u.location AS user_address
                                                  FROM bookings b JOIN users_details u ON b.user_id = u.id
                                                  WHERE b.mechanic_id = $mechanic_id AND b.status IN ('pending','accepted','in_progress')
                                                  ORDER BY b.id DESC");
                            while ($j = $jobs->fetch_assoc()):
                                $status_class = ($j['status'] == 'pending') ? 'badge-pending' : (($j['status'] == 'accepted') ? 'badge-assigned' : 'badge-progress'); ?>
                        <tr>
                            <td>#<?= $j['id'] ?></td>
                            <td><?= htmlspecialchars($j['user_name']) ?><br><small><?= htmlspecialchars($j['phone_number']) ?></small></td>
                            <td><?= htmlspecialchars($j['item_name']) ?></td>
                            <td><?= htmlspecialchars($j['user_address'] ?? '') ?></td>
                            <td><?= $j['booking_date'] ?> <?= $j['time_slot'] ?></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateJobStatus(<?= $j['id'] ?>, this.value)">
                                    <option value="accepted" <?= $j['status'] == 'accepted' ? 'selected' : '' ?>>Start</option>
                                    <option value="in_progress" <?= $j['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="completed" <?= $j['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $j['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <a href="tel:<?= $j['phone_number'] ?>" class="btn btn-sm btn-success"><i class="fas fa-phone"></i></a>
                                <a href="job_details.php?id=<?= $j['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="7">No assigned jobs yet (or mechanic_id column missing).</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page == 'emergency'): ?>
            <h2>Emergency Jobs</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Customer</th><th>Location</th><th>Issue</th><th>Time</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if ($emergency_has_mechanic):
                            $emerg = $conn->query("SELECT e.*, u.name AS user_name, u.phone_number
                                                   FROM emergency_requests e
                                                   JOIN users_details u ON e.user_id = u.id
                                                   WHERE e.mechanic_id = $mechanic_id
                                                   ORDER BY e.id DESC");
                            if ($emerg->num_rows > 0):
                                while ($em = $emerg->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $em['id'] ?></td>
                            <td><?= htmlspecialchars($em['user_name']) ?><br><?= htmlspecialchars($em['phone_number']) ?></td>
                            <td><?= htmlspecialchars($em['location']) ?></td>
                            <td><?= htmlspecialchars($em['issue']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($em['request_time'])) ?></td>
                            <td><a href="tel:<?= $em['phone_number'] ?>" class="btn btn-sm btn-success"><i class="fas fa-phone"></i></a></td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6">No emergencies assigned.</td></tr>
                        <?php endif; else: ?>
                            <tr><td colspan="6">Emergency table / column missing.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page == 'history'): ?>
            <h2>Job History</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Booking ID</th><th>Customer</th><th>Service</th><th>Date</th><th>Status</th><th>Proof</th></tr></thead>
                    <tbody>
                        <?php if ($bookings_has_mechanic):
                            $history = $conn->query("SELECT b.id, u.name AS user_name, b.item_name, b.booking_date, b.status
                                                    FROM bookings b JOIN users_details u ON b.user_id = u.id
                                                    WHERE b.mechanic_id = $mechanic_id AND b.status IN ('completed','cancelled')
                                                    ORDER BY b.id DESC");
                            while ($h = $history->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $h['id'] ?></td>
                            <td><?= htmlspecialchars($h['user_name']) ?></td>
                            <td><?= htmlspecialchars($h['item_name']) ?></td>
                            <td><?= $h['booking_date'] ?></td>
                            <td><span class="badge <?= $h['status'] == 'completed' ? 'badge-completed' : 'badge-cancelled' ?>"><?= $h['status'] ?></span></td>
                            <td><a href="#" class="btn btn-sm btn-info"><i class="fas fa-image"></i></a></td>
                        </tr>
                        <?php endwhile; else: ?>
                            <tr><td colspan="6">No history available.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($page == 'earnings'): ?>
            <h2>Earnings Breakdown</h2>
            <div class="row">
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?= number_format($earnings_today) ?></div><div>Today</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?= number_format($earnings_week) ?></div><div>This Week</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?= number_format($earnings_month) ?></div><div>This Month</div></div></div>
            </div>
            <div class="table-container mt-3">
                <h5>Earnings History</h5>
                <table class="table">
                    <tr><th>Date</th><th>Job</th><th>Amount</th></tr>
                    <?php if ($bookings_has_mechanic):
                        $earn = $conn->query("SELECT id, item_name, amount, created_at FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' ORDER BY created_at DESC LIMIT 10");
                        while ($e = $earn->fetch_assoc()): ?>
                    <tr>
                        <td><?= date('Y-m-d', strtotime($e['created_at'])) ?></td>
                        <td><?= htmlspecialchars($e['item_name']) ?> (#<?= $e['id'] ?>)</td>
                        <td>₹<?= number_format($e['amount']) ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="3">No earnings data yet.</td></tr>
                    <?php endif; ?>
                </table>
            </div>

        <?php elseif ($page == 'notifications'): ?>
            <h2>Notifications</h2>
            <div class="list-group">
                <?php if (table_exists($conn, 'notifications')):
                    $notif = $conn->query("SELECT id, message, created_at, is_read FROM notifications ORDER BY created_at DESC LIMIT 20");
                    while ($n = $notif->fetch_assoc()): ?>
                <a href="?page=notifications&read=<?= $n['id'] ?>" class="list-group-item list-group-item-action <?= $n['is_read'] ? '' : 'list-group-item-warning' ?>">
                    <?= htmlspecialchars($n['message']) ?>
                    <small class="text-muted float-end"><?= date('d-m-Y H:i', strtotime($n['created_at'])) ?></small>
                </a>
                <?php endwhile; else: ?>
                    <p>No notifications table yet.</p>
                <?php endif; ?>
            </div>

        <?php elseif ($page == 'profile'): ?>
            <h2>My Profile</h2>
            <form method="post" class="table-container" style="max-width: 500px;">
                <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" value="<?= htmlspecialchars($mech['name']) ?>" required></div>
                <div class="mb-3"><label>Phone</label><input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($mech['phone_number']) ?>"></div>
                <div class="mb-3"><label>Location</label><input type="text" name="location" class="form-control" value="<?= htmlspecialchars($mech['location'] ?? '') ?>"></div>
                <div class="mb-3"><label>New Password (leave blank to keep)</label><input type="password" name="password" class="form-control"></div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateAvailability(status) {
    fetch('service_home.php?action=update_availability&status=' + encodeURIComponent(status))
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); else alert('Failed'); });
}
function updateJobStatus(bid, status) {
    fetch('service_home.php?action=update_job_status&booking_id=' + bid + '&status=' + encodeURIComponent(status))
        .then(r => r.json())
        .then(d => { if (d.success) location.reload(); else alert('Update failed'); });
}
</script>
</body>
</html>