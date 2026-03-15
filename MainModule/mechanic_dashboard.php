<?php
session_start();
require_once '../dbcon.php';

// Check mechanic login
// if(!isset($_SESSION['mechanic_id'])) header('location: login.php');
$mechanic_id = 1; // Example, from session

// Dummy stats
$assigned_jobs = 4;
$completed_today = 2;
$earnings_today = 800;
$earnings_week = 4500;
$earnings_month = 18500;

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
        .badge-ontheway { background: #ffc107; }
        .badge-progress { background: #0d6efd; color: white; }
        .badge-completed { background: #198754; }
        .badge-cancelled { background: #6c757d; }
        .availability-select { width: 150px; display: inline-block; margin-left: 10px; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .notification-icon { position: relative; }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4><i class="fas fa-tools"></i> Mechanic Panel</h4>
            <div class="mt-2">
                <span class="badge bg-success">Available</span>
            </div>
        </div>
        <a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=assigned" class="<?php echo $page=='assigned'?'active':''; ?>"><i class="fas fa-clipboard-list"></i> My Assigned Jobs <span class="badge bg-danger"><?php echo $assigned_jobs; ?></span></a>
        <a href="?page=emergency" class="<?php echo $page=='emergency'?'active':''; ?>"><i class="fas fa-exclamation-triangle"></i> Emergency Jobs</a>
        <a href="?page=history" class="<?php echo $page=='history'?'active':''; ?>"><i class="fas fa-history"></i> Job History</a>
        <a href="?page=earnings" class="<?php echo $page=='earnings'?'active':''; ?>"><i class="fas fa-rupee-sign"></i> Earnings</a>
        <a href="?page=notifications" class="<?php echo $page=='notifications'?'active':''; ?>"><i class="fas fa-bell"></i> Notifications <span class="badge bg-warning">2</span></a>
        <a href="?page=profile" class="<?php echo $page=='profile'?'active':''; ?>"><i class="fas fa-user"></i> My Profile</a>
        <a href="logout.php" class="mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="top-bar">
            <h2>Welcome, Ramesh</h2>
            <div>
                <span>Status: </span>
                <select class="form-select availability-select" onchange="updateAvailability(this.value)">
                    <option value="available" selected>Available</option>
                    <option value="busy">Busy</option>
                    <option value="offline">Offline</option>
                </select>
                <a href="#" class="btn btn-outline-primary notification-icon"><i class="fas fa-bell"></i> <span class="badge bg-danger">2</span></a>
            </div>
        </div>

        <?php if($page == 'dashboard'): ?>
            <div class="row">
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $assigned_jobs; ?></div><div>Assigned Jobs</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $completed_today; ?></div><div>Completed Today</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?php echo $earnings_today; ?></div><div>Today's Earnings</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?php echo $earnings_month; ?></div><div>Monthly Earnings</div></div></div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="table-container">
                        <h5>Recent Assigned Jobs</h5>
                        <table class="table table-sm">
                            <tr><th>Booking</th><th>Customer</th><th>Service</th><th>Status</th></tr>
                            <tr><td>#21</td><td>Ajaya</td><td>Tyre Change</td><td><span class="badge bg-info">Assigned</span></td></tr>
                            <tr><td>#20</td><td>Ajaya</td><td>Battery Jump</td><td><span class="badge bg-warning">On The Way</span></td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="table-container">
                        <h5>Quick Actions</h5>
                        <a href="?page=assigned" class="btn btn-primary w-100 mb-2">View My Jobs</a>
                        <a href="?page=earnings" class="btn btn-success w-100">Check Earnings</a>
                    </div>
                </div>
            </div>

        <?php elseif($page == 'assigned'): ?>
            <h2>My Assigned Jobs</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Booking ID</th><th>Customer</th><th>Service</th><th>Address</th><th>Date/Time</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $jobs = [
                            ['id'=>21, 'customer'=>'Ajaya', 'phone'=>'9348792986', 'service'=>'Tyre Change', 'address'=>'Bhubaneswar', 'time'=>'2025-12-04 7-8 AM', 'status'=>'assigned', 'emergency'=>0],
                            ['id'=>20, 'customer'=>'Ajaya', 'phone'=>'9348792986', 'service'=>'Battery Jump', 'address'=>'Bhubaneswar', 'time'=>'2025-12-04 8-9 AM', 'status'=>'ontheway', 'emergency'=>1],
                            ['id'=>17, 'customer'=>'Bhabani', 'phone'=>'9114725836', 'service'=>'Engine Repair', 'address'=>'BBSR', 'time'=>'2025-12-02 8-9 AM', 'status'=>'inprogress', 'emergency'=>0],
                        ];
                        $status_badge = [
                            'assigned'=>'info',
                            'ontheway'=>'warning',
                            'inprogress'=>'primary',
                            'completed'=>'success',
                            'cancelled'=>'secondary'
                        ];
                        foreach($jobs as $j): ?>
                        <tr>
                            <td>#<?php echo $j['id']; ?> <?php if($j['emergency']) echo '<span class="badge bg-danger">Emergency</span>'; ?></td>
                            <td><?php echo $j['customer']; ?><br><small><?php echo $j['phone']; ?></small></td>
                            <td><?php echo $j['service']; ?></td>
                            <td><?php echo $j['address']; ?> <a href="https://maps.google.com/?q=<?php echo urlencode($j['address']); ?>" target="_blank"><i class="fas fa-map-marker-alt text-primary"></i></a></td>
                            <td><?php echo $j['time']; ?></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateJobStatus(<?php echo $j['id']; ?>, this.value)">
                                    <option value="assigned" <?php if($j['status']=='assigned') echo 'selected'; ?>>Assigned</option>
                                    <option value="ontheway" <?php if($j['status']=='ontheway') echo 'selected'; ?>>On The Way</option>
                                    <option value="inprogress" <?php if($j['status']=='inprogress') echo 'selected'; ?>>In Progress</option>
                                    <option value="completed" <?php if($j['status']=='completed') echo 'selected'; ?>>Completed</option>
                                    <option value="cancelled" <?php if($j['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <a href="tel:<?php echo $j['phone']; ?>" class="btn btn-sm btn-success"><i class="fas fa-phone"></i></a>
                                <a href="job_details.php?id=<?php echo $j['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#uploadProofModal" onclick="setBooking(<?php echo $j['id']; ?>)"><i class="fas fa-camera"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'emergency'): ?>
            <h2>Emergency Jobs</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Customer</th><th>Location</th><th>Issue</th><th>Time</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr><td>#E01</td><td>Ajaya <br>9348792986</td><td>Bhubaneswar</td><td>Engine failure</td><td>10:23 AM</td><td><a href="#" class="btn btn-sm btn-primary">Accept</a> <a href="tel:9348792986" class="btn btn-sm btn-success"><i class="fas fa-phone"></i></a></td></tr>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'history'): ?>
            <h2>Job History</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Booking ID</th><th>Customer</th><th>Service</th><th>Date</th><th>Status</th><th>Proof</th></tr></thead>
                    <tbody>
                        <tr><td>#15</td><td>Bhabani</td><td>Fuel Delivery</td><td>2025-12-01</td><td><span class="badge bg-success">Completed</span></td><td><a href="#" class="btn btn-sm btn-info"><i class="fas fa-image"></i></a></td></tr>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'earnings'): ?>
            <h2>Earnings Breakdown</h2>
            <div class="row">
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?php echo $earnings_today; ?></div><div>Today</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?php echo $earnings_week; ?></div><div>This Week</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?php echo $earnings_month; ?></div><div>This Month</div></div></div>
            </div>
            <div class="table-container mt-3">
                <h5>Earnings History</h5>
                <table class="table">
                    <tr><th>Date</th><th>Job</th><th>Amount</th></tr>
                    <tr><td>2026-03-15</td><td>Tyre Change #21</td><td>₹250</td></tr>
                    <tr><td>2026-03-14</td><td>Battery Jump #20</td><td>₹300</td></tr>
                </table>
            </div>

        <?php elseif($page == 'notifications'): ?>
            <h2>Notifications</h2>
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">New job assigned #22 <small class="text-muted">5 min ago</small></a>
                <a href="#" class="list-group-item list-group-item-action">Payment received for #21 <small class="text-muted">1 hour ago</small></a>
            </div>

        <?php elseif($page == 'profile'): ?>
            <h2>My Profile</h2>
            <form class="table-container" style="max-width: 500px;">
                <div class="mb-3"><label>Name</label><input type="text" class="form-control" value="Ramesh"></div>
                <div class="mb-3"><label>Phone</label><input type="text" class="form-control" value="9876543210"></div>
                <div class="mb-3"><label>Location</label><input type="text" class="form-control" value="Bhubaneswar"></div>
                <div class="mb-3"><label>New Password</label><input type="password" class="form-control"></div>
                <button class="btn btn-primary">Update</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<!-- Modal for uploading proof -->
<div class="modal fade" id="uploadProofModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Upload Work Proof</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form action="upload_proof.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="booking_id" id="modal_booking_id">
            <input type="file" name="proof_image" class="form-control" accept="image/*" required>
            <button type="submit" class="btn btn-primary mt-3">Upload</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateJobStatus(bookingId, status) {
    alert('Update booking '+bookingId+' to '+status); // Replace with AJAX
}
function updateAvailability(status) {
    alert('Mechanic status changed to '+status); // Replace with AJAX
}
function setBooking(id) {
    document.getElementById('modal_booking_id').value = id;
}
</script>
</body>
</html>