<?php
session_start();
require_once '../dbcon.php';

// ---- ONLY CUSTOMERS CAN BOOK ----
if (!isset($_SESSION['id']) || $_SESSION['type'] !== 'customer') {
    header('location: login.php');
    exit();
}

// Check if booking data exists in session
if (!isset($_SESSION['pending_booking'])) {
    header('location: services.php');
    exit();
}

$user_id = $_SESSION['id'];
$booking_data = $_SESSION['pending_booking'];
$user_location = $booking_data['location'];

// Create necessary tables if they don't exist
$conn->query("CREATE TABLE IF NOT EXISTS location_cache (
    id INT AUTO_INCREMENT PRIMARY KEY,
    address VARCHAR(500) UNIQUE,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS mechanic_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mechanic_id INT,
    status ENUM('available', 'busy') DEFAULT 'available',
    booking_time DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Function to get coordinates from address
function getCoordinates($address, $conn) {
    if (empty($address)) return null;
    
    // First check if coordinates exist in cache
    $stmt = $conn->prepare("SELECT latitude, longitude FROM location_cache WHERE address = ?");
    $stmt->bind_param("s", $address);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return ['lat' => $row['latitude'], 'lon' => $row['longitude']];
    }
    
    // Use Nominatim API
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($address) . "&format=json&limit=1";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Roadside Companion App');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (!empty($data)) {
        $lat = $data[0]['lat'];
        $lon = $data[0]['lon'];
        
        // Cache the coordinates
        $stmt = $conn->prepare("INSERT INTO location_cache (address, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $address, $lat, $lon);
        $stmt->execute();
        $stmt->close();
        
        return ['lat' => $lat, 'lon' => $lon];
    }
    
    return null;
}

// Function to calculate distance between two coordinates
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $km = $miles * 1.609344;
    return $km;
}

// Get customer location coordinates
$customer_coords = getCoordinates($user_location, $conn);
$available_mechanics = [];

if ($customer_coords) {
    // Get all active service providers (mechanics)
    $query = "SELECT u.*, 
              (SELECT COUNT(*) FROM mechanic_availability WHERE mechanic_id = u.id AND status = 'busy' AND booking_time > DATE_SUB(NOW(), INTERVAL 1 HOUR)) as is_busy
              FROM users_details u 
              WHERE u.accounttype = 'service-provider' AND u.status = 'active'";
    
    $result = $conn->query($query);
    
    while ($mechanic = $result->fetch_assoc()) {
        // Get mechanic coordinates from their stored location
        $mechanic_location = $mechanic['location'] ?? '';
        
        if (!empty($mechanic_location)) {
            $mechanic_coords = getCoordinates($mechanic_location, $conn);
            
            if ($mechanic_coords) {
                $distance = calculateDistance(
                    $customer_coords['lat'], $customer_coords['lon'],
                    $mechanic_coords['lat'], $mechanic_coords['lon']
                );
                
                // Check if mechanic is within 10km and not busy
                if ($distance <= 10 && $mechanic['is_busy'] == 0) {
                    $mechanic['distance'] = round($distance, 2);
                    $mechanic['rating'] = 4.5; // Default rating
                    $mechanic['experience_years'] = 5;
                    $mechanic['specialization'] = 'Mechanic';
                    
                    // Calculate score (based on distance - closer is better)
                    $mechanic['score'] = round((10 - $distance) * 10, 1);
                    
                    $available_mechanics[] = $mechanic;
                }
            }
        }
    }
    
    // Sort mechanics by score (highest first - closest)
    usort($available_mechanics, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
}

// Select top mechanic
$selected_mechanic = !empty($available_mechanics) ? $available_mechanics[0] : null;

// If mechanic is selected
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['select_mechanic'])) {
    $mechanic_id = intval($_POST['mechanic_id']);
    
    // Store selected mechanic in session
    $_SESSION['selected_mechanic'] = $mechanic_id;
    
    // Proceed to confirm booking
    header('location: confirm_booking.php');
    exit();
}

include_once 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Mechanic - RoadSide Companion</title>
    <link href="../bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Orbitron:wght@500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0a0f 0%, #0f0f1a 100%);
            min-height: 100vh;
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
            background: radial-gradient(circle at 20% 50%, rgba(13,110,253,0.08) 0%, transparent 50%);
            z-index: 0;
        }
        
        .main-wrapper {
            position: relative;
            z-index: 1;
            padding: 40px 20px 60px;
        }
        
        .search-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .search-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .search-header h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .search-header p {
            color: rgba(255,255,255,0.7);
        }
        
        .mechanic-card {
            background: rgba(15, 20, 35, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        
        .mechanic-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-color: rgba(13,110,253,0.5);
        }
        
        .mechanic-card.selected {
            border: 2px solid #0d6efd;
            background: rgba(13,110,253,0.15);
        }
        
        .mechanic-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-right: 20px;
        }
        
        .mechanic-info h4 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .mechanic-info .rating {
            color: #FFD700;
            margin-bottom: 5px;
        }
        
        .mechanic-info .distance {
            color: #20B2AA;
            font-size: 0.85rem;
        }
        
        .badge-custom {
            background: rgba(13,110,253,0.2);
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            margin-right: 8px;
        }
        
        .timer-container {
            background: rgba(13,110,253,0.1);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13,110,253,0.4);
        }
        
        @media (max-width: 768px) {
            .mechanic-avatar {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
                margin-bottom: 10px;
            }
            .mechanic-card {
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="search-container">
        <div class="search-header">
            <h2><i class="fas fa-search-location me-2"></i>Finding Available Mechanics</h2>
            <p>Searching for mechanics within 10km of your location</p>
        </div>
        
        <div class="timer-container">
            <i class="fas fa-map-marker-alt me-2"></i>
            <strong>Your Location:</strong> <?= htmlspecialchars($user_location) ?>
        </div>
        
        <?php if (empty($available_mechanics)): ?>
            <div class="text-center p-5" style="background: rgba(15,20,35,0.85); border-radius: 20px;">
                <i class="fas fa-sad-tear fa-4x mb-3" style="color: #FFA500;"></i>
                <h4>No Mechanics Available Nearby</h4>
                <p>We couldn't find any available mechanics within 10km of your location.</p>
                <p class="mt-3">Please try again later or contact our support.</p>
                <a href="book_service.php" class="btn btn-primary mt-3">Back to Booking</a>
            </div>
        <?php else: ?>
            <div class="timer-container">
                <i class="fas fa-users me-2"></i>
                <strong>Found <?= count($available_mechanics) ?> mechanic(s)</strong> within 10km
                <div class="mt-2">
                    <small>Showing mechanics sorted by distance (closest first)</small>
                </div>
            </div>
            
            <form method="post" id="mechanicForm">
                <input type="hidden" name="mechanic_id" id="selectedMechanicId" value="<?= $selected_mechanic['id'] ?? '' ?>">
                
                <?php foreach ($available_mechanics as $index => $mechanic): ?>
                    <div class="mechanic-card <?= ($index == 0) ? 'selected' : '' ?>" onclick="selectMechanic(<?= $mechanic['id'] ?>, this)">
                        <div class="row align-items-center">
                            <div class="col-md-2 text-center">
                                <div class="mechanic-avatar mx-auto">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mechanic-info">
                                    <h4><?= htmlspecialchars($mechanic['name']) ?></h4>
                                    <div class="rating">
                                        <?php 
                                        for($i = 1; $i <= 5; $i++):
                                            if($i <= 4.5):
                                        ?>
                                            <i class="fas fa-star"></i>
                                        <?php elseif($i - 0.5 <= 4.5): ?>
                                            <i class="fas fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="far fa-star"></i>
                                        <?php endif; ?>
                                        <?php endfor; ?>
                                        <span class="ms-2">(4.5)</span>
                                    </div>
                                    <div class="distance">
                                        <i class="fas fa-location-dot"></i> <?= $mechanic['distance'] ?> km away
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div>
                                    <span class="badge-custom">
                                        <i class="fas fa-wrench"></i> Mechanic
                                    </span>
                                    <br>
                                    <span class="badge-custom">
                                        <i class="fas fa-clock"></i> Available Now
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <i class="fas fa-tachometer-alt text-primary"></i>
                                    <div>Distance Score</div>
                                    <strong><?= number_format($mechanic['score'], 1) ?></strong>
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <?php if ($index == 0): ?>
                                    <span class="badge bg-success p-2">
                                        <i class="fas fa-star"></i> Closest
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="text-center mt-4">
                    <button type="submit" name="select_mechanic" class="btn btn-primary btn-lg">
                        <i class="fas fa-check-circle me-2"></i>Select & Proceed
                    </button>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">Auto-selected the closest mechanic to your location</small>
                </div>
            </form>
        <?php endif; ?>
        
        <div class="text-center mt-4">
            <a href="book_service.php" class="text-white-50" style="color: rgba(255,255,255,0.6); text-decoration: none;">
                <i class="fas fa-arrow-left me-2"></i>Back to Booking
            </a>
        </div>
    </div>
</div>

<script>
    let selectedCard = null;
    
    function selectMechanic(mechanicId, element) {
        // Remove selected class from all cards
        document.querySelectorAll('.mechanic-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Add selected class to clicked card
        element.classList.add('selected');
        
        // Set the hidden input value
        document.getElementById('selectedMechanicId').value = mechanicId;
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include_once 'footer.php'; ?>