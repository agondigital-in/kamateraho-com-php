<?php
$page_title = "Edit Category";
include '../config/db.php';
include 'subadmin_auth.php'; // Sub-admin authentication check

// Check permissions for sub-admin
if ($isSubAdmin) {
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'add_new_category'");
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
}

// Get category ID from URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch category details
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        header("Location: manage_categories.php?error=" . urlencode("Category not found!"));
        exit;
    }
} catch(PDOException $e) {
    header("Location: manage_categories.php?error=" . urlencode("Error fetching category: " . $e->getMessage()));
    exit;
}

// Handle form submission BEFORE including the layout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = !empty($_POST['price']) ? $_POST['price'] : null;
    $photo = $category['photo']; // Keep existing photo by default
    
    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/categories/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $uploadFile = $uploadDir . $fileName;
        
        // Check if file is an image
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                // Delete old photo if it exists
                if (!empty($category['photo']) && file_exists('../' . $category['photo'])) {
                    unlink('../' . $category['photo']);
                }
                $photo = 'uploads/categories/' . $fileName;
            } else {
                $error = "Error uploading photo file.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }
    
    if (!empty($name) && !isset($error)) {
        try {
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, price = ?, photo = ? WHERE id = ?");
            $stmt->execute([$name, $price, $photo, $category_id]);
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'edit_category', 'Edited category: ' . $name]);
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            if ($isAdmin) {
                header("Location: manage_categories.php?message=" . urlencode("Category updated successfully!"));
            } else {
                header("Location: manage_categories.php?message=" . urlencode("Category updated successfully!"));
            }
            exit;
        } catch(PDOException $e) {
            $error = "Error updating category: " . $e->getMessage();
        }
    } else if (!isset($error)) {
        $error = "Category name is required!";
    }
}

// Include appropriate layout based on user type
if ($isAdmin) {
    include 'includes/admin_layout.php';
} else {
    include 'subadmin_header.php';
}
?>

<?php if ($isAdmin): ?>
<div class="container-fluid">
<?php else: ?>
<!-- Content is already started in subadmin_header.php -->
<?php endif; ?>
    <h2>Edit Category</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5>Category Details</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    <div class="form-text">Example: Amazon - Top Deals, Best Cards for Shopping</div>
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label">Price (Optional)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($category['price'] ?? ''); ?>">
                    <div class="form-text">Enter the price for this category if applicable</div>
                </div>
                
                <div class="mb-3">
                    <label for="photo" class="form-label">Category Photo (Optional)</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    <?php if (!empty($category['photo'])): ?>
                        <div class="mt-2">
                            <p>Current Photo:</p>
                            <img src="../<?php echo htmlspecialchars($category['photo']); ?>" alt="Current Photo" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                    <div class="form-text">Upload a new photo image for this category (JPG, PNG, GIF)</div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Category</button>
                <?php if ($isAdmin): ?>
                <a href="manage_categories.php" class="btn btn-secondary">Manage Categories</a>
                <?php else: ?>
                <a href="manage_categories.php" class="btn btn-secondary">Manage Categories</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>