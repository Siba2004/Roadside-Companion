<?php
session_start();
require_once '../dbcon.php';

// Check admin login (example)
// if(!isset($_SESSION['admin_id'])) header('location: login.php');

// Dummy counts for dashboard (replace with actual queries)
$total_users = 45;
$total_mechanics = 12;
$total_services = 10;
$total_packages = 5;
$total_bookings = 128;
$emergency_requests = 3;
$pending_payments = 8;

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - RoadSide Companion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f4f7fc; font-family: 'Segoe UI', sans-serif; }
        .wrapper { display: flex; }
        .sidebar {
            min-width: 260px;
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
        .btn-add {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .btn-add:hover { background: #0b5ed7; }
        .badge-emergency { background: #dc3545; color: white; }
        .badge-pending { background: #ffc107; color: #000; }
        .badge-paid { background: #198754; color: white; }
        .search-box { max-width: 300px; margin-bottom: 15px; }
        .notification-badge {
            position: relative;
            top: -10px;
            right: 5px;
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h4><i class="fas fa-tools"></i> RoadSide Admin</h4>
        </div>
        <a href="?page=dashboard" class="<?php echo $page=='dashboard'?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=users" class="<?php echo $page=='users'?'active':''; ?>"><i class="fas fa-users"></i> Manage Users</a>
        <a href="?page=mechanics" class="<?php echo $page=='mechanics'?'active':''; ?>"><i class="fas fa-user-cog"></i> Manage Mechanics</a>
        <a href="?page=services" class="<?php echo $page=='services'?'active':''; ?>"><i class="fas fa-wrench"></i> Manage Services</a>
        <a href="?page=packages" class="<?php echo $page=='packages'?'active':''; ?>"><i class="fas fa-box"></i> Manage Packages</a>
        <a href="?page=bookings" class="<?php echo $page=='bookings'?'active':''; ?>"><i class="fas fa-calendar-check"></i> View Bookings</a>
        <a href="?page=emergency" class="<?php echo $page=='emergency'?'active':''; ?>"><i class="fas fa-exclamation-triangle"></i> Emergency Requests <span class="badge bg-danger"><?php echo $emergency_requests; ?></span></a>
        <a href="?page=payments" class="<?php echo $page=='payments'?'active':''; ?>"><i class="fas fa-credit-card"></i> Payments</a>
        <a href="?page=reports" class="<?php echo $page=='reports'?'active':''; ?>"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="?page=notifications" class="<?php echo $page=='notifications'?'active':''; ?>"><i class="fas fa-bell"></i> Notifications <span class="badge bg-warning">3</span></a>
        <a href="?page=profile" class="<?php echo $page=='profile'?'active':''; ?>"><i class="fas fa-user"></i> Admin Profile</a>
        <a href=".././MainModule/logout.php" class="mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php if($page == 'dashboard'): ?>
            <h2>Dashboard</h2>
            <div class="row">
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $total_users; ?></div><div>Total Users</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $total_mechanics; ?></div><div>Mechanics</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $total_services; ?></div><div>Services</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $total_packages; ?></div><div>Packages</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $total_bookings; ?></div><div>Bookings</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?php echo $emergency_requests; ?></div><div>Emergency</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?php echo number_format(15890,2); ?></div><div>Revenue (month)</div></div></div>
            </div>

        <?php elseif($page == 'users'): ?>
            <h2>Manage Users</h2>
            <div class="d-flex justify-content-between">
                <input type="text" class="form-control search-box" placeholder="Search users...">
                <button class="btn btn-add"><i class="fas fa-download"></i> Export</button>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        // Dummy data
                        $users = [
                            ['id'=>1,'name'=>'Ajaya','email'=>'aj@gmail.com','phone'=>'9348792986','status'=>'active'],
                            ['id'=>2,'name'=>'Bhabani','email'=>'bs@gmail.com','phone'=>'9114725836','status'=>'active'],
                            ['id'=>3,'name'=>'Rahul','email'=>'rahul@test.com','phone'=>'9876543210','status'=>'blocked'],
                        ];
                        foreach($users as $u): ?>
                        <tr>
                            <td>#<?php echo $u['id']; ?></td>
                            <td><?php echo $u['name']; ?></td>
                            <td><?php echo $u['email']; ?></td>
                            <td><?php echo $u['phone']; ?></td>
                            <td><span class="badge bg-<?php echo $u['status']=='active'?'success':'secondary'; ?>"><?php echo $u['status']; ?></span></td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-ban"></i> Block</a>
                                <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'mechanics'): ?>
            <h2>Manage Mechanics</h2>
            <div class="d-flex justify-content-between">
                <input type="text" class="form-control search-box" placeholder="Search mechanics...">
                <a href="add_mechanic.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Mechanic</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $mechs = [
                            ['id'=>1,'name'=>'Ramesh','phone'=>'9876543210','location'=>'Bhubaneswar','status'=>'available'],
                            ['id'=>2,'name'=>'Suresh','phone'=>'9876543211','location'=>'Cuttack','status'=>'busy'],
                        ];
                        foreach($mechs as $m): ?>
                        <tr>
                            <td>#<?php echo $m['id']; ?></td>
                            <td><?php echo $m['name']; ?></td>
                            <td><?php echo $m['phone']; ?></td>
                            <td><?php echo $m['location']; ?></td>
                            <td><span class="badge bg-<?php echo $m['status']=='available'?'success':'warning'; ?>"><?php echo $m['status']; ?></span></td>
                            <td>
                                <a href="edit_mechanic.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_mechanic.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'services'): ?>
            <!-- Similar to previous services table, add search/filter -->
            <h2>Manage Services</h2>
            <div class="d-flex justify-content-between">
                <input type="text" class="form-control search-box" placeholder="Search services...">
                <a href="add_service.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Service</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Service Name</th><th>Original Price</th><th>Discounted</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $services = [
                            ['id'=>8,'name'=>'Tyre Change','price'=>500,'discount'=>400],
                            ['id'=>7,'name'=>'Battery Jump','price'=>300,'discount'=>250],
                        ];
                        foreach($services as $s): ?>
                        <tr>
                            <td>#<?php echo $s['id']; ?></td>
                            <td><?php echo $s['name']; ?></td>
                            <td>₹<?php echo $s['price']; ?></td>
                            <td>₹<?php echo $s['discount']; ?></td>
                            <td>
                                <a href="edit_service.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_service.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'packages'): ?>
            <!-- Similar, add search/filter -->
            <h2>Manage Packages</h2>
            <div class="d-flex justify-content-between">
                <input type="text" class="form-control search-box" placeholder="Search packages...">
                <a href="add_package.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Package</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Package Name</th><th>Description</th><th>Price</th><th>Discounted</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $packages = [
                            ['id'=>4,'name'=>'Basic Pack','desc'=>'Tyre + Battery','price'=>1200,'discount'=>900],
                        ];
                        foreach($packages as $p): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td><?php echo $p['name']; ?></td>
                            <td><?php echo $p['desc']; ?></td>
                            <td>₹<?php echo $p['price']; ?></td>
                            <td>₹<?php echo $p['discount']; ?></td>
                            <td><a href="#" class="btn btn-sm btn-primary">Edit</a> <a href="#" class="btn btn-sm btn-danger">Delete</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'bookings'): ?>
            <h2>View Bookings</h2>
            <div class="row mb-3">
                <div class="col-md-3"><input type="date" class="form-control" placeholder="From date"></div>
                <div class="col-md-3"><input type="date" class="form-control" placeholder="To date"></div>
                <div class="col-md-3">
                    <select class="form-select">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Accepted</option>
                        <option>In Progress</option>
                        <option>Completed</option>
                        <option>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by user">
                </div>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr><th>Booking ID</th><th>User</th><th>Item</th><th>Type</th><th>Date/Time</th><th>Amount</th><th>Payment</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php
                        $bookings = [
                            ['id'=>21,'user'=>'Ajaya','phone'=>'9348792986','item'=>'Tyre Change','type'=>'Service','date'=>'2025-12-04','slot'=>'7-8 AM','amount'=>250,'payment'=>'Pending','status'=>'pending'],
                            ['id'=>20,'user'=>'Ajaya','item'=>'Basic Pack','type'=>'Package','date'=>'2025-12-04','slot'=>'7-8 AM','amount'=>900,'payment'=>'Pending','status'=>'pending'],
                        ];
                        foreach($bookings as $b): ?>
                        <tr>
                            <td>#<?php echo $b['id']; ?></td>
                            <td><?php echo $b['user']; ?><br><small><?php echo $b['phone']; ?></small></td>
                            <td><?php echo $b['item']; ?></td>
                            <td><?php echo $b['type']; ?></td>
                            <td><?php echo $b['date']; ?><br><small><?php echo $b['slot']; ?></small></td>
                            <td>₹<?php echo $b['amount']; ?></td>
                            <td><span class="badge bg-warning"><?php echo $b['payment']; ?></span></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateStatus(<?php echo $b['id']; ?>, this.value)">
                                    <option value="pending" <?php echo $b['status']=='pending'?'selected':''; ?>>Pending</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <a href="assign_mechanic.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-user-cog"></i> Assign</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'emergency'): ?>
            <h2>Emergency Requests</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>User</th><th>Location</th><th>Issue</th><th>Time</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <tr>
                            <td>#E01</td>
                            <td>Ajaya <br> 9348792986</td>
                            <td>Bhubaneswar</td>
                            <td>Engine failure</td>
                            <td>2025-03-15 10:23</td>
                            <td><span class="badge bg-danger">Pending</span></td>
                            <td><a href="assign_mechanic.php?emergency=1" class="btn btn-sm btn-warning">Assign Mechanic</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'payments'): ?>
            <h2>Payment Management</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Booking ID</th><th>User</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Status</th></tr></thead>
                    <tbody>
                        <tr><td>#21</td><td>Ajaya</td><td>₹250</td><td>Cash</td><td>N/A</td><td><span class="badge bg-warning">Pending</span></td></tr>
                        <tr><td>#20</td><td>Ajaya</td><td>₹900</td><td>Online</td><td>TXN123456</td><td><span class="badge bg-success">Paid</span></td></tr>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'reports'): ?>
            <h2>Reports & Analytics</h2>
            <div class="row">
                <div class="col-md-4"><div class="stats-card"><canvas id="chart1" height="150"></canvas></div></div>
                <div class="col-md-8">
                    <div class="table-container">
                        <h5>Monthly Revenue</h5>
                        <table class="table">
                            <tr><th>Month</th><th>Bookings</th><th>Revenue</th></tr>
                            <tr><td>Jan 2026</td><td>45</td><td>₹12,500</td></tr>
                            <tr><td>Feb 2026</td><td>52</td><td>₹15,800</td></tr>
                        </table>
                    </div>
                </div>
            </div>

        <?php elseif($page == 'notifications'): ?>
            <h2>Notifications</h2>
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action">New booking #22 from Rahul <small class="text-muted">5 min ago</small></a>
                <a href="#" class="list-group-item list-group-item-action">Payment received for #21 <small class="text-muted">1 hour ago</small></a>
                <a href="#" class="list-group-item list-group-item-action">Emergency request from Bhubaneswar <small class="text-muted">2 hours ago</small></a>
            </div>

        <?php elseif($page == 'profile'): ?>
            <h2>Admin Profile</h2>
            <form class="table-container" style="max-width: 500px;">
                <div class="mb-3"><label>Name</label><input type="text" class="form-control" value="Admin User"></div>
                <div class="mb-3"><label>Email</label><input type="email" class="form-control" value="admin@roadside.com"></div>
                <div class="mb-3"><label>New Password</label><input type="password" class="form-control" placeholder="Leave blank to keep"></div>
                <button class="btn btn-primary">Update</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateStatus(bookingId, status) {
    alert('Update booking '+bookingId+' to '+status); // Replace with AJAX
}
</script>
</body>
</html>