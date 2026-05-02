<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $original_price = (float)$_POST['original_price'];
    $offer_price = (float)$_POST['offer_price'];
    $discount_percent = (int)$_POST['discount_percent'];
    $rating = (float)$_POST['rating'];
    $badge_text = mysqli_real_escape_string($conn, $_POST['badge_text']);
    $display_order = (int)$_POST['display_order'];
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
        $upload_dir = "../services_pics/two_wheeler_services_pics/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['service_image']['tmp_name'], $target_file)) {
            $image_path = "services_pics/two_wheeler_services_pics/" . $file_name;
        }
    }
    
    $query = "INSERT INTO two_wheeler_services (service_name, description, original_price, offer_price, 
              discount_percent, image_path, badge_text, rating, display_order, status) 
              VALUES ('$service_name', '$description', $original_price, $offer_price, $discount_percent, 
              '$image_path', '$badge_text', $rating, $display_order, 'active')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Two wheeler service added successfully!'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Failed to add service: ' . mysqli_error($conn)];
    }
    
    header('location: admin_home.php?page=two_wheeler_services');
    exit();
}
?>