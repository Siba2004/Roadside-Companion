<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Roadside Companion</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #1e293b;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            color: #ffffff;
            font-size: 20px;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .navbar ul li a {
            color: #e5e7eb;
            text-decoration: none;
            font-size: 15px;
        }

        .navbar ul li a:hover {
            color: #38bdf8;
        }

        .logout {
            color: #f87171 !important;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="home.php" class="logo">🚗 Roadside Companion</a>

    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#">Track Request</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="logout.php" class="logout">Logout</a></li>
    </ul>
</div>

</body>
</html>
