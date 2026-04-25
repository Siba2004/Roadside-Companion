<?php
session_start();
require_once '../dbcon.php';
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit;
}
// Ensure services table exists (run the SQL snippet if not)
if ($conn->query("SHOW TABLES LIKE 'services'")->num_rows == 0) {
    die("Services table does not exist. Please create it first.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['service_name']);
    $price = floatval($_POST['price']);
    $discount = floatval($_POST['discount_price']);

    if (empty($name)) {
        $error = "Service name required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO services (service_name, price, discount_price) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $name, $price, $discount);
        $stmt->execute();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Service added.'];
        header('location: admin_home.php?page=services');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:500px">
    <div class="card shadow">
        <div class="card-header bg-success text-white"><h5>Add Service</h5></div>
        <div class="card-body">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="post">
                <div class="mb-3"><input name="service_name" class="form-control" placeholder="Service Name" required></div>
                <div class="mb-3"><input name="price" type="number" step="0.01" class="form-control" placeholder="Original Price" required></div>
                <div class="mb-3"><input name="discount_price" type="number" step="0.01" class="form-control" placeholder="Discounted Price" required></div>
                <button class="btn btn-success w-100">Save Service</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>