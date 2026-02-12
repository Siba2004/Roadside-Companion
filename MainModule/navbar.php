<?php
require_once '../dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<html>
<head>
    <title>Roadside Companion</title>
     <style>
        .nav-link{
            color: white !important;
        }
    </style>
    <link rel="stylesheet" href="../bootstrap.min.css">
</head>
<body style="background-image: url('../image/homebg.webp'); background-size: cover; background-attachment: fixed;">

<nav class="navbar navbar-expand-lg bg-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand text-light" href="home.php">🚗 Roadside Companion</a>
        <button class="navbar-toggler btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                <?php
                if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])){?>
                <form action="search.php" method="post" class="d-flex mx-auto w-50" role="search">
                    <input name="search_key" class="form-control rounded-0" type="search" placeholder="Search" aria-label="Search"/>
                    <button class="btn btn-light rounded-0" type="submit">🔍</button>
                </form>
                <li class="nav-item dropdown">
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Services</a></li>
                        <li><a class="dropdown-item" href="#">Track Request</a></li>
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item logout" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
                <?php }else{?>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link fw-semibold" href="register.php">Register</a></li>
                <?php }
                if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){?>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="login.php">Login</a></li>
                <?php }?>
            </ul>
        </div>
    </div>
</nav>
