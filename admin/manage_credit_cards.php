<?php
// Start session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Manage Credit Cards";
include '../config/db.php';
include 'includes/admin_layout.php';

// Handle form submission
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_card'])) {
        $title = $_POST['title'];
        $link = $_POST['link'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/credit_cards/';
            
            // Ensure directory exists with proper permissions
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error = "Failed to create upload directory.";
                }
            }
            
            // Check if directory is writable
            if (!is_writable($upload_dir)) {
                $error = "Upload directory is not writable. Please check permissions.";
            }
            
            if (empty($error)) {
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                // Debug information
                error_log("Attempting to move file from: " . $_FILES['image']['tmp_name'] . " to: " . $upload_path);
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'uploads/credit_cards/' . $filename;
                    
                    try {
                        $stmt = $pdo->prepare("INSERT INTO credit_cards (title, image, link, is_active) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$title, $image_path, $link, $is_active]);
                        header("Location: manage_credit_cards.php?success=" . urlencode("Credit card added successfully!"));
                        exit;
                    } catch(PDOException $e) {
                        $error = "Error adding credit card: " . $e->getMessage();
                        // Delete the uploaded file if database insert fails
                        if (file_exists($upload_path)) {
                            unlink($upload_path);
                        }
                    }
                } else {
                    $error = "Error uploading image. Check directory permissions.";
                    // Log detailed error information
                    error_log("Failed to move uploaded file. Upload errors: " . print_r($_FILES['image'], true));
                }
            }
        } else {
            $error = "Please select an image.";
            if (isset($_FILES['image'])) {
                $error .= " Upload error code: " . $_FILES['image']['error'];
            }
        }
    } elseif (isset($_POST['delete_card'])) {
        $id = $_POST['id'];
        try {
            // First get the image path to delete the file
            $stmt = $pdo->prepare("SELECT image FROM credit_cards WHERE id = ?");
            $stmt->execute([$id]);
            $card = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($card) {
                // Delete the image file
                $image_path = '../' . $card['image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
                
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM credit_cards WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: manage_credit_cards.php?success=" . urlencode("Credit card deleted successfully!"));
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
            header("Location: manage_credit_cards.php?success=" . urlencode("Credit card status updated successfully!"));
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

// Fetch all credit cards
try {
    $stmt = $pdo->query("SELECT * FROM credit_cards ORDER BY created_at DESC");
    $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching credit cards: " . $e->getMessage();
    $credit_cards = [];
}
?>

<div class="container-fluid">
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
                                <th>Link</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($credit_cards as $card): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" style="width: 100px; height: auto;">
                                    </td>
                                    <td><?php echo htmlspecialchars($card['title']); ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($card['link']); ?>" target="_blank">View Link</a>
                                    </td>
                                    <td>
                                        <?php if ($card['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($card['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                            <input type="hidden" name="is_active" value="<?php echo $card['is_active'] ? 0 : 1; ?>">
                                            <button type="submit" name="toggle_active" class="btn btn-sm btn-outline-primary">
                                                <?php echo $card['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $card['id']; ?>">
                                            <button type="submit" name="delete_card" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this credit card?')">
                                                Delete
                                            </button>
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
</div>

<?php include 'includes/admin_footer.php'; ?>