<?php
session_start();
require_once '../dbcon.php';
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $location = trim($_POST['location']);

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields except location are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users_details (name, email, phone_number, password, accounttype, status, location)
                                VALUES (?, ?, ?, ?, 'service-provider', 'available', ?)");
        $stmt->bind_param("sssss", $name, $email, $phone, $hashed, $location);
        if ($stmt->execute()) {
            $_SESSION['message'] = ['type' => 'success', 'text' => 'Mechanic added successfully.'];
            header('location: admin_home.php?page=mechanics');
            exit;
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Mechanic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:500px">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h5>Add Mechanic</h5></div>
        <div class="card-body">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="post">
                <div class="mb-3"><input name="name" class="form-control" placeholder="Full Name" required></div>
                <div class="mb-3"><input name="email" class="form-control" placeholder="Email" required></div>
                <div class="mb-3"><input name="phone_number" class="form-control" placeholder="Phone Number" required></div>
                <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
                <div class="mb-3"><input name="location" class="form-control" placeholder="Location (optional)"></div>
                <button class="btn btn-primary w-100">Save Mechanic</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>