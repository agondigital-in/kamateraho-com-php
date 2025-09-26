<?php
$page_title = "Manage Categories";
include '../config/db.php';

// Handle category deletion - THIS MUST BE BEFORE INCLUDING THE LAYOUT
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$delete_id]);
        header("Location: manage_categories.php?message=" . urlencode("Category deleted successfully!"));
        exit;
    } catch(PDOException $e) {
        $error = "Error deleting category: " . $e->getMessage();
    }
}

include 'includes/admin_layout.php'; // This includes auth check

// Fetch all categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching categories: " . $e->getMessage();
    $categories = [];
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Categories</h2>
        <a href="add_category.php" class="btn btn-primary">Add New Category</a>
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
</div>

<?php include 'includes/admin_footer.php'; ?>