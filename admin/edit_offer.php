<?php
session_start();
$page_title = "Edit Offer";
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
    
    // Check if sub-admin has permission for managing offers
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'manage_offers'");
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

// Get offer ID from URL
$offer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$offer_id) {
    header("Location: manage_offers.php");
    exit;
}

// Fetch the offer details
try {
    $stmt = $pdo->prepare("
        SELECT o.*, c.name as category_name 
        FROM offers o 
        LEFT JOIN categories c ON o.category_id = c.id 
        WHERE o.id = ?
    ");
    $stmt->execute([$offer_id]);
    $offer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$offer) {
        $_SESSION['error'] = "Offer not found.";
        header("Location: manage_offers.php");
        exit;
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching offer: " . $e->getMessage();
    header("Location: manage_offers.php");
    exit;
}

// Fetch categories for the dropdown
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching categories: " . $e->getMessage();
    $categories = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $redirect_url = $_POST['redirect_url'];
    
    try {
        // Update the offer
        $stmt = $pdo->prepare("UPDATE offers SET category_id = ?, title = ?, description = ?, price = ?, redirect_url = ? WHERE id = ?");
        $stmt->execute([$category_id, $title, $description, $price, $redirect_url, $offer_id]);
        
        $_SESSION['message'] = "Offer updated successfully!";
        header("Location: edit_offer.php?id=" . $offer_id);
        exit;
    } catch(PDOException $e) {
        $error = "Error updating offer: " . $e->getMessage();
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
    <h2>Edit Offer</h2>
    
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
        <div class="card-header">
            <h5>Offer Details</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Select Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Choose a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $offer['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($offer['title']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($offer['description']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="price" class="form-label">Price (₹)</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $offer['price']; ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="redirect_url" class="form-label">Redirect URL</label>
                    <input type="url" class="form-control" id="redirect_url" name="redirect_url" value="<?php echo htmlspecialchars($offer['redirect_url']); ?>" placeholder="https://example.com">
                    <div class="form-text">Enter the URL where users will be redirected when they click on this offer.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Current Image</label>
                    <div>
                        <?php if (!empty($offer['image'])): ?>
                            <?php 
                            // Determine image source
                            $image_src = '';
                            if (preg_match('/^https?:\/\//i', $offer['image'])) {
                                $image_src = $offer['image'];
                            } else {
                                $image_src = '../' . htmlspecialchars($offer['image']);
                            }
                            ?>
                            <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="max-width: 200px; height: auto;">
                        <?php else: ?>
                            <div class="bg-light" style="width: 200px; height: 150px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Offer</button>
                <a href="manage_offers.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="manage_offers.php" class="btn btn-primary">Back to Manage Offers</a>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>