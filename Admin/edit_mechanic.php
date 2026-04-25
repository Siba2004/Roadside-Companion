<?php
session_start();
require_once '../dbcon.php';
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mech = $conn->query("SELECT * FROM users_details WHERE id = $id AND accounttype='service-provider'")->fetch_assoc();
if (!$mech) die("Mechanic not found.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone_number']);
    $location = trim($_POST['location']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Name, email, phone required.";
    } else {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users_details SET name=?, email=?, phone_number=?, location=?, password=? WHERE id=?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $location, $hashed, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users_details SET name=?, email=?, phone_number=?, location=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $location, $id);
        }
        $stmt->execute();
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Mechanic updated.'];
        header('location: admin_home.php?page=mechanics');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Mechanic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width:500px">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h5>Edit Mechanic</h5></div>
        <div class="card-body">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="post">
                <div class="mb-3"><input name="name" class="form-control" value="<?= htmlspecialchars($mech['name']) ?>" required></div>
                <div class="mb-3"><input name="email" class="form-control" value="<?= htmlspecialchars($mech['email']) ?>" required></div>
                <div class="mb-3"><input name="phone_number" class="form-control" value="<?= htmlspecialchars($mech['phone_number']) ?>" required></div>
                <div class="mb-3"><input name="location" class="form-control" value="<?= htmlspecialchars($mech['location'] ?? '') ?>"></div>
                <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="New password (leave blank to keep)"></div>
                <button class="btn btn-primary w-100">Update Mechanic</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>