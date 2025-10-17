<?php
session_start();
$page_title = "Edit Credit Card";
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

// Get credit card ID from URL
$card_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$card_id) {
    header("Location: manage_credit_cards.php");
    exit;
}

// Fetch the credit card details
try {
    $stmt = $pdo->prepare("SELECT * FROM credit_cards WHERE id = ?");
    $stmt->execute([$card_id]);
    $card = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$card) {
        $_SESSION['error'] = "Credit card not found.";
        header("Location: manage_credit_cards.php");
        exit;
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching credit card: " . $e->getMessage();
    header("Location: manage_credit_cards.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $link = $_POST['link'];
    $sequence_id = $_POST['sequence_id'];
    $amount = isset($_POST['amount']) ? $_POST['amount'] : 0;
    $percentage = isset($_POST['percentage']) ? $_POST['percentage'] : 0;
    $flat_rate = isset($_POST['flat_rate']) ? $_POST['flat_rate'] : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    try {
        // Handle image upload if a new image is provided
        $image_path = $card['image']; // Keep existing image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = '../uploads/credit_cards/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_extension;
            $target_file = $upload_dir . $file_name;
            
            // Check if file is an actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    // Delete old image if it exists and is different
                    if (!empty($image_path) && file_exists('../' . $image_path) && $image_path !== 'uploads/credit_cards/' . $file_name) {
                        unlink('../' . $image_path);
                    }
                    $image_path = 'uploads/credit_cards/' . $file_name;
                } else {
                    throw new Exception("Sorry, there was an error uploading your file.");
                }
            } else {
                throw new Exception("File is not an image.");
            }
        }
        
        // Update the credit card
        $stmt = $pdo->prepare("UPDATE credit_cards SET title = ?, description = ?, image = ?, link = ?, sequence_id = ?, amount = ?, percentage = ?, flat_rate = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image_path, $link, $sequence_id, $amount, $percentage, $flat_rate, $is_active, $card_id]);
        
        // Log activity for sub-admin
        if ($isSubAdmin) {
            try {
                $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                $activityStmt->execute([$subAdminId, 'edit_credit_card', 'Edited credit card: ' . $title]);
            } catch (PDOException $e) {
                // Silently fail on activity logging
            }
        }
        
        $_SESSION['message'] = "Credit card updated successfully!";
        header("Location: edit_credit_card.php?id=" . $card_id);
        exit;
    } catch(PDOException $e) {
        $error = "Error updating credit card: " . $e->getMessage();
    } catch(Exception $e) {
        $error = "Error uploading image: " . $e->getMessage();
    }
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
    <h2>Edit Credit Card</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Credit Card Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Card Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($card['title']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($card['description']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="link" class="form-label fw-bold">Link URL</label>
                            <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($card['link']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sequence_id" class="form-label fw-bold">Sequence ID</label>
                            <input type="number" class="form-control" id="sequence_id" name="sequence_id" value="<?php echo $card['sequence_id']; ?>" min="0">
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="amount" class="form-label fw-bold">Amount (₹)</label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" value="<?php echo $card['amount']; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="percentage" class="form-label fw-bold">Percentage (%)</label>
                                    <input type="number" class="form-control" id="percentage" name="percentage" step="0.01" min="0" max="100" value="<?php echo $card['percentage']; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="flat_rate" class="form-label fw-bold">Flat Rate (₹)</label>
                                    <input type="number" class="form-control" id="flat_rate" name="flat_rate" step="0.01" min="0" value="<?php echo $card['flat_rate']; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?php echo $card['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold" for="is_active">Active</label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Card Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Leave blank to keep the current image. Upload a new image to replace it.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Current Image</label>
                            <div class="border rounded p-2 text-center" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                <?php if (!empty($card['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" style="max-height: 100%; width: auto; object-fit: contain;">
                                <?php else: ?>
                                    <span class="text-muted">No image available</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary btn-lg">Update Credit Card</button>
                    <a href="manage_credit_cards.php" class="btn btn-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="manage_credit_cards.php" class="btn btn-primary">Back to Manage Credit Cards</a>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>