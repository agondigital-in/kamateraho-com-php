<?php
session_start();
$page_title = "Manage Credit Cards";
include '../config/db.php';
include '../config/app.php'; 

// Check if main admin is logged in
$isAdmin = false;
$isSubAdmin = false;
$subAdminId = null;

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    $isAdmin = true;
} elseif (isset($_SESSION['sub_admin_logged_in']) && $_SESSION['sub_admin_logged_in']) {
    $isSubAdmin = true;
    $subAdminId = $_SESSION['sub_admin_id'];
    
    // Check if sub-admin has permission for managing credit cards
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'manage_credit_cards'");
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
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_card'])) {
        $title = $_POST['title'];
        $link = $_POST['link'];
        $amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
        $percentage = isset($_POST['percentage']) ? $_POST['percentage'] : 0;
        $flat_rate = isset($_POST['flat_rate']) ? $_POST['flat_rate'] : 0;
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Set upload directory outside admin
            $upload_dir = '../uploads/credit_cards/';

            // Ensure directory exists with proper permissions
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error = "Failed to create upload directory. Please check permissions.";
                }
            }

            // If directory exists but is not writable, try to fix permissions
            if (empty($error) && !is_writable($upload_dir)) {
                @chmod($upload_dir, 0755);
                if (!is_writable($upload_dir)) {
                    $error = "Upload directory is not writable (" . $upload_dir . "). Please check permissions.";
                }
            }

            // Continue only if no error occurred
            if (empty($error)) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;

                // Check if file is a valid image
                $image_info = getimagesize($_FILES['image']['tmp_name']);
                if ($image_info !== false) {
                    ob_start();
                    $upload_result = move_uploaded_file($_FILES['image']['tmp_name'], $upload_path);
                    $upload_output = ob_get_clean();

                    if ($upload_result) {
                        // Store relative path for database storage
                        $image_path = 'uploads/credit_cards/' . $filename;
                        try {
                            $stmt = $pdo->prepare("INSERT INTO credit_cards (title, image, link, amount, percentage, flat_rate, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$title, $image_path, $link, $amount, $percentage, $flat_rate, $is_active]);
                            
                            // Log activity for sub-admin
                            if ($isSubAdmin) {
                                try {
                                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                                    $activityStmt->execute([$subAdminId, 'add_credit_card', 'Added credit card: ' . $title]);
                                } catch (PDOException $e) {
                                    // Silently fail on activity logging
                                }
                            }
                            
                            echo "<script>window.location.href = 'manage_credit_cards.php?success=" . urlencode("Credit card added successfully!") . "';</script>";
                            exit;
                        } catch(PDOException $e) {
                            if (file_exists($upload_path)) {
                                unlink($upload_path);
                            }
                            $error = "Error adding credit card: " . $e->getMessage();
                        }
                    } else {
                        $error = "Error uploading image. Please check directory permissions.";
                        if (!empty($upload_output)) {
                            error_log("Upload error output: " . $upload_output);
                        }
                        error_log("Failed to move uploaded file to: " . $upload_path);
                    }
                } else {
                    $error = "Invalid image file. Please upload a valid image.";
                }
            }
        } else {
            $error = "Please select an image.";
        }
    } elseif (isset($_POST['delete_card'])) {
        $id = $_POST['id'];
        try {
            // First get the image path to delete the file
            $stmt = $pdo->prepare("SELECT image FROM credit_cards WHERE id = ?");
            $stmt->execute([$id]);
            $card = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($card) {
                // Delete the image file using the proper path
                $image_path = '../' . $card['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM credit_cards WHERE id = ?");
                $stmt->execute([$id]);
                
                // Log activity for sub-admin
                if ($isSubAdmin) {
                    try {
                        $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                        $activityStmt->execute([$subAdminId, 'delete_credit_card', 'Deleted credit card ID: ' . $id]);
                    } catch (PDOException $e) {
                        // Silently fail on activity logging
                    }
                }
                
                // Use JavaScript redirect to avoid headers already sent error
                echo "<script>window.location.href = 'manage_credit_cards.php?success=" . urlencode("Credit card deleted successfully!") . "';</script>";
                exit;
            }
        } catch(PDOException $e) {
            $error = "Error deleting credit card: " . $e->getMessage();
        }
    } elseif (isset($_POST['toggle_active'])) {
        $id = $_POST['id'];
        $is_active = $_POST['is_active'];
        try {
            $stmt = $pdo->prepare("UPDATE credit_cards SET is_active = ? WHERE id = ?");
            $stmt->execute([$is_active, $id]);
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'toggle_credit_card', 'Toggled credit card ID: ' . $id . ' to ' . ($is_active ? 'active' : 'inactive')]);
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            // Use JavaScript redirect to avoid headers already sent error
            echo "<script>window.location.href = 'manage_credit_cards.php?success=" . urlencode("Credit card status updated successfully!") . "';</script>";
            exit;
        } catch(PDOException $e) {
            $error = "Error updating credit card status: " . $e->getMessage();
        }
    }
}

