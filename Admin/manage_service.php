<?php
session_start();
require_once '../dbcon.php';

// Check admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['type'] !== 'administrator') {
    header('location: login.php');
    exit;
}

// Get service type from URL
$service_type = isset($_GET['type']) && $_GET['type'] == 'two_wheeler' ? 'two_wheeler' : 'four_wheeler';
$table_name = $service_type == 'two_wheeler' ? 'two_wheeler_services' : 'four_wheeler_services';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage <?php echo ucwords(str_replace('_', ' ', $service_type)); ?> Services - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #0a0a0f;
            color: white;
        }
        .sidebar {
            background: #11111a;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            padding: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .service-card {
            background: rgba(20, 20, 30, 0.95);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: 0.3s;
        }
        .service-card:hover {
            transform: translateY(-5px);
            border-color: #0d6efd;
            box-shadow: 0 0 20px rgba(13,110,253,0.3);
        }
        .service-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .btn-group-custom {
            gap: 10px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background: rgba(32, 178, 170, 0.2);
            color: #20B2AA;
            border: 1px solid #20B2AA;
        }
        .status-inactive {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .modal-content {
            background: #1a1a2a;
            color: white;
        }
        .form-control, .form-select {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.15);
            color: white;
            border-color: #0d6efd;
        }
        .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 10px 15px;
            border-radius: 10px;
            transition: 0.3s;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(13,110,253,0.1);
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center mb-4">Admin Panel</h4>
        <nav>
            <a href="admin_home.php" class="nav-link d-block mb-2">
                <i class="fas fa-dashboard"></i> Dashboard
            </a>
            <a href="manage_services.php?type=four_wheeler" class="nav-link d-block mb-2 <?php echo $service_type == 'four_wheeler' ? 'active' : ''; ?>">
                <i class="fas fa-car"></i> Four Wheeler Services
            </a>
            <a href="manage_services.php?type=two_wheeler" class="nav-link d-block mb-2 <?php echo $service_type == 'two_wheeler' ? 'active' : ''; ?>">
                <i class="fas fa-motorcycle"></i> Two Wheeler Services
            </a>
            <a href="manage_bookings.php" class="nav-link d-block mb-2">
                <i class="fas fa-calendar-check"></i> Bookings
            </a>
            <a href="logout.php" class="nav-link d-block mt-4">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-<?php echo $service_type == 'four_wheeler' ? 'car' : 'motorcycle'; ?>"></i> 
                Manage <?php echo ucwords(str_replace('_', ' ', $service_type)); ?> Services</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                <i class="fas fa-plus"></i> Add New Service
            </button>
        </div>

        <!-- Service List -->
        <div class="row" id="servicesList">
            <!-- Services will be loaded here -->
        </div>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addServiceForm" enctype="multipart/form-data">
                        <input type="hidden" name="service_type" value="<?php echo $service_type; ?>">
                        <div class="mb-3">
                            <label>Service Name *</label>
                            <input type="text" name="service_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description *</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Original Price (₹) *</label>
                                <input type="number" name="original_price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Offer Price (₹) *</label>
                                <input type="number" name="offer_price" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Discount Percent (%)</label>
                                <input type="number" name="discount_percent" class="form-control" step="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Rating (1-5)</label>
                                <input type="number" name="rating" class="form-control" step="0.1" min="0" max="5" value="4.5">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Badge Text (Optional)</label>
                            <input type="text" name="badge_text" class="form-control" placeholder="e.g., Popular, Best Seller">
                        </div>
                        <div class="mb-3">
                            <label>Service Image</label>
                            <input type="file" name="service_image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label>Display Order</label>
                            <input type="number" name="display_order" class="form-control" value="0">
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="addService()">Add Service</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm" enctype="multipart/form-data">
                        <input type="hidden" name="service_id" id="edit_service_id">
                        <input type="hidden" name="service_type" value="<?php echo $service_type; ?>">
                        <div class="mb-3">
                            <label>Service Name *</label>
                            <input type="text" name="service_name" id="edit_service_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description *</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Original Price (₹) *</label>
                                <input type="number" name="original_price" id="edit_original_price" class="form-control" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Offer Price (₹) *</label>
                                <input type="number" name="offer_price" id="edit_offer_price" class="form-control" step="0.01" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Discount Percent (%)</label>
                                <input type="number" name="discount_percent" id="edit_discount_percent" class="form-control" step="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Rating (1-5)</label>
                                <input type="number" name="rating" id="edit_rating" class="form-control" step="0.1" min="0" max="5">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Badge Text (Optional)</label>
                            <input type="text" name="badge_text" id="edit_badge_text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Current Image</label>
                            <div id="current_image_container"></div>
                            <label>Change Image (Optional)</label>
                            <input type="file" name="service_image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label>Display Order</label>
                            <input type="number" name="display_order" id="edit_display_order" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateService()">Update Service</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentServiceType = '<?php echo $service_type; ?>';

        // Load services on page load
        $(document).ready(function() {
            loadServices();
        });

        function loadServices() {
            $.ajax({
                url: 'ajax_services.php',
                method: 'POST',
                data: { action: 'get_services', service_type: currentServiceType },
                success: function(response) {
                    $('#servicesList').html(response);
                }
            });
        }

        function addService() {
            const formData = new FormData(document.getElementById('addServiceForm'));
            formData.append('action', 'add_service');
            
            $.ajax({
                url: 'ajax_services.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#addServiceModal').modal('hide');
                        document.getElementById('addServiceForm').reset();
                        loadServices();
                        showAlert('success', result.message);
                    } else {
                        showAlert('danger', result.message);
                    }
                }
            });
        }

        function editService(id) {
            $.ajax({
                url: 'ajax_services.php',
                method: 'POST',
                data: { action: 'get_service', service_id: id, service_type: currentServiceType },
                success: function(response) {
                    const service = JSON.parse(response);
                    $('#edit_service_id').val(service.id);
                    $('#edit_service_name').val(service.service_name);
                    $('#edit_description').val(service.description);
                    $('#edit_original_price').val(service.original_price);
                    $('#edit_offer_price').val(service.offer_price);
                    $('#edit_discount_percent').val(service.discount_percent);
                    $('#edit_rating').val(service.rating);
                    $('#edit_badge_text').val(service.badge_text);
                    $('#edit_display_order').val(service.display_order);
                    $('#edit_status').val(service.status);
                    
                    if (service.image_path) {
                        $('#current_image_container').html(`
                            <div class="mb-2">
                                <img src="../${service.image_path}" style="max-width: 150px; max-height: 150px;" class="rounded">
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="remove_image" id="remove_image" class="form-check-input">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            </div>
                        `);
                    } else {
                        $('#current_image_container').html('<p>No image currently set</p>');
                    }
                    
                    $('#editServiceModal').modal('show');
                }
            });
        }

        function updateService() {
            const formData = new FormData(document.getElementById('editServiceForm'));
            formData.append('action', 'update_service');
            
            $.ajax({
                url: 'ajax_services.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        $('#editServiceModal').modal('hide');
                        loadServices();
                        showAlert('success', result.message);
                    } else {
                        showAlert('danger', result.message);
                    }
                }
            });
        }

        function deleteService(id) {
            if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                $.ajax({
                    url: 'ajax_services.php',
                    method: 'POST',
                    data: { action: 'delete_service', service_id: id, service_type: currentServiceType },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            loadServices();
                            showAlert('success', result.message);
                        } else {
                            showAlert('danger', result.message);
                        }
                    }
                });
            }
        }

        function toggleStatus(id, currentStatus) {
            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            if (confirm(`Are you sure you want to ${newStatus} this service?`)) {
                $.ajax({
                    url: 'ajax_services.php',
                    method: 'POST',
                    data: { action: 'toggle_status', service_id: id, status: newStatus, service_type: currentServiceType },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            loadServices();
                            showAlert('success', `Service ${newStatus}d successfully`);
                        } else {
                            showAlert('danger', result.message);
                        }
                    }
                });
            }
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(alertHtml);
            setTimeout(() => {
                $('.alert').fadeOut('slow', function() { $(this).remove(); });
            }, 3000);
        }
    </script>
</body>
</html>