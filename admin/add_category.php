<?php
$page_title = "Add Category";
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

// Handle form submission BEFORE including the layout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'add_category', 'Added category: ' . $name]);
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            if ($isAdmin) {
                header("Location: manage_categories.php?message=" . urlencode("Category added successfully!"));
            } else {
                header("Location: manage_categories.php?message=" . urlencode("Category added successfully!"));
            }
            exit;
        } catch(PDOException $e) {
            $error = "Error adding category: " . $e->getMessage();
        }
    } else {
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
    <h2>Add New Category</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5>Category Details</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="form-text">Example: Amazon - Top Deals, Best Cards for Shopping</div>
                </div>
                <button type="submit" class="btn btn-primary">Save Category</button>
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