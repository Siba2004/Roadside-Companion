<?php
session_start();
require_once '../dbcon.php';

// ---- AUTHENTICATION ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'service-provider') {
    header('location: ../login.php');
    exit();
}

$mechanic_id = $_SESSION['id'];

// ---- ERROR DISPLAY ----
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ---- DATABASE CHECK ----
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . ($conn->connect_error ?? "Connection not established"));
}

// ---- ACCEPT REQUEST (AJAX) ----
if (isset($_GET['action']) && $_GET['action'] === 'accept_request' && isset($_GET['booking_id'])) {
    header('Content-Type: application/json');
    $booking_id = (int)$_GET['booking_id'];
    
    // Check if booking exists and is not assigned to any mechanic
    $check = $conn->query("SELECT id, status, mechanic_id, user_id FROM bookings WHERE id = $booking_id");
    if ($check && $check->num_rows > 0) {
        $booking = $check->fetch_assoc();
        
        // Only accept if mechanic_id is NULL or 0, and status is pending
        if (($booking['status'] == 'pending') && (empty($booking['mechanic_id']) || $booking['mechanic_id'] == 0)) {
            $stmt = $conn->prepare("UPDATE bookings SET mechanic_id = ?, status = 'accepted' WHERE id = ?");
            $stmt->bind_param("ii", $mechanic_id, $booking_id);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();
            
            echo json_encode(['success' => $success, 'message' => $success ? 'Request accepted successfully' : 'Failed to accept']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Request already assigned to another mechanic']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
    }
    exit;
}

// ---- REJECT REQUEST (AJAX) ----
if (isset($_GET['action']) && $_GET['action'] === 'reject_request' && isset($_GET['booking_id'])) {
    header('Content-Type: application/json');
    $booking_id = (int)$_GET['booking_id'];
    
    // For reject, we just keep it pending but mark that this mechanic rejected it
    // You might want to add a rejected_mechanics field to track who rejected
    $stmt = $conn->prepare("UPDATE bookings SET status = 'pending' WHERE id = ? AND mechanic_id IS NULL");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $success = $stmt->affected_rows > 0;
    $stmt->close();
    
    echo json_encode(['success' => $success, 'message' => $success ? 'Request rejected' : 'Failed to reject']);
    exit;
}

// ---- CHANGE OWN STATUS (AJAX) ----
if (isset($_GET['action']) && $_GET['action'] === 'update_availability') {
    header('Content-Type: application/json');
    $new_status = $_GET['status'] ?? '';
    $allowed = ['available', 'busy', 'offline'];
    if (in_array($new_status, $allowed)) {
        $stmt = $conn->prepare("UPDATE users_details SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $mechanic_id);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

// ---- UPDATE JOB STATUS (AJAX) ----
if (isset($_GET['action']) && $_GET['action'] === 'update_job_status' && isset($_GET['booking_id'], $_GET['status'])) {
    header('Content-Type: application/json');
    $booking_id = (int)$_GET['booking_id'];
    $new_status = $_GET['status'];
    $allowed = ['accepted', 'in_progress', 'completed', 'cancelled'];
    
    if (in_array($new_status, $allowed)) {
        // First check if this mechanic owns this booking
        $check = $conn->query("SELECT id FROM bookings WHERE id = $booking_id AND mechanic_id = $mechanic_id");
        if ($check && $check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND mechanic_id = ?");
            $stmt->bind_param("sii", $new_status, $booking_id, $mechanic_id);
            $stmt->execute();
            $success = $stmt->affected_rows > 0;
            $stmt->close();
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
        }
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

// ---- FETCH MECHANIC DATA ----
$mech_result = $conn->query("SELECT * FROM users_details WHERE id = $mechanic_id");
if (!$mech_result || $mech_result->num_rows == 0) {
    die("Mechanic not found");
}
$mech = $mech_result->fetch_assoc();

// ---- FETCH REQUESTS (WHERE mechanic_id IS NULL) ----
$pending_requests = 0;
$requests_list = [];

// IMPORTANT: Look for bookings with mechanic_id IS NULL or 0
$query = "SELECT b.*, u.name AS user_name, u.phone_number, u.location AS user_address
          FROM bookings b 
          JOIN users_details u ON b.user_id = u.id
          WHERE (b.mechanic_id IS NULL OR b.mechanic_id = 0)
          AND b.status = 'pending'
          ORDER BY b.id DESC";

$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $requests_list[] = $row;
    }
    $pending_requests = count($requests_list);
}

// ---- ASSIGNED JOBS (mechanic_id matches and status is active) ----
$assigned_jobs = 0;
$assigned_list = [];

$query_assigned = "SELECT b.*, u.name AS user_name, u.phone_number, u.location AS user_address
                   FROM bookings b 
                   JOIN users_details u ON b.user_id = u.id
                   WHERE b.mechanic_id = $mechanic_id 
                   AND b.status IN ('accepted', 'in_progress')
                   ORDER BY b.id DESC";

$result_assigned = $conn->query($query_assigned);
if ($result_assigned) {
    while ($row = $result_assigned->fetch_assoc()) {
        $assigned_list[] = $row;
    }
    $assigned_jobs = count($assigned_list);
}

// ---- JOB HISTORY (completed or cancelled) ----
$history_list = [];
$query_history = "SELECT b.*, u.name AS user_name, u.phone_number
                  FROM bookings b 
                  JOIN users_details u ON b.user_id = u.id
                  WHERE b.mechanic_id = $mechanic_id 
                  AND b.status IN ('completed', 'cancelled')
                  ORDER BY b.id DESC LIMIT 50";

$result_history = $conn->query($query_history);
if ($result_history) {
    while ($row = $result_history->fetch_assoc()) {
        $history_list[] = $row;
    }
}

// ---- COMPLETED TODAY ----
$completed_today = 0;
$result = $conn->query("SELECT COUNT(*) AS cnt FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND DATE(created_at) = CURDATE()");
if ($result) $completed_today = $result->fetch_object()->cnt;

// ---- EARNINGS ----
$earnings_today = 0;
$earnings_week = 0;
$earnings_month = 0;

$today_result = $conn->query("SELECT COALESCE(SUM(amount),0) AS rev FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND DATE(created_at) = CURDATE()");
if ($today_result) $earnings_today = $today_result->fetch_object()->rev;

$week_result = $conn->query("SELECT COALESCE(SUM(amount),0) AS rev FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
if ($week_result) $earnings_week = $week_result->fetch_object()->rev;

$month_result = $conn->query("SELECT COALESCE(SUM(amount),0) AS rev FROM bookings WHERE mechanic_id = $mechanic_id AND status = 'completed' AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
if ($month_result) $earnings_month = $month_result->fetch_object()->rev;

$current_status = $mech['status'] ?? 'available';
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
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
        }
        .sidebar a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: rgba(255,255,255,0.1);
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
        .stats-card.pending { border-left-color: #ffc107; }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .availability-select { width: 150px; display: inline-block; margin-left: 10px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-accept { background: #198754; color: white; }
        .btn-accept:hover { background: #157347; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-reject:hover { background: #bb2d3b; }
        .toast-notification { position: fixed; bottom: 20px; right: 20px; z-index: 9999; }
        .request-card { border-left: 4px solid #ffc107; margin-bottom: 10px; }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-accepted { background: #0d6efd; }
        .badge-progress { background: #0dcaf0; }
        .badge-completed { background: #198754; }
        .badge-cancelled { background: #dc3545; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4><i class="fas fa-tools"></i> Mechanic Panel</h4>
            <div class="mt-2">
                <span class="badge bg-<?= $current_status == 'available' ? 'success' : ($current_status == 'busy' ? 'warning' : 'secondary') ?>">
                    <?= ucfirst($current_status) ?>
                </span>
            </div>
        </div>
        <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=requests" class="<?= $page=='requests'?'active':'' ?>">
            <i class="fas fa-bell"></i> New Requests 
            <?php if($pending_requests > 0): ?>
                <span class="badge bg-danger"><?= $pending_requests ?></span>
            <?php endif; ?>
        </a>
        <a href="?page=assigned" class="<?= $page=='assigned'?'active':'' ?>">
            <i class="fas fa-clipboard-list"></i> My Assigned Jobs 
            <?php if($assigned_jobs > 0): ?>
                <span class="badge bg-info"><?= $assigned_jobs ?></span>
            <?php endif; ?>
        </a>
        <a href="?page=history" class="<?= $page=='history'?'active':'' ?>"><i class="fas fa-history"></i> Job History</a>
        <a href="?page=earnings" class="<?= $page=='earnings'?'active':'' ?>"><i class="fas fa-rupee-sign"></i> Earnings</a>
        <a href="?page=profile" class="<?= $page=='profile'?'active':'' ?>"><i class="fas fa-user"></i> My Profile</a>
        <a href="../MainModule/logout.php" class="mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
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
                <div class="col-md-3">
                    <div class="stats-card pending">
                        <div class="number"><?= $pending_requests ?></div>
                        <div>Pending Requests</div>
                        <small><a href="?page=requests" class="text-decoration-none">View all →</a></small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="number"><?= $assigned_jobs ?></div>
                        <div>Active Jobs</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="number"><?= $completed_today ?></div>
                        <div>Completed Today</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card">
                        <div class="number">₹<?= number_format($earnings_month) ?></div>
                        <div>Monthly Earnings</div>
                    </div>
                </div>
            </div>

            <?php if($pending_requests > 0): ?>
                <div class="alert alert-warning mt-3">
                    <strong><i class="fas fa-bell"></i> New Requests!</strong> You have <?= $pending_requests ?> pending service request(s). 
                    <a href="?page=requests" class="alert-link">Click here to view and respond</a>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> No pending requests. 
                    When customers create bookings without selecting a mechanic, they will appear here.
                </div>
            <?php endif; ?>

        <?php elseif ($page == 'requests'): ?>
            <h2><i class="fas fa-bell"></i> New Service Requests</h2>
            <p class="text-muted">Requests from customers waiting for a mechanic to accept.</p>

            <?php if(count($requests_list) > 0): ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Date & Time</th>
                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($requests_list as $req): ?>
                                <tr data-id="<?= $req['id'] ?>">
                                    <td><strong>#<?= $req['id'] ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($req['user_name']) ?><br>
                                        <small class="text-muted"><i class="fas fa-phone"></i> <?= htmlspecialchars($req['phone_number']) ?></small>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($req['item_name'] ?? 'N/A') ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($req['item_type'] ?? 'Service') ?></small>
                                    </td>
                                    <td>
                                        <?= date('d-m-Y', strtotime($req['booking_date'])) ?><br>
                                        <small><?= $req['time_slot'] ?? 'Flexible' ?></small>
                                    </td>
                                    <td><strong>₹<?= number_format($req['amount'] ?? 0) ?></strong></td>
                                    <td>
                                        <button class="btn btn-sm btn-accept me-1" onclick="acceptRequest(<?= $req['id'] ?>)">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                        <button class="btn btn-sm btn-reject" onclick="rejectRequest(<?= $req['id'] ?>)">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </td>
                                 </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No pending requests found.
                </div>
            <?php endif; ?>

        <?php elseif ($page == 'assigned'): ?>
            <h2><i class="fas fa-clipboard-list"></i> My Assigned Jobs</h2>
            
            <?php if(count($assigned_list) > 0): ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($assigned_list as $job): ?>
                                <tr>
                                    <td><strong>#<?= $job['id'] ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($job['user_name']) ?><br>
                                        <small class="text-muted"><?= $job['phone_number'] ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($job['item_name'] ?? 'N/A') ?></td>
                                    <td><?= date('d-m-Y', strtotime($job['booking_date'])) ?></td>
                                    <td><strong>₹<?= number_format($job['amount'] ?? 0) ?></strong></td>
                                    <td>
                                        <select class="form-select form-select-sm" style="width: 130px;" onchange="updateJobStatus(<?= $job['id'] ?>, this.value)">
                                            <option value="accepted" <?= $job['status'] == 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                            <option value="in_progress" <?= $job['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= $job['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $job['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <a href="tel:<?= $job['phone_number'] ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-phone"></i> Call
                                        </a>
                                    </td>
                                 </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No assigned jobs yet.</div>
            <?php endif; ?>

        <?php elseif ($page == 'history'): ?>
            <h2><i class="fas fa-history"></i> Job History</h2>
            
            <?php if(count($history_list) > 0): ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Service</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($history_list as $job): ?>
                                <tr>
                                    <td><strong>#<?= $job['id'] ?></strong></td>
                                    <td><?= htmlspecialchars($job['user_name']) ?></td>
                                    <td><?= htmlspecialchars($job['item_name'] ?? 'N/A') ?></td>
                                    <td><?= date('d-m-Y', strtotime($job['booking_date'])) ?></td>
                                    <td>₹<?= number_format($job['amount'] ?? 0) ?></td>
                                    <td>
                                        <span class="badge <?= $job['status'] == 'completed' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ucfirst($job['status']) ?>
                                        </span>
                                    </td>
                                 </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No job history yet.</div>
            <?php endif; ?>

        <?php elseif ($page == 'earnings'): ?>
            <h2><i class="fas fa-rupee-sign"></i> Earnings Breakdown</h2>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <div class="number text-success">₹<?= number_format($earnings_today) ?></div>
                        <div>Today's Earnings</div>
                        <small class="text-muted">From <?= $completed_today ?> jobs</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <div class="number text-primary">₹<?= number_format($earnings_week) ?></div>
                        <div>This Week</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card text-center">
                        <div class="number">₹<?= number_format($earnings_month) ?></div>
                        <div>This Month</div>
                    </div>
                </div>
            </div>

        <?php elseif ($page == 'profile'): ?>
            <h2><i class="fas fa-user"></i> My Profile</h2>
            <form method="post" class="table-container" style="max-width: 500px;">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($mech['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($mech['phone_number'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($mech['location'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="toast-notification" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function showToast(message, type) {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    container.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    setTimeout(() => toast.remove(), 3000);
}

function updateAvailability(status) {
    fetch('?action=update_availability&status=' + encodeURIComponent(status))
        .then(r => r.json())
        .then(d => { 
            if(d.success) {
                showToast('Status updated successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Failed to update status', 'danger');
            }
        });
}

function acceptRequest(id) {
    if(!confirm('Accept this service request?')) return;
    fetch(`?action=accept_request&booking_id=${id}`)
        .then(r => r.json())
        .then(d => { 
            showToast(d.message, d.success ? 'success' : 'danger'); 
            if(d.success) setTimeout(() => location.reload(), 1000);
        });
}

function rejectRequest(id) {
    if(!confirm('Reject this request?')) return;
    fetch(`?action=reject_request&booking_id=${id}`)
        .then(r => r.json())
        .then(d => { 
            showToast(d.message, d.success ? 'success' : 'danger'); 
        });
}

function updateJobStatus(id, status) {
    fetch(`?action=update_job_status&booking_id=${id}&status=${status}`)
        .then(r => r.json())
        .then(d => { 
            if(d.success) {
                showToast('Job status updated!', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast('Update failed', 'danger');
            }
        });
}
</script>
</body>
</html>