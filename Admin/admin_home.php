<?php
session_start();
require_once '../dbcon.php';

// ---- AUTHENTICATION ----
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit();
}

// ---- DATABASE CHECK ----
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

/**
 * Helper function: check if a table exists in the database
 */
function table_exists($conn, $table) {
    static $cache = [];
    if (isset($cache[$table])) return $cache[$table];
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    $exists = ($result && $result->num_rows > 0);
    $cache[$table] = $exists;
    return $exists;
}

// ---- HANDLE AJAX & ACTIONS ----

// AJAX: update booking status (only if bookings table exists)
if (isset($_GET['action']) && $_GET['action'] === 'update_booking_status' &&
    isset($_GET['booking_id'], $_GET['status']) && table_exists($conn, 'bookings')) {
    $allowed_statuses = ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'];
    $booking_id = (int)$_GET['booking_id'];
    $status     = $_GET['status'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status.']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $booking_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
    exit;
}

// Delete operations – only run if the target table exists
if (isset($_GET['delete'])) {
    $table = $_GET['delete']; // users, services, four_wheeler_services, two_wheeler_services, packages, bookings
    $id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $page  = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

    // Map to actual table names
    $actual_table = '';
    if ($table === 'users') $actual_table = 'users_details';
    elseif ($table === 'services') $actual_table = 'services';
    elseif ($table === 'four_wheeler_services') $actual_table = 'four_wheeler_services';
    elseif ($table === 'two_wheeler_services') $actual_table = 'two_wheeler_services';
    elseif ($table === 'packages') $actual_table = 'packages';
    elseif ($table === 'bookings') $actual_table = 'bookings';
    else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Invalid delete request.'];
        header("location: ?page=$page");
        exit;
    }

    if ($id <= 0 || !table_exists($conn, $actual_table)) {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Table not found.'];
        header("location: ?page=$page");
        exit;
    }

    // Delete image file if exists for service tables
    if (in_array($actual_table, ['four_wheeler_services', 'two_wheeler_services'])) {
        $img_query = $conn->query("SELECT image_path FROM $actual_table WHERE id = $id");
        if ($img_query && $img = $img_query->fetch_assoc()) {
            if ($img['image_path'] && file_exists("../" . $img['image_path'])) {
                unlink("../" . $img['image_path']);
            }
        }
    }

    $stmt = $conn->prepare("DELETE FROM $actual_table WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Record deleted successfully.'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Deletion failed: ' . $conn->error];
    }
    $stmt->close();
    header("location: ?page=$page");
    exit;
}

// Toggle user block (users_details always exists)
if (isset($_GET['toggle_user']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM users_details WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $new_status = ($row['status'] === 'active') ? 'blocked' : 'active';
        $conn->query("UPDATE users_details SET status = '$new_status' WHERE id = $id");
        $_SESSION['message'] = ['type' => 'success', 'text' => "User status changed to $new_status."];
    }
    header("location: ?page=users");
    exit;
}

// Toggle mechanic status (mechanics are in users_details)
if (isset($_GET['toggle_mechanic']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $res = $conn->query("SELECT status FROM users_details WHERE id = $id AND accounttype='service-provider'");
    if ($res && $row = $res->fetch_assoc()) {
        $new_status = ($row['status'] === 'available') ? 'busy' : 'available';
        $conn->query("UPDATE users_details SET status = '$new_status' WHERE id = $id");
        $_SESSION['message'] = ['type' => 'success', 'text' => "Mechanic status changed to $new_status."];
    }
    header("location: ?page=mechanics");
    exit;
}

// Toggle service status (for four_wheeler_services and two_wheeler_services)
if (isset($_GET['toggle_service_status']) && isset($_GET['id']) && isset($_GET['type'])) {
    $id = (int)$_GET['id'];
    $service_type = $_GET['type'];
    $table = $service_type == 'two_wheeler' ? 'two_wheeler_services' : 'four_wheeler_services';
    
    if (table_exists($conn, $table)) {
        $res = $conn->query("SELECT status FROM $table WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            $new_status = ($row['status'] === 'active') ? 'inactive' : 'active';
            $conn->query("UPDATE $table SET status = '$new_status' WHERE id = $id");
            $_SESSION['message'] = ['type' => 'success', 'text' => "Service status changed to $new_status."];
        }
    }
    header("location: ?page={$service_type}_services");
    exit;
}

// Export users CSV
if (isset($_GET['page']) && $_GET['page'] === 'users' && isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=users_export.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Status']);
    $result = $conn->query("SELECT id, name, email, phone_number, status FROM users_details WHERE accounttype='customer' ORDER BY id");
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['name'], $row['email'], $row['phone_number'], $row['status']]);
    }
    fclose($output);
    exit;
}

// Mark notification as read (if table exists)
if (isset($_GET['read_notification']) && isset($_GET['id']) && table_exists($conn, 'notifications')) {
    $id = (int)$_GET['id'];
    $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id");
    header("location: ?page=notifications");
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

// ---- DETERMINE PAGE ----
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// ---- DASHBOARD COUNTS (safe, with table existence checks) ----
if ($page === 'dashboard') {
    $total_users     = $conn->query("SELECT COUNT(*) as cnt FROM users_details WHERE accounttype='customer'")->fetch_object()->cnt;
    $total_mechanics = $conn->query("SELECT COUNT(*) as cnt FROM users_details WHERE accounttype='service-provider'")->fetch_object()->cnt;

    $total_services  = table_exists($conn, 'services')  ? $conn->query("SELECT COUNT(*) as cnt FROM services")->fetch_object()->cnt : 0;
    $total_four_wheeler_services = table_exists($conn, 'four_wheeler_services') ? $conn->query("SELECT COUNT(*) as cnt FROM four_wheeler_services")->fetch_object()->cnt : 0;
    $total_two_wheeler_services = table_exists($conn, 'two_wheeler_services') ? $conn->query("SELECT COUNT(*) as cnt FROM two_wheeler_services")->fetch_object()->cnt : 0;
    $total_packages  = table_exists($conn, 'packages')  ? $conn->query("SELECT COUNT(*) as cnt FROM packages")->fetch_object()->cnt : 0;
    $total_bookings  = table_exists($conn, 'bookings')  ? $conn->query("SELECT COUNT(*) as cnt FROM bookings")->fetch_object()->cnt : 0;
    $emergency_req   = table_exists($conn, 'emergency_requests') ? $conn->query("SELECT COUNT(*) as cnt FROM emergency_requests WHERE status = 'pending'")->fetch_object()->cnt : 0;
    $month_rev       = table_exists($conn, 'bookings')  ? $conn->query("SELECT COALESCE(SUM(amount),0) as rev FROM bookings WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())")->fetch_object()->rev : 0;
} else {
    $total_users = $total_mechanics = $total_services = $total_four_wheeler_services = $total_two_wheeler_services = $total_packages = $total_bookings = $emergency_req = 0;
    $month_rev = 0;
}
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
        /* YOUR EXACT STYLES, kept identical */
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
            transition: transform 0.3s;
        }
        .stats-card:hover { transform: translateY(-5px); }
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
        .service-image-preview {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }
        .btn-group-sm {
            gap: 5px;
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
        <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="?page=users" class="<?= $page=='users'?'active':'' ?>"><i class="fas fa-users"></i> Manage Users</a>
        <a href="?page=mechanics" class="<?= $page=='mechanics'?'active':'' ?>"><i class="fas fa-user-cog"></i> Manage Mechanics</a>
        
        <!-- Services dropdown style -->
        <div style="padding-left: 20px; margin-top: 5px;">
            <div style="color: rgba(255,255,255,0.6); font-size: 0.8rem; padding: 8px 0;">SERVICES</div>
            <a href="?page=four_wheeler_services" class="<?= $page=='four_wheeler_services'?'active':'' ?>" style="padding-left: 30px;">
                <i class="fas fa-car"></i> Four Wheeler Services
            </a>
            <a href="?page=two_wheeler_services" class="<?= $page=='two_wheeler_services'?'active':'' ?>" style="padding-left: 30px;">
                <i class="fas fa-motorcycle"></i> Two Wheeler Services
            </a>
        </div>
        
        <a href="?page=bookings" class="<?= $page=='bookings'?'active':'' ?>"><i class="fas fa-calendar-check"></i> View Bookings</a>
        <a href="?page=payments" class="<?= $page=='payments'?'active':'' ?>"><i class="fas fa-credit-card"></i> Payments</a>
        <a href="?page=reports" class="<?= $page=='reports'?'active':'' ?>"><i class="fas fa-chart-bar"></i> Reports</a>
        <a href="?page=notifications" class="<?= $page=='notifications'?'active':'' ?>"><i class="fas fa-bell"></i> Notifications <span class="badge bg-warning"><?= table_exists($conn, 'notifications') ? $conn->query("SELECT COUNT(*) as cnt FROM notifications WHERE is_read=0")->fetch_object()->cnt : 0 ?></span></a>
        <a href="?page=profile" class="<?= $page=='profile'?'active':'' ?>"><i class="fas fa-user"></i> Admin Profile</a>
        <a href="../MainModule/logout.php" class="mt-5"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?= $message ?>

        <?php if($page == 'dashboard'): ?>
            <h2>Dashboard</h2>
            <div class="row">
                <div class="col-md-3"><div class="stats-card"><div class="number"><?= $total_users ?></div><div>Total Users</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?= $total_mechanics ?></div><div>Mechanics</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?= $total_services + $total_four_wheeler_services + $total_two_wheeler_services ?></div><div>All Services</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number"><?= $total_bookings ?></div><div>Bookings</div></div></div>
                <div class="col-md-3"><div class="stats-card"><div class="number">₹<?= number_format($month_rev,2) ?></div><div>Revenue (month)</div></div></div>
            </div>

        <?php elseif($page == 'users'): ?>
            <h2>Manage Users</h2>
            <div class="d-flex justify-content-between">
                <form method="get" class="d-flex">
                    <input type="hidden" name="page" value="users">
                    <input type="text" name="search" class="form-control search-box" placeholder="Search users..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                </form>
                <a href="?page=users&export=1" class="btn btn-add"><i class="fas fa-download"></i> Export</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? '%'.$conn->real_escape_string($_GET['search']).'%' : '%%';
                        $stmt = $conn->prepare("SELECT id, name, email, phone_number, status FROM users_details WHERE accounttype='customer' AND (name LIKE ? OR email LIKE ? OR phone_number LIKE ?) ORDER BY id");
                        $stmt->bind_param("sss", $search, $search, $search);
                        $stmt->execute();
                        $users = $stmt->get_result();
                        while ($u = $users->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone_number']) ?></td>
                            <td><span class="badge bg-<?= $u['status']=='active'?'success':'secondary' ?>"><?= $u['status'] ?></span></td>
                            <td>
                                <a href="?page=users&toggle_user=1&id=<?= $u['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-<?= $u['status']=='active'?'ban':'check' ?>"></i> <?= $u['status']=='active'?'Block':'Unblock' ?>
                                </a>
                                <a href="?page=users&delete=users&id=<?= $u['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'mechanics'): ?>
            <h2>Manage Mechanics</h2>
            <div class="d-flex justify-content-between">
                <form method="get">
                    <input type="hidden" name="page" value="mechanics">
                    <input type="text" name="search" class="form-control search-box" placeholder="Search mechanics..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </form>
                <a href="add_mechanic.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Mechanic</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Name</th><th>Phone</th><th>Location</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? '%'.$conn->real_escape_string($_GET['search']).'%' : '%%';
                        $stmt = $conn->prepare("SELECT id, name, phone_number, location, status FROM users_details WHERE accounttype='service-provider' AND (name LIKE ? OR phone_number LIKE ?) ORDER BY id");
                        $stmt->bind_param("ss", $search, $search);
                        $stmt->execute();
                        $mechs = $stmt->get_result();
                        while ($m = $mechs->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $m['id'] ?></td>
                            <td><?= htmlspecialchars($m['name']) ?></td>
                            <td><?= htmlspecialchars($m['phone_number']) ?></td>
                            <td><?= htmlspecialchars($m['location'] ?? 'N/A') ?></td>
                            <td><span class="badge bg-<?= $m['status']=='available'?'success':'warning' ?>"><?= $m['status'] ?></span></td>
                            <td>
                                <a href="edit_mechanic.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="?page=mechanics&toggle_mechanic=1&id=<?= $m['id'] ?>" class="btn btn-sm btn-info">Toggle Status</a>
                                <a href="?page=mechanics&delete=users&id=<?= $m['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>

        <?php elseif($page == 'services'): ?>
            <h2>Manage Services (Basic)</h2>
            <?php if (table_exists($conn, 'services')): ?>
            <div class="d-flex justify-content-between">
                <form method="get">
                    <input type="hidden" name="page" value="services">
                    <input type="text" name="search" class="form-control search-box" placeholder="Search services..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </form>
                <a href="add_service.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Service</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Service Name</th><th>Original Price</th><th>Discounted</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? '%'.$conn->real_escape_string($_GET['search']).'%' : '%%';
                        $stmt = $conn->prepare("SELECT id, service_name, price, discount_price FROM services WHERE service_name LIKE ?");
                        $stmt->bind_param("s", $search);
                        $stmt->execute();
                        $services = $stmt->get_result();
                        while ($s = $services->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $s['id'] ?></td>
                            <td><?= htmlspecialchars($s['service_name']) ?></td>
                            <td>₹<?= $s['price'] ?></td>
                            <td>₹<?= $s['discount_price'] ?></td>
                            <td>
                                <a href="edit_service.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                <a href="?page=services&delete=services&id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Services table not found in database.</div>
            <?php endif; ?>

        <?php elseif($page == 'four_wheeler_services'): ?>
            <h2><i class="fas fa-car"></i> Four Wheeler Services</h2>
            <?php if (table_exists($conn, 'four_wheeler_services')): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form method="get" class="d-flex">
                    <input type="hidden" name="page" value="four_wheeler_services">
                    <input type="text" name="search" class="form-control search-box" placeholder="Search services..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                </form>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addFourWheelerModal">
                    <i class="fas fa-plus"></i> Add Four Wheeler Service
                </button>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Service Name</th>
                            <th>Original Price</th>
                            <th>Offer Price</th>
                            <th>Discount</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? '%'.$conn->real_escape_string($_GET['search']).'%' : '%%';
                        $stmt = $conn->prepare("SELECT * FROM four_wheeler_services WHERE service_name LIKE ? ORDER BY display_order ASC, id ASC");
                        $stmt->bind_param("s", $search);
                        $stmt->execute();
                        $services = $stmt->get_result();
                        while ($s = $services->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $s['id'] ?></td>
                            <td>
                                <?php if ($s['image_path'] && file_exists("../" . $s['image_path'])): ?>
                                    <img src="../<?= htmlspecialchars($s['image_path']) ?>" class="service-image-preview" alt="">
                                <?php else: ?>
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($s['service_name']) ?></td>
                            <td>₹<?= number_format($s['original_price'], 2) ?></td>
                            <td>₹<?= number_format($s['offer_price'], 2) ?></td>
                            <td><?= $s['discount_percent'] ?>%</td>
                            <td><?= number_format($s['rating'], 1) ?> <i class="fas fa-star text-warning"></i></td>
                            <td>
                                <span class="badge bg-<?= $s['status']=='active'?'success':'secondary' ?>">
                                    <?= $s['status'] ?>
                                </span>
                            </td>
                            <td class="btn-group-sm">
                                <button class="btn btn-sm btn-primary" onclick="editFourWheeler(<?= $s['id'] ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="?page=four_wheeler_services&toggle_service_status=1&type=four_wheeler&id=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-<?= $s['status']=='active'?'ban':'check' ?>"></i> Toggle
                                </a>
                                <a href="?page=four_wheeler_services&delete=four_wheeler_services&id=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Add Four Wheeler Service Modal -->
            <div class="modal fade" id="addFourWheelerModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Four Wheeler Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="add_four_wheeler_service.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Service Name *</label>
                                    <input type="text" name="service_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description *</label>
                                    <textarea name="description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Original Price (₹) *</label>
                                        <input type="number" name="original_price" class="form-control" step="0.01" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Offer Price (₹) *</label>
                                        <input type="number" name="offer_price" class="form-control" step="0.01" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Discount Percent (%)</label>
                                        <input type="number" name="discount_percent" class="form-control" step="1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Rating (1-5)</label>
                                        <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" value="4.5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Badge Text (Optional)</label>
                                    <input type="text" name="badge_text" class="form-control" placeholder="e.g., Popular, Best Seller">
                                </div>
                                <div class="mb-3">
                                    <label>Service Image</label>
                                    <input type="file" name="service_image" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label>Display Order</label>
                                    <input type="number" name="display_order" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Service</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Edit Four Wheeler Service Modal -->
            <div class="modal fade" id="editFourWheelerModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Four Wheeler Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="edit_four_wheeler_service.php">
                            <div class="modal-body">
                                <input type="hidden" name="service_id" id="edit_fw_id">
                                <div class="mb-3">
                                    <label>Service Name *</label>
                                    <input type="text" name="service_name" id="edit_fw_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description *</label>
                                    <textarea name="description" id="edit_fw_description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Original Price (₹) *</label>
                                        <input type="number" name="original_price" id="edit_fw_original" class="form-control" step="0.01" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Offer Price (₹) *</label>
                                        <input type="number" name="offer_price" id="edit_fw_offer" class="form-control" step="0.01" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Discount Percent (%)</label>
                                        <input type="number" name="discount_percent" id="edit_fw_discount" class="form-control" step="1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Rating (1-5)</label>
                                        <input type="number" name="rating" id="edit_fw_rating" class="form-control" step="0.1" min="0" max="5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Badge Text (Optional)</label>
                                    <input type="text" name="badge_text" id="edit_fw_badge" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Change Image (Optional)</label>
                                    <input type="file" name="service_image" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label>Display Order</label>
                                    <input type="number" name="display_order" id="edit_fw_order" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Service</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Four wheeler services table not found. Please run the database setup.</div>
            <?php endif; ?>

        <?php elseif($page == 'two_wheeler_services'): ?>
            <h2><i class="fas fa-motorcycle"></i> Two Wheeler Services</h2>
            <?php if (table_exists($conn, 'two_wheeler_services')): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form method="get" class="d-flex">
                    <input type="hidden" name="page" value="two_wheeler_services">
                    <input type="text" name="search" class="form-control search-box" placeholder="Search services..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                    <button class="btn btn-primary ms-2"><i class="fas fa-search"></i></button>
                </form>
                <button class="btn btn-add" data-bs-toggle="modal" data-bs-target="#addTwoWheelerModal">
                    <i class="fas fa-plus"></i> Add Two Wheeler Service
                </button>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Service Name</th>
                            <th>Original Price</th>
                            <th>Offer Price</th>
                            <th>Discount</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? '%'.$conn->real_escape_string($_GET['search']).'%' : '%%';
                        $stmt = $conn->prepare("SELECT * FROM two_wheeler_services WHERE service_name LIKE ? ORDER BY display_order ASC, id ASC");
                        $stmt->bind_param("s", $search);
                        $stmt->execute();
                        $services = $stmt->get_result();
                        while ($s = $services->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $s['id'] ?></td>
                            <td>
                                <?php if ($s['image_path'] && file_exists("../" . $s['image_path'])): ?>
                                    <img src="../<?= htmlspecialchars($s['image_path']) ?>" class="service-image-preview" alt="">
                                <?php else: ?>
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                <?php endif; ?>
                             </td>
                            <td><?= htmlspecialchars($s['service_name']) ?></td>
                            <td>₹<?= number_format($s['original_price'], 2) ?></td>
                            <td>₹<?= number_format($s['offer_price'], 2) ?></td>
                            <td><?= $s['discount_percent'] ?>%</td>
                            <td><?= number_format($s['rating'], 1) ?> <i class="fas fa-star text-warning"></i></td>
                            <td>
                                <span class="badge bg-<?= $s['status']=='active'?'success':'secondary' ?>">
                                    <?= $s['status'] ?>
                                </span>
                             </td>
                            <td class="btn-group-sm">
                                <button class="btn btn-sm btn-primary" onclick="editTwoWheeler(<?= $s['id'] ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="?page=two_wheeler_services&toggle_service_status=1&type=two_wheeler&id=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-<?= $s['status']=='active'?'ban':'check' ?>"></i> Toggle
                                </a>
                                <a href="?page=two_wheeler_services&delete=two_wheeler_services&id=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                             </td>
                         </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Add Two Wheeler Service Modal -->
            <div class="modal fade" id="addTwoWheelerModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Two Wheeler Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="add_two_wheeler_service.php">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Service Name *</label>
                                    <input type="text" name="service_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description *</label>
                                    <textarea name="description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Original Price (₹) *</label>
                                        <input type="number" name="original_price" class="form-control" step="0.01" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Offer Price (₹) *</label>
                                        <input type="number" name="offer_price" class="form-control" step="0.01" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Discount Percent (%)</label>
                                        <input type="number" name="discount_percent" class="form-control" step="1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Rating (1-5)</label>
                                        <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" value="4.5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Badge Text (Optional)</label>
                                    <input type="text" name="badge_text" class="form-control" placeholder="e.g., Popular, Best Seller">
                                </div>
                                <div class="mb-3">
                                    <label>Service Image</label>
                                    <input type="file" name="service_image" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label>Display Order</label>
                                    <input type="number" name="display_order" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Service</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Edit Two Wheeler Service Modal -->
            <div class="modal fade" id="editTwoWheelerModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Two Wheeler Service</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="edit_two_wheeler_service.php">
                            <div class="modal-body">
                                <input type="hidden" name="service_id" id="edit_tw_id">
                                <div class="mb-3">
                                    <label>Service Name *</label>
                                    <input type="text" name="service_name" id="edit_tw_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Description *</label>
                                    <textarea name="description" id="edit_tw_description" class="form-control" rows="3" required></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Original Price (₹) *</label>
                                        <input type="number" name="original_price" id="edit_tw_original" class="form-control" step="0.01" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Offer Price (₹) *</label>
                                        <input type="number" name="offer_price" id="edit_tw_offer" class="form-control" step="0.01" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Discount Percent (%)</label>
                                        <input type="number" name="discount_percent" id="edit_tw_discount" class="form-control" step="1">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Rating (1-5)</label>
                                        <input type="number" name="rating" id="edit_tw_rating" class="form-control" step="0.1" min="0" max="5">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Badge Text (Optional)</label>
                                    <input type="text" name="badge_text" id="edit_tw_badge" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Change Image (Optional)</label>
                                    <input type="file" name="service_image" class="form-control" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label>Display Order</label>
                                    <input type="number" name="display_order" id="edit_tw_order" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Service</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Two wheeler services table not found. Please run the database setup.</div>
            <?php endif; ?>

        <?php elseif($page == 'packages'): ?>
            <h2>Manage Packages</h2>
            <?php if (table_exists($conn, 'packages')): ?>
            <div class="d-flex justify-content-between">
                <form method="get">
                    <input type="hidden" name="page" value="packages">
                    <input type="text" name="search" class="form-control search-box" placeholder="Search packages..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </form>
                <a href="add_package.php" class="btn btn-add"><i class="fas fa-plus"></i> Add Package</a>
            </div>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>Package Name</th><th>Description</th><th>Price</th><th>Discounted</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $search = isset($_GET['search']) ? '%'.$conn->real_escape_string($_GET['search']).'%' : '%%';
                        $stmt = $conn->prepare("SELECT id, package_name, description, price, discount_price FROM packages WHERE package_name LIKE ?");
                        $stmt->bind_param("s", $search);
                        $stmt->execute();
                        $pkgs = $stmt->get_result();
                        while ($p = $pkgs->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['package_name']) ?></td>
                            <td><?= htmlspecialchars($p['description']) ?></td>
                            <td>₹<?= $p['price'] ?></td>
                            <td>₹<?= $p['discount_price'] ?></td>
                            <td>
                                <a href="edit_package.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="?page=packages&delete=packages&id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
                            </td>
                         </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Packages table not found in database.</div>
            <?php endif; ?>

        <?php elseif($page == 'bookings'): ?>
            <h2>View Bookings</h2>
            <?php if (table_exists($conn, 'bookings')): ?>
            <form method="get" class="row mb-3">
                <input type="hidden" name="page" value="bookings">
                <div class="col-md-3"><input type="date" name="from_date" class="form-control" value="<?= $_GET['from_date']??'' ?>" placeholder="From date"></div>
                <div class="col-md-3"><input type="date" name="to_date" class="form-control" value="<?= $_GET['to_date']??'' ?>" placeholder="To date"></div>
                <div class="col-md-3">
                    <select name="status_filter" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" <?= (isset($_GET['status_filter'])&&$_GET['status_filter']=='pending')?'selected':'' ?>>Pending</option>
                        <option value="accepted" <?= (isset($_GET['status_filter'])&&$_GET['status_filter']=='accepted')?'selected':'' ?>>Accepted</option>
                        <option value="in_progress" <?= (isset($_GET['status_filter'])&&$_GET['status_filter']=='in_progress')?'selected':'' ?>>In Progress</option>
                        <option value="completed" <?= (isset($_GET['status_filter'])&&$_GET['status_filter']=='completed')?'selected':'' ?>>Completed</option>
                        <option value="cancelled" <?= (isset($_GET['status_filter'])&&$_GET['status_filter']=='cancelled')?'selected':'' ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="search_user" class="form-control" placeholder="Search by user" value="<?= htmlspecialchars($_GET['search_user'] ?? '') ?>">
                </div>
                <div class="col-md-12 mt-2"><button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button></div>
            </form>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Booking ID</th><th>User</th><th>Item</th><th>Type</th><th>Date/Time</th><th>Amount</th><th>Payment</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php
                        $conditions = [];
                        $params = [];
                        $types = "";
                        if (!empty($_GET['from_date'])) { $conditions[] = "b.booking_date >= ?"; $params[] = $_GET['from_date']; $types .= "s"; }
                        if (!empty($_GET['to_date']))   { $conditions[] = "b.booking_date <= ?"; $params[] = $_GET['to_date']; $types .= "s"; }
                        if (!empty($_GET['status_filter'])) { $conditions[] = "b.status = ?"; $params[] = $_GET['status_filter']; $types .= "s"; }
                        if (!empty($_GET['search_user'])) {
                            $search = "%".$_GET['search_user']."%";
                            $conditions[] = "(u.name LIKE ? OR u.phone_number LIKE ?)";
                            $params[] = $search; $params[] = $search; $types .= "ss";
                        }
                        $where = '';
                        if (!empty($conditions)) $where = "WHERE ".implode(" AND ", $conditions);
                        $sql = "SELECT b.id, u.name AS user_name, u.phone_number, b.item_name, b.item_type,
                                       b.booking_date, b.time_slot, b.amount, b.payment_status, b.status
                                FROM bookings b
                                JOIN users_details u ON b.user_id = u.id
                                $where
                                ORDER BY b.id DESC";
                        $stmt = $conn->prepare($sql);
                        if (!empty($params)) {
                            $stmt->bind_param($types, ...$params);
                        }
                        $stmt->execute();
                        $bookings = $stmt->get_result();
                        while ($b = $bookings->fetch_assoc()):
                        ?>
                        <tr>
                            <td>#<?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['user_name']) ?><br><small><?= htmlspecialchars($b['phone_number']) ?></small></td>
                            <td><?= htmlspecialchars($b['item_name']) ?></td>
                            <td><?= $b['item_type'] ?></td>
                            <td><?= $b['booking_date'] ?><br><small><?= $b['time_slot'] ?></small></td>
                            <td>₹<?= $b['amount'] ?></td>
                            <td><span class="badge bg-<?= $b['payment_status']=='paid'?'success':'warning' ?>"><?= $b['payment_status'] ?></span></td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateStatus(<?= $b['id'] ?>, this.value)">
                                    <option value="pending" <?= $b['status']=='pending'?'selected':'' ?>>Pending</option>
                                    <option value="accepted" <?= $b['status']=='accepted'?'selected':'' ?>>Accepted</option>
                                    <option value="in_progress" <?= $b['status']=='in_progress'?'selected':'' ?>>In Progress</option>
                                    <option value="completed" <?= $b['status']=='completed'?'selected':'' ?>>Completed</option>
                                    <option value="cancelled" <?= $b['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                </select>
                             </td>
                            <td>
                                <a href="assign_mechanic.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-user-cog"></i> Assign</a>
                                <a href="?page=bookings&delete=bookings&id=<?= $b['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete booking?')"><i class="fas fa-trash"></i></a>
                             </td>
                         </tr>
                        <?php endwhile; $stmt->close(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Bookings table not found in database.</div>
            <?php endif; ?>

        <?php elseif($page == 'emergency'): ?>
            <h2>Emergency Requests</h2>
            <?php if (table_exists($conn, 'emergency_requests')): ?>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>ID</th><th>User</th><th>Location</th><th>Issue</th><th>Time</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT e.id, u.name, u.phone_number, e.location, e.issue, e.request_time, e.status
                                             FROM emergency_requests e
                                             JOIN users_details u ON e.user_id = u.id
                                             ORDER BY e.id DESC");
                        while ($em = $res->fetch_assoc()):
                            $badge_class = ($em['status'] == 'pending') ? 'danger' : 'success';
                        ?>
                        <tr>
                            <td>#<?= $em['id'] ?></td>
                            <td><?= htmlspecialchars($em['name']) ?><br> <?= htmlspecialchars($em['phone_number']) ?>
                                                        <td><?= htmlspecialchars($em['location']) ?></td>
                            <td><?= htmlspecialchars($em['issue']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($em['request_time'])) ?></td>
                            <td><span class="badge bg-<?= $badge_class ?>"><?= $em['status'] ?></span></td>
                            <td><a href="assign_mechanic.php?emergency=<?= $em['id'] ?>" class="btn btn-sm btn-warning">Assign Mechanic</a></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Emergency requests table not found.</div>
            <?php endif; ?>

        <?php elseif($page == 'payments'): ?>
            <h2>Payment Management</h2>
            <?php if (table_exists($conn, 'bookings')): ?>
            <div class="table-container">
                <table class="table table-hover">
                    <thead><tr><th>Booking ID</th><th>User</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT b.id, u.name, b.amount, b.payment_method, b.transaction_id, b.payment_status
                                             FROM bookings b
                                             JOIN users_details u ON b.user_id = u.id
                                             ORDER BY b.id DESC");
                        while ($pay = $res->fetch_assoc()):
                            $badge = ($pay['payment_status']=='paid') ? 'success' : 'warning';
                        ?>
                        <tr>
                            <td>#<?= $pay['id'] ?></td>
                            <td><?= htmlspecialchars($pay['name']) ?></td>
                            <td>₹<?= $pay['amount'] ?></td>
                            <td><?= $pay['payment_method'] ?: 'N/A' ?></td>
                            <td><?= $pay['transaction_id'] ?: 'N/A' ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= $pay['payment_status'] ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Bookings table not found – payment data unavailable.</div>
            <?php endif; ?>

        <?php elseif($page == 'reports'): ?>
            <h2>Reports & Analytics</h2>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-container">
                        <h5>Monthly Revenue Report</h5>
                        <?php if (table_exists($conn, 'bookings')): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Total Bookings</th>
                                    <th>Completed Bookings</th>
                                    <th>Cancelled Bookings</th>
                                    <th>Total Revenue (₹)</th>
                                    <th>Average Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rev_query = $conn->query("
                                    SELECT 
                                        DATE_FORMAT(created_at, '%b %Y') as month,
                                        DATE_FORMAT(created_at, '%Y-%m') as month_sort,
                                        COUNT(*) as total_bookings,
                                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                                        SUM(CASE WHEN payment_status = 'paid' THEN amount ELSE 0 END) as revenue,
                                        AVG(CASE WHEN payment_status = 'paid' THEN amount ELSE NULL END) as avg_order
                                    FROM bookings
                                    GROUP BY YEAR(created_at), MONTH(created_at)
                                    ORDER BY created_at DESC LIMIT 12
                                ");
                                while ($r = $rev_query->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $r['month'] ?></td>
                                    <td><?= $r['total_bookings'] ?></td>
                                    <td><?= $r['completed_bookings'] ?></td>
                                    <td><?= $r['cancelled_bookings'] ?></td>
                                    <td><strong>₹<?= number_format($r['revenue'], 2) ?></strong></td>
                                    <td>₹<?= number_format($r['avg_order'] ?? 0, 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        
                        <h5 class="mt-4">Service Popularity Report</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Service/Package Name</th>
                                    <th>Type</th>
                                    <th>Total Bookings</th>
                                    <th>Total Revenue (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $popular_query = $conn->query("
                                    SELECT 
                                        item_name,
                                        item_type,
                                        COUNT(*) as bookings_count,
                                        SUM(amount) as total_revenue
                                    FROM bookings
                                    WHERE payment_status = 'paid'
                                    GROUP BY item_name, item_type
                                    ORDER BY bookings_count DESC
                                    LIMIT 10
                                ");
                                while ($pop = $popular_query->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($pop['item_name']) ?></td>
                                    <td><span class="badge bg-info"><?= $pop['item_type'] ?></span></td>
                                    <td><?= $pop['bookings_count'] ?></td>
                                    <td>₹<?= number_format($pop['total_revenue'], 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                            <p>No booking data available for reports.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php elseif($page == 'notifications'): ?>
            <h2>Notifications</h2>
            <?php if (table_exists($conn, 'notifications')): ?>
            <div class="table-container">
                <div class="list-group">
                    <?php
                    $notif = $conn->query("SELECT id, message, created_at, is_read FROM notifications ORDER BY created_at DESC LIMIT 20");
                    if ($notif->num_rows > 0):
                        while ($n = $notif->fetch_assoc()):
                            $read_class = $n['is_read'] ? '' : 'list-group-item-warning';
                    ?>
                    <a href="?page=notifications&read_notification=1&id=<?= $n['id'] ?>"
                       class="list-group-item list-group-item-action <?= $read_class ?>">
                        <?= htmlspecialchars($n['message']) ?>
                        <small class="text-muted float-end"><?= date('d-m-Y H:i', strtotime($n['created_at'])) ?></small>
                    </a>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div class="text-center py-3">No notifications found.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">Notifications table not found.</div>
            <?php endif; ?>

        <?php elseif($page == 'profile'): ?>
            <h2>Admin Profile</h2>
            <?php
            $admin_id = $_SESSION['admin_id'];
            $admin = $conn->query("SELECT name, email FROM users_details WHERE id = $admin_id")->fetch_assoc();
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
                $new_name = $_POST['name'];
                $new_password = $_POST['password'];
                if (!empty($new_password)) {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users_details SET name = ?, password = ? WHERE id = ?");
                    $stmt->bind_param("ssi", $new_name, $hashed, $admin_id);
                } else {
                    $stmt = $conn->prepare("UPDATE users_details SET name = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_name, $admin_id);
                }
                $stmt->execute();
                $stmt->close();
                $_SESSION['message'] = ['type' => 'success', 'text' => 'Profile updated successfully.'];
                header("location: ?page=profile");
                exit;
            }
            ?>
            <div class="row">
                <div class="col-md-6">
                    <form method="post" class="table-container">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($admin['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Email (readonly)</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" readonly disabled>
                        </div>
                        <div class="mb-3">
                            <label>New Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                            <small class="text-muted">Minimum 6 characters recommended</small>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="table-container">
                        <h5>Session Information</h5>
                        <table class="table">
                            <tr>
                                <th>Admin ID:</th>
                                <td><?= $_SESSION['admin_id'] ?></td>
                            </tr>
                            <tr>
                                <th>Admin Type:</th>
                                <td><span class="badge bg-primary"><?= $_SESSION['type'] ?></span></td>
                            </tr>
                            <tr>
                                <th>Last Login:</th>
                                <td><?= date('d-m-Y H:i:s') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Function to update booking status
function updateStatus(bookingId, status) {
    if (!confirm('Update booking #'+bookingId+' to '+status+'?')) return;
    fetch('?action=update_booking_status&booking_id='+bookingId+'&status='+encodeURIComponent(status))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Update failed! Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating status.');
        });
}

// Function to edit four wheeler service
function editFourWheeler(id) {
    // Fetch service details via AJAX
    $.ajax({
        url: 'get_service_details.php',
        method: 'POST',
        data: { id: id, type: 'four_wheeler' },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#edit_fw_id').val(data.service.id);
                $('#edit_fw_name').val(data.service.service_name);
                $('#edit_fw_description').val(data.service.description);
                $('#edit_fw_original').val(data.service.original_price);
                $('#edit_fw_offer').val(data.service.offer_price);
                $('#edit_fw_discount').val(data.service.discount_percent);
                $('#edit_fw_rating').val(data.service.rating);
                $('#edit_fw_badge').val(data.service.badge_text);
                $('#edit_fw_order').val(data.service.display_order);
                $('#editFourWheelerModal').modal('show');
            } else {
                alert('Failed to load service details');
            }
        },
        error: function() {
            alert('Error loading service details');
        }
    });
}

// Function to edit two wheeler service
function editTwoWheeler(id) {
    // Fetch service details via AJAX
    $.ajax({
        url: 'get_service_details.php',
        method: 'POST',
        data: { id: id, type: 'two_wheeler' },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#edit_tw_id').val(data.service.id);
                $('#edit_tw_name').val(data.service.service_name);
                $('#edit_tw_description').val(data.service.description);
                $('#edit_tw_original').val(data.service.original_price);
                $('#edit_tw_offer').val(data.service.offer_price);
                $('#edit_tw_discount').val(data.service.discount_percent);
                $('#edit_tw_rating').val(data.service.rating);
                $('#edit_tw_badge').val(data.service.badge_text);
                $('#edit_tw_order').val(data.service.display_order);
                $('#editTwoWheelerModal').modal('show');
            } else {
                alert('Failed to load service details');
            }
        },
        error: function() {
            alert('Error loading service details');
        }
    });
}

// Auto-hide alerts after 5 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Add search functionality with enter key
    $('.search-box').keypress(function(e) {
        if (e.which == 13) {
            $(this).closest('form').submit();
        }
    });
});
</script>
</body>
</html>