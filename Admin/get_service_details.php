<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['type'])) {
    $id = (int)$_POST['id'];
    $type = $_POST['type'];
    $table = $type == 'four_wheeler' ? 'four_wheeler_services' : 'two_wheeler_services';
    
    $query = "SELECT * FROM $table WHERE id = $id";
    $result = mysqli_query($conn, $query);
    
    if ($result && $service = mysqli_fetch_assoc($result)) {
        echo json_encode(['success' => true, 'service' => $service]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Service not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>