<?php
include_once 'navbar.php';
require_once '../dbcon.php';   // if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Service Type - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --dark-blue: #0b5ed7;
            --dark-overlay: rgba(6, 6, 6, 0.95);
        }

        * { font-family: 'Poppins', sans-serif; }
        
        body {
            background: url('vehiclebg.png') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            color: white;
            position: relative;
        }
        
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark-overlay);
            z-index: 0;
        }

        .page-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Card wrapper with gradient background */
        .selection-box {
            position: relative;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 50px 30px;
            box-shadow: 0 0 40px rgba(0,0,0,0.8);
            max-width: 750px;
            width: 90%;
            overflow: hidden;
        }

        .selection-box::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(135deg, rgba(13,110,253,0.15) 0%, rgba(0,0,0,0.8) 100%);
            z-index: -1;
        }

        .selection-box > * {
            position: relative;
            z-index: 1;
        }

        .selection-box h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
            text-shadow: 0 0 15px rgba(13,110,253,0.5);
            animation: fadeInDown 0.6s ease-out;
        }

        .selection-box p {
            color: rgba(255,255,255,0.85);
            margin-bottom: 40px;
            font-size: 1.1rem;
            animation: fadeInDown 0.6s ease-out 0.1s;
            animation-fill-mode: both;
        }

        /* Dropdown animation for cards */
        @keyframes dropdown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .service-select-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s;
            display: block;
            text-decoration: none;
            color: white;
            height: 100%;
            /* Dropdown animation */
            animation: dropdown 0.6s ease-out;
            animation-fill-mode: both;

            /* Initial state hidden (animation-fill-mode: both applies from start state) */
            /* We don't need opacity:0 initially because the keyframe handles it */
        }

        .service-select-card:nth-child(1) {
            animation-delay: 0.2s;
        }
        .service-select-card:nth-child(2) {
            animation-delay: 0.35s;
        }

        .service-select-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 0 30px rgba(13,110,253,0.4);
            color: white;
        }

        .service-select-card i {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .service-select-card h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .service-select-card p {
            color: rgba(255,255,255,0.8);
            margin-top: 10px;
            font-size: 0.9rem;
            animation: none;   /* remove inherited animation from parent */
        }

        @media (max-width: 576px) {
            .selection-box {
                padding: 30px 20px;
            }
            .selection-box h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="selection-box text-center">
            <h1>Select Service Type</h1>
            <p>Choose the type of vehicle to see available services and pricing</p>
            <div class="row g-4">
                <div class="col-md-6">
                    <a href="four_wheeler_services.php" class="service-select-card">
                        <i class="fas fa-car"></i>
                        <h3>Four Wheeler</h3>
                        <p>Cars, Jeeps, SUVs & more</p>
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="two_wheeler_services.php" class="service-select-card">
                        <i class="fas fa-motorcycle"></i>
                        <h3>Two Wheeler</h3>
                        <p>Bikes, Scooters & more</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include_once 'footer.php'; ?>