// Check for success message in URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

// Include admin layout only for main admin
if ($isAdmin) {
    include 'includes/admin_layout.php';
}

// Fetch all credit cards
try {
    $stmt = $pdo->query("SELECT * FROM credit_cards ORDER BY created_at DESC");
    $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching credit cards: " . $e->getMessage();
    $credit_cards = [];
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
    <h2 class="mb-4">Manage Credit Cards</h2>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Add Credit Card Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Add New Credit Card</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Card Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="link" class="form-label">Link URL</label>
                            <input type="url" class="form-control" id="link" name="link" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount (₹)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="percentage" class="form-label">Percentage (%)</label>
                                    <input type="number" class="form-control" id="percentage" name="percentage" step="0.01" min="0" max="100" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="flat_rate" class="form-label">Flat Rate (₹)</label>
                                    <input type="number" class="form-control" id="flat_rate" name="flat_rate" step="0.01" min="0" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label">Card Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                            <div class="form-text">Recommended size: 300x200 pixels</div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="add_card" class="btn btn-primary">Add Credit Card</button>
            </form>
        </div>
    </div>
    
    <!-- Credit Cards List -->
    <div class="card">
        <div class="card-header">
            <h5>Credit Cards</h5>
        </div>
        <div class="card-body">
            <?php if (empty($credit_cards)): ?>
                <p>No credit cards found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Amount Details</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($credit_cards as $card): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($card['image'])): ?>
                                            <img src="../<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" style="width: 100px; height: auto;">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($card['title']); ?></td>
                                    <td>
                                        <?php if ($card['amount'] > 0): ?>
                                            <div>Amount: ₹<?php echo number_format($card['amount'], 2); ?></div>
                                        <?php endif; ?>
                                        <?php if ($card['percentage'] > 0): ?>
                                            <div>Percentage: <?php echo number_format($card['percentage'], 2); ?>%</div>
                                        <?php endif; ?>
                                        <?php if ($card['flat_rate'] > 0): ?>
                                            <div>Flat Rate: ₹<?php echo number_format($card['flat_rate'], 2); ?></div>
                                        <?php endif; ?>
                                        <?php if ($card['amount'] == 0 && $card['percentage'] == 0 && $card['flat_rate'] == 0): ?>
                                            <span class="text-muted">No amount details</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($card['link'])): ?>
                                            <a href="<?php echo htmlspecialchars($card['link']); ?>" target="_blank">View Link</a>
                                        <?php else: ?>
                                            <span class="text-muted">No link</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($card['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                            <input type="hidden" name="is_active" value="<?php echo $card['is_active'] ? 0 : 1; ?>">
                                            <button type="submit" name="toggle_active" class="btn btn-sm btn-<?php echo $card['is_active'] ? 'warning' : 'success'; ?>">
                                                <?php echo $card['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this credit card?');">
                                            <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                            <button type="submit" name="delete_card" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>