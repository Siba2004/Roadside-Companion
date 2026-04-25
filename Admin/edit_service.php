<?php
session_start();
require_once '../dbcon.php';
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit;
}
if ($conn->query("SHOW TABLES LIKE 'services'")->num_rows == 0) {
    die("Services table missing.");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$service = $conn->query("SELECT * FROM services WHERE id = $id")->fetch_assoc();
if (!$service) die("Service not found.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['service_name']);
    $price = floatval($_POST['price']);
    $discount = floatval($_POST['discount_price']);

    if (empty($name)) {
        $error = "Service name required.";
    } else {
        $stmt = $conn->prepare("UPDATE services SET service_name=?, price=?, discount_price=? WHERE id=?");
        $stmt->bind_param("sddi", $name, $price, $discount, $id);
        $stmt->execute();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Service updated.'];
        header('location: admin_home.php?page=services');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:500px">
    <div class="card shadow">
        <div class="card-header bg-success text-white"><h5>Edit Service</h5></div>
        <div class="card-body">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="post">
                <div class="mb-3"><input name="service_name" class="form-control" value="<?= htmlspecialchars($service['service_name']) ?>" required></div>
                <div class="mb-3"><input name="price" type="number" step="0.01" class="form-control" value="<?= $service['price'] ?>" required></div>
                <div class="mb-3"><input name="discount_price" type="number" step="0.01" class="form-control" value="<?= $service['discount_price'] ?>" required></div>
                <button class="btn btn-success w-100">Update Service</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>