<?php
$page_title = "Manage Categories";
include '../config/db.php';
include 'subadmin_auth.php'; // Sub-admin authentication check

// Check permissions for sub-admin
if ($isSubAdmin) {
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'all_categories'");
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

// Handle category deletion - THIS MUST BE BEFORE INCLUDING THE LAYOUT
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$delete_id]);
        
        // Log activity for sub-admin
        if ($isSubAdmin) {
            try {
                $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                $activityStmt->execute([$subAdminId, 'delete_category', 'Deleted category ID: ' . $delete_id]);
            } catch (PDOException $e) {
                // Silently fail on activity logging
            }
        }
        
        if ($isAdmin) {
            header("Location: manage_categories.php?message=" . urlencode("Category deleted successfully!"));
        } else {
            header("Location: manage_categories.php?message=" . urlencode("Category deleted successfully!"));
        }
        exit;
    } catch(PDOException $e) {
        $error = "Error deleting category: " . $e->getMessage();
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Categories</h2>
        <?php if ($isAdmin): ?>
        <a href="add_category.php" class="btn btn-primary">Add New Category</a>
        <?php else: ?>
        <a href="add_category.php" class="btn btn-primary">Add New Category</a>
        <?php endif; ?>
    </div>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (empty($categories)): ?>
        <div class="alert alert-info">No categories found. <a href="add_category.php">Add your first category</a>.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h5>All Categories</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['id']); ?></td>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($category['created_at'])); ?></td>
                                    <td>
                                        <a href="?delete_id=<?php echo $category['id']; ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Are you sure you want to delete this category? This will also delete all offers in this category.')">
                                            ❌ Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>