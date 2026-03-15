<?php
session_start();
require_once '../dbcon.php'; // adjust path as needed

// Check if admin is logged in (example)
// if(!isset($_SESSION['admin_id'])) header('location: login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Breakdown Assistance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f4f7fc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .wrapper {
            display: flex;
        }
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
        .sidebar i {
            margin-right: 10px;
            width: 20px;
        }
        .content {
            flex: 1;
            padding: 20px 30px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid #0d6efd;
            margin-bottom: 20px;
        }
        .stats-card .number {
            font-size: 2rem;
            font-weight: bold;
        }
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
        .btn-add:hover {
            background: #0b5ed7;
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
        <a href="?page=dashboard" class="<?php echo (!isset($_GET['page']) || $_GET['page']=='dashboard')?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=services" class="<?php echo isset($_GET['page']) && $_GET['page']=='services'?'active':''; ?>"><i class="fas fa-wrench"></i> Manage Services</a>
        <a href="?page=packages" class="<?php echo isset($_GET['page']) && $_GET['page']=='packages'?'active':''; ?>"><i class="fas fa-box"></i> Manage Packages</a>
        <a href="?page=bookings" class="<?php echo isset($_GET['page']) && $_GET['page']=='bookings'?'active':''; ?>"><i class="fas fa-calendar-check"></i> View Bookings</a>
        <a href="logout.php" class="mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
        if($page == 'dashboard') {
            // Dummy stats (replace with actual counts from DB)
            $total_services = 12;
            $total_packages = 5;
            $total_bookings = 21;
        ?>
            <h2>Dashboard</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="number"><?php echo $total_services; ?></div>
                        <div>Total Services</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="number"><?php echo $total_packages; ?></div>
                        <div>Total Packages</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <div class="number"><?php echo $total_bookings; ?></div>
                        <div>Total Bookings</div>
                    </div>
                </div>
            </div>
        <?php
        } elseif($page == 'services') {
            // Dummy services data
            $services = [
                ['id'=>8, 'name'=>'Tyre Change', 'price'=>500, 'discount'=>400],
                ['id'=>7, 'name'=>'Battery Jump Start', 'price'=>300, 'discount'=>250],
                ['id'=>6, 'name'=>'Fuel Delivery', 'price'=>200, 'discount'=>180],
                ['id'=>5, 'name'=>'Towing (upto 10km)', 'price'=>1500, 'discount'=>1200],
                ['id'=>4, 'name'=>'Lockout Assistance', 'price'=>400, 'discount'=>350],
                ['id'=>3, 'name'=>'Engine Repair (on-site)', 'price'=>1000, 'discount'=>850],
                ['id'=>2, 'name'=>'Flat Tyre Repair', 'price'=>300, 'discount'=>250],
                ['id'=>1, 'name'=>'Basic Vehicle Check', 'price'=>600, 'discount'=>500],
            ];
        ?>
            <div class="d-flex justify-content-between align-items-center">
                <h2>Manage Services</h2>
                <a href="add_service.php" class="btn btn-add"><i class="fas fa-plus"></i> Add New Service</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Original Price</th>
                            <th>Discounted Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($services as $s): ?>
                        <tr>
                            <td>#<?php echo $s['id']; ?></td>
                            <td><?php echo $s['name']; ?></td>
                            <td>₹<?php echo number_format($s['price'],2); ?></td>
                            <td>₹<?php echo number_format($s['discount'],2); ?></td>
                            <td>
                                <a href="edit_service.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_service.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php
        } elseif($page == 'packages') {
            // Dummy packages
            $packages = [
                ['id'=>4, 'name'=>'Basic Assistance Pack', 'desc'=>'Tyre + Battery', 'price'=>1200, 'discount'=>900],
                ['id'=>3, 'name'=>'Family Pack', 'desc'=>'2 Services + Towing', 'price'=>2500, 'discount'=>2000],
                ['id'=>2, 'name'=>'Premium Roadside Pack', 'desc'=>'All services', 'price'=>4000, 'discount'=>3200],
                ['id'=>1, 'name'=>'Emergency Pack', 'desc'=>'Fuel + Towing', 'price'=>1800, 'discount'=>1500],
            ];
        ?>
            <div class="d-flex justify-content-between align-items-center">
                <h2>Manage Packages</h2>
                <a href="add_package.php" class="btn btn-add"><i class="fas fa-plus"></i> Add New Package</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Package Name</th>
                            <th>Description</th>
                            <th>Original Price</th>
                            <th>Discounted Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($packages as $p): ?>
                        <tr>
                            <td>#<?php echo $p['id']; ?></td>
                            <td><?php echo $p['name']; ?></td>
                            <td><?php echo $p['desc']; ?></td>
                            <td>₹<?php echo number_format($p['price'],2); ?></td>
                            <td>₹<?php echo number_format($p['discount'],2); ?></td>
                            <td>
                                <a href="edit_package.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="delete_package.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php
        } elseif($page == 'bookings') {
            // Dummy bookings
            $bookings = [
                ['id'=>21, 'name'=>'Ajaya', 'phone'=>'9348792986', 'email'=>'aj@gmail.com', 'item'=>'Tyre Change', 'type'=>'Service', 'date'=>'2025-12-04', 'slot'=>'Morning (7-8 AM)', 'amount'=>250, 'payment'=>'Pending', 'address'=>'Bhubaneswar...'],
                ['id'=>20, 'name'=>'Ajaya', 'phone'=>'9348792986', 'email'=>'aj@gmail.com', 'item'=>'Basic Assistance Pack', 'type'=>'Package', 'date'=>'2025-12-04', 'slot'=>'Morning (7-8 AM)', 'amount'=>900, 'payment'=>'Pending', 'address'=>'Bhubaneswar...'],
                ['id'=>17, 'name'=>'Bhabani Nanda', 'phone'=>'9114725836', 'email'=>'bs@gmail.com', 'item'=>'Battery Jump Start', 'type'=>'Service', 'date'=>'2025-12-02', 'slot'=>'Morning (8-9 AM)', 'amount'=>250, 'payment'=>'Pending', 'address'=>'BBSR...'],
                ['id'=>18, 'name'=>'Bhabani Nanda', 'phone'=>'9114725836', 'email'=>'bs@gmail.com', 'item'=>'Tyre Change', 'type'=>'Service', 'date'=>'2025-12-02', 'slot'=>'Morning (8-9 AM)', 'amount'=>400, 'payment'=>'Pending', 'address'=>'BBSR...'],
                ['id'=>19, 'name'=>'Bhabani Nanda', 'phone'=>'9114725836', 'email'=>'bs@gmail.com', 'item'=>'Engine Repair', 'type'=>'Service', 'date'=>'2025-12-02', 'slot'=>'Morning (8-9 AM)', 'amount'=>850, 'payment'=>'Pending', 'address'=>'BBSR...'],
            ];
        ?>
            <h2>View Bookings</h2>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>User Details</th>
                            <th>Item</th>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bookings as $b): ?>
                        <tr>
                            <td>#<?php echo $b['id']; ?></td>
                            <td>
                                <?php echo $b['name']; ?><br>
                                <small><?php echo $b['phone']; ?><br><?php echo $b['email']; ?></small>
                            </td>
                            <td><?php echo $b['item']; ?></td>
                            <td><?php echo $b['type']; ?></td>
                            <td><?php echo $b['date']; ?><br><small><?php echo $b['slot']; ?></small></td>
                            <td>₹<?php echo number_format($b['amount'],2); ?></td>
                            <td><span class="badge bg-warning"><?php echo $b['payment']; ?></span></td>
                            <td><?php echo $b['address']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>