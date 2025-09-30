<?php
session_start();
$page_title = "Upload Offer";
include '../config/db.php';
include '../config/app.php'; // Include app config to access UPLOAD_PATH

// Check if main admin is logged in
$isAdmin = false;
$isSubAdmin = false;
$subAdminId = null;

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    $isAdmin = true;
} elseif (isset($_SESSION['sub_admin_logged_in']) && $_SESSION['sub_admin_logged_in']) {
    $isSubAdmin = true;
    $subAdminId = $_SESSION['sub_admin_id'];
    
    // Check if sub-admin has permission for uploading offers
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'upload_offer'");
        $stmt->execute([$subAdminId]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$permission || !$permission['allowed']) {
            // Redirect to sub-admin dashboard if no permission
            header("Location: subadmin_dashboard.php");
            exit;
        }
    } catch (PDOException $e) {
        // Redirect on error
        header("Location: subadmin_dashboard.php");
        exit;
    }
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

// Handle form submission BEFORE including the layout
$message = '';
$error = ''; // Initialize error variable
// Start output buffering early to prevent any PHP warnings/notices from breaking headers
ob_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $redirect_url = $_POST['redirect_url'];
    
    // Handle file uploads
    $uploaded_images = [];
    
    // Process multiple image uploads
    if (isset($_FILES['images'])) {
        // Use helper to resolve a safe, absolute filesystem path for the offers upload directory
        $upload_dir = rtrim(upload_dir('offers'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Ensure the directory exists
        if (!is_dir($upload_dir)) {
            if (!@mkdir($upload_dir, 0777, true)) {
                $error = 'Failed to create upload directory: ' . htmlspecialchars($upload_dir) . '. Please check folder permissions on your server. You may need to manually create this folder and set permissions to 0777 or 0755. <br><br>You can also try running the <a href="../fix_upload_permissions.php" target="_blank">permission fix script</a> to automatically resolve this issue.';
            }
        }

        // Check if directory is writable
        if (empty($error) && !is_writable($upload_dir)) {
            $error = 'Upload directory is not writable. Please check permissions. <br><br>You can try running the <a href="../fix_upload_permissions.php" target="_blank">permission fix script</a> to automatically resolve this issue.';
        }
        
        // Continue only if no error occurred
        if (empty($error)) {
            $files = $_FILES['images'];
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_name = time() . '_' . $i . '_' . basename($files['name'][$i]);
                    $target_file = $upload_dir . $file_name;
                    
                    // Allow certain file formats
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($imageFileType, $allowed_types)) {
                        // Buffer output to prevent headers already sent error
                        ob_start();
                        $upload_result = move_uploaded_file($files['tmp_name'][$i], $target_file);
                        $upload_output = ob_get_clean();
                        
                        if ($upload_result) {
                            // Store web-relative path for database storage so it works across frontend pages
                            $uploaded_images[] = 'uploads/offers/' . $file_name;
                        } else {
                            $error = "Error uploading image. Please check directory permissions.";
                            // Log the actual error for debugging
                            if (!empty($upload_output)) {
                                error_log("Upload error output: " . $upload_output);
                            }
                            error_log("Failed to move uploaded file to: " . $target_file);
                        }
                    }
                }
            }
        }
    }
    
    if (empty($error) && !empty($category_id) && !empty($title) && !empty($price) && !empty($uploaded_images)) {
        try {
            // Insert the main offer
            $stmt = $pdo->prepare("INSERT INTO offers (category_id, title, description, price, image, redirect_url) VALUES (?, ?, ?, ?, ?, ?)");
            // Use the first image as the main image
            $main_image = $uploaded_images[0];
            $stmt->execute([$category_id, $title, $description, $price, $main_image, $redirect_url]);
            
            // Get the ID of the inserted offer
            $offer_id = $pdo->lastInsertId();
            
            // Insert all images into the offer_images table
            foreach ($uploaded_images as $image_path) {
                $stmt = $pdo->prepare("INSERT INTO offer_images (offer_id, image_path) VALUES (?, ?)");
                $stmt->execute([$offer_id, $image_path]);
            }
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'upload_offer', 'Uploaded offer: ' . $title]);
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            // Use JavaScript redirect to avoid headers already sent error
            echo "<script>window.location.href = 'upload_offer.php?message=" . urlencode("Offer uploaded successfully with " . count($uploaded_images) . " images!") . "';</script>";
            exit;
        } catch(PDOException $e) {
            // Delete uploaded files if database operation fails
            foreach ($uploaded_images as $image_path) {
                $full_path = __DIR__ . '/../' . $image_path;
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            }
            $error = "Error uploading offer: " . $e->getMessage();
        }
    } elseif (empty($error)) {
        if (empty($uploaded_images)) {
            $error = "At least one image is required!";
        } else {
            $error = "Category, Title, and Price are required!";
        }
    }
}

// Fetch categories for the dropdown
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching categories: " . $e->getMessage();
    $categories = [];
}

// Include admin layout only for main admin
if ($isAdmin) {
    include 'includes/admin_layout.php';
}

// For sub-admin, use the new sidebar layout
if ($isSubAdmin) {
    include 'subadmin_header.php';
}
?>

<?php if ($isAdmin): ?>
<div class="container-fluid">
<?php else: ?>
<!-- Content is already started in subadmin_header.php -->
<?php endif; ?>
    <h2>Upload New Offer</h2>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5>Offer Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Select Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Choose a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label">Price (₹)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                </div>
                
                <div class="mb-3">
                    <label for="redirect_url" class="form-label">Redirect URL</label>
                    <input type="url" class="form-control" id="redirect_url" name="redirect_url" placeholder="https://example.com">
                    <div class="form-text">Enter the URL where users will be redirected when they click on this offer.</div>
                </div>
                
                <div class="mb-3">
                    <label for="images" class="form-label">Upload Images</label>
                    <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple required>
                    <div class="form-text">You can select multiple images. Hold Ctrl (or Cmd on Mac) to select multiple files.</div>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Offer</button>
                <?php if ($isAdmin): ?>
                <a href="../index.php" class="btn btn-secondary">View User Dashboard</a>
                <?php endif; ?>
                <a href="manage_offers.php" class="btn btn-info">Manage All Offers</a>
            </form>
        </div>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>