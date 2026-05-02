<?php
require_once '../dbcon.php';
session_start();

if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$service_type = $_POST['service_type'] ?? 'four_wheeler';
$table = $service_type == 'two_wheeler' ? 'two_wheeler_services' : 'four_wheeler_services';

switch ($action) {
    case 'get_services':
        $query = "SELECT * FROM $table ORDER BY display_order ASC, id ASC";
        $result = mysqli_query($conn, $query);
        $services = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
        
        foreach ($services as $service) {
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="service-card">
                    <?php if ($service['image_path']): ?>
                        <img src="../<?php echo htmlspecialchars($service['image_path']); ?>" class="service-image" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                    <?php else: ?>
                        <div class="service-image bg-secondary d-flex align-items-center justify-content-center">
                            <i class="fas fa-image fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    
                    <h5><?php echo htmlspecialchars($service['service_name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars(substr($service['description'], 0, 100)) . '...'; ?></p>
                    
                    <div class="mb-2">
                        <span class="text-decoration-line-through text-muted">₹<?php echo number_format($service['original_price']); ?></span>
                        <strong class="text-success ms-2">₹<?php echo number_format($service['offer_price']); ?></strong>
                        <?php if ($service['discount_percent'] > 0): ?>
                            <span class="badge bg-warning ms-2">-<?php echo $service['discount_percent']; ?>%</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-2">
                        <span class="status-badge <?php echo $service['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ucfirst($service['status']); ?>
                        </span>
                        <?php if ($service['badge_text']): ?>
                            <span class="badge bg-info ms-2"><?php echo htmlspecialchars($service['badge_text']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="btn-group-custom d-flex mt-3">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1" onclick="editService(<?php echo $service['id']; ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger flex-grow-1" onclick="deleteService(<?php echo $service['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <button class="btn btn-sm <?php echo $service['status'] == 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?> flex-grow-1" 
                                onclick="toggleStatus(<?php echo $service['id']; ?>, '<?php echo $service['status']; ?>')">
                            <i class="fas fa-<?php echo $service['status'] == 'active' ? 'ban' : 'check'; ?>"></i>
                            <?php echo $service['status'] == 'active' ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
        break;
        
    case 'get_service':
        $id = (int)$_POST['service_id'];
        $query = "SELECT * FROM $table WHERE id = $id";
        $result = mysqli_query($conn, $query);
        echo json_encode(mysqli_fetch_assoc($result));
        break;
        
    case 'add_service':
        $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $original_price = (float)$_POST['original_price'];
        $offer_price = (float)$_POST['offer_price'];
        $discount_percent = (int)$_POST['discount_percent'];
        $rating = (float)$_POST['rating'];
        $badge_text = mysqli_real_escape_string($conn, $_POST['badge_text']);
        $display_order = (int)$_POST['display_order'];
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $upload_dir = "../services_pics/{$service_type}_services_pics/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['service_image']['tmp_name'], $target_file)) {
                $image_path = "services_pics/{$service_type}_services_pics/" . $file_name;
            }
        }
        
        $query = "INSERT INTO $table (service_name, description, original_price, offer_price, discount_percent, 
                  image_path, badge_text, rating, display_order, status) 
                  VALUES ('$service_name', '$description', $original_price, $offer_price, $discount_percent, 
                  '$image_path', '$badge_text', $rating, $display_order, '$status')";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Service added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add service: ' . mysqli_error($conn)]);
        }
        break;
        
    case 'update_service':
        $id = (int)$_POST['service_id'];
        $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $original_price = (float)$_POST['original_price'];
        $offer_price = (float)$_POST['offer_price'];
        $discount_percent = (int)$_POST['discount_percent'];
        $rating = (float)$_POST['rating'];
        $badge_text = mysqli_real_escape_string($conn, $_POST['badge_text']);
        $display_order = (int)$_POST['display_order'];
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $update_fields = "service_name = '$service_name', description = '$description', 
                         original_price = $original_price, offer_price = $offer_price, 
                         discount_percent = $discount_percent, rating = $rating, 
                         badge_text = '$badge_text', display_order = $display_order, status = '$status'";
        
        // Handle image removal
        if (isset($_POST['remove_image']) && $_POST['remove_image'] == 'on') {
            // Get current image path to delete file
            $result = mysqli_query($conn, "SELECT image_path FROM $table WHERE id = $id");
            $current = mysqli_fetch_assoc($result);
            if ($current['image_path'] && file_exists("../" . $current['image_path'])) {
                unlink("../" . $current['image_path']);
            }
            $update_fields .= ", image_path = ''";
        }
        
        // Handle new image upload
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $upload_dir = "../services_pics/{$service_type}_services_pics/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Delete old image
            $result = mysqli_query($conn, "SELECT image_path FROM $table WHERE id = $id");
            $current = mysqli_fetch_assoc($result);
            if ($current['image_path'] && file_exists("../" . $current['image_path'])) {
                unlink("../" . $current['image_path']);
            }
            
            $file_extension = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
            $file_name = time() . '_' . uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['service_image']['tmp_name'], $target_file)) {
                $image_path = "services_pics/{$service_type}_services_pics/" . $file_name;
                $update_fields .= ", image_path = '$image_path'";
            }
        }
        
        $query = "UPDATE $table SET $update_fields WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Service updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update service: ' . mysqli_error($conn)]);
        }
        break;
        
    case 'delete_service':
        $id = (int)$_POST['service_id'];
        
        // Get image path to delete file
        $result = mysqli_query($conn, "SELECT image_path FROM $table WHERE id = $id");
        $service = mysqli_fetch_assoc($result);
        if ($service['image_path'] && file_exists("../" . $service['image_path'])) {
            unlink("../" . $service['image_path']);
        }
        
        $query = "DELETE FROM $table WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Service deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete service: ' . mysqli_error($conn)]);
        }
        break;
        
    case 'toggle_status':
        $id = (int)$_POST['service_id'];
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $query = "UPDATE $table SET status = '$status' WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
        break;
}
?>