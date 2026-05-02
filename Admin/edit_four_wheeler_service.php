<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['service_id'];
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $original_price = (float)$_POST['original_price'];
    $offer_price = (float)$_POST['offer_price'];
    $discount_percent = (int)$_POST['discount_percent'];
    $rating = (float)$_POST['rating'];
    $badge_text = mysqli_real_escape_string($conn, $_POST['badge_text']);
    $display_order = (int)$_POST['display_order'];
    
    // Handle image upload
    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
        // Delete old image
        $result = mysqli_query($conn, "SELECT image_path FROM four_wheeler_services WHERE id = $id");
        $old = mysqli_fetch_assoc($result);
        if ($old['image_path'] && file_exists("../" . $old['image_path'])) {
            unlink("../" . $old['image_path']);
        }
        
        // Upload new image
        $upload_dir = "../services_pics/four_wheeler_services_pics/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_extension;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['service_image']['tmp_name'], $target_file)) {
            $image_path = "services_pics/four_wheeler_services_pics/" . $file_name;
            $query = "UPDATE four_wheeler_services SET 
                      service_name='$service_name', description='$description', 
                      original_price=$original_price, offer_price=$offer_price, 
                      discount_percent=$discount_percent, rating=$rating, 
                      badge_text='$badge_text', display_order=$display_order, 
                      image_path='$image_path' 
                      WHERE id=$id";
        } else {
            $_SESSION['message'] = ['type' => 'danger', 'text' => 'Failed to upload image'];
            header('location: admin_home.php?page=four_wheeler_services');
            exit();
        }
    } else {
        $query = "UPDATE four_wheeler_services SET 
                  service_name='$service_name', description='$description', 
                  original_price=$original_price, offer_price=$offer_price, 
                  discount_percent=$discount_percent, rating=$rating, 
                  badge_text='$badge_text', display_order=$display_order 
                  WHERE id=$id";
    }
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Service updated successfully!'];
    } else {
        $_SESSION['message'] = ['type' => 'danger', 'text' => 'Failed to update service: ' . mysqli_error($conn)];
    }
    
    header('location: admin_home.php?page=four_wheeler_services');
    exit();
}
?>