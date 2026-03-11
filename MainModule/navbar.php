<?php
require_once '../dbcon.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RoadSide Companion - Corporate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0B4F6C;
            --secondary: #3282B8;
            --accent: #1B98F5;
            --success: #20B2AA;
            --warning: #FFA500;
            --light-bg: #F8FBFE;
            --dark-blue: #0A2472;
            --gray-corporate: #4A5568;
            --border-corporate: #E2E8F0;
        }

        * {
            font-family: 'Montserrat', sans-serif;
        }

        body {
            background: var(--light-bg);
            color: var(--gray-corporate);
        }

        /* ── NAVBAR - Matching login/register design ── */
        .navbar { 
            background: rgba(0,0,0,0.65); 
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 0; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.8);
        }
        .navbar-brand { 
            font-weight: 600; 
            font-size: 1.4rem; 
            color: white !important; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            font-family: 'Orbitron', sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .navbar-brand i { 
            color: var(--primary); 
            font-size: 1.8rem;
        }
        .nav-link { 
            color: rgba(255,255,255,0.8) !important; 
            font-weight: 400; 
            margin: 0 10px; 
            text-transform: uppercase; 
            font-size: .85rem; 
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .nav-link:hover { 
            color: var(--primary) !important; 
            transform: translateY(-2px);
        }

/* Responsive */
@media (max-width: 768px) {
    .slide img {
        height: 200px;
    }
    
    .sli
    </style>
</head>
<body>
  <!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-tools me-2"></i>RoadSide Companion
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#reviews">Your Location</a></li>
                <li class="nav-item ms-2">
                    <a class="btn btn-primary" href="login.php">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>