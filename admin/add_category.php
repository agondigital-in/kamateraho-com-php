<?php
$page_title = "Add Category";
include '../config/db.php';

// Handle form submission BEFORE including the layout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            header("Location: manage_categories.php?message=" . urlencode("Category added successfully!"));
            exit;
        } catch(PDOException $e) {
            $error = "Error adding category: " . $e->getMessage();
        }
    } else {
        $error = "Category name is required!";
    }
}

include 'includes/admin_layout.php'; // This includes auth check
?>

<div class="container-fluid">
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
                <a href="manage_categories.php" class="btn btn-secondary">Manage Categories</a>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>