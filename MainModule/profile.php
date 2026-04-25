<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

$user_id = $_SESSION['id'];
$user = $conn->query("SELECT * FROM users_details WHERE id = $user_id")->fetch_assoc();
if (!$user) { die("User not found."); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name  = trim($_POST['name']);
    $new_phone = trim($_POST['phone_number']);
    $new_password = $_POST['password'];

    if (empty($new_name)) {
        $message = '<div class="alert alert-danger">Name is required.</div>';
    } else {
        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users_details SET name=?, phone_number=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $new_name, $new_phone, $hashed, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users_details SET name=?, phone_number=? WHERE id=?");
            $stmt->bind_param("ssi", $new_name, $new_phone, $user_id);
        }
        if ($stmt->execute()) {
            $_SESSION['name'] = $new_name;
            $message = '<div class="alert alert-success">Profile updated successfully.</div>';
            $user = $conn->query("SELECT * FROM users_details WHERE id = $user_id")->fetch_assoc();
        } else {
            $message = '<div class="alert alert-danger">Update failed.</div>';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #0a0a0f;                /* solid dark base */
            font-family: 'Poppins', sans-serif;
            color: white;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(13,110,253,0.03) 0%, rgba(0,0,0,0.8) 100%);
            z-index: 0;
        }
        .profile-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }
        .profile-card {
            background: rgba(20,20,30,0.65);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.7);
        }
        .profile-card h2 {
            font-family: 'Orbitron', sans-serif;
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(13,110,253,0.5);
        }
        .form-control {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.15);
            border-color: #0d6efd;
            box-shadow: 0 0 10px #0d6efd;
        }
        label {
            font-weight: 500;
            color: rgba(255,255,255,0.9);
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px;
            border-radius: 8px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #0b5ed7;
            box-shadow: 0 10px 20px rgba(13,110,253,0.4);
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <h2><i class="fas fa-user-circle me-2"></i>My Profile</h2>
            <?= $message ?>
            <form method="post">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email (readonly)</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label>Phone Number</label>
                    <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($user['phone_number']) ?>">
                </div>
                <div class="mb-3">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary w-100">Update Profile</button>
            </form>
            <div class="text-center mt-3">
                <a href="home.php" class="text-white-50">← Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>