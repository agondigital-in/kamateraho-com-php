<?php
session_start();
$page_title = "Manage Offers";
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

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $offer_id = $_GET['id'];
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Delete associated images from offer_images table
        $stmt = $pdo->prepare("DELETE FROM offer_images WHERE offer_id = ?");
        $stmt->execute([$offer_id]);
        
        // Delete the offer
        $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
        $stmt->execute([$offer_id]);
        
        // Commit transaction
        $pdo->commit();
        
        // Set success message
        $_SESSION['message'] = "Offer deleted successfully!";
        header("Location: manage_offers.php");
        exit;
    } catch(PDOException $e) {
        // Rollback transaction on error
        $pdo->rollback();
        $_SESSION['error'] = "Error deleting offer: " . $e->getMessage();
    }
}

// Fetch all offers with category names
try {
    $stmt = $pdo->query("
        SELECT o.*, c.name as category_name 
        FROM offers o 
        LEFT JOIN categories c ON o.category_id = c.id 
        ORDER BY o.created_at DESC
    ");
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching offers: " . $e->getMessage();
    $offers = [];
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
    <h2>Manage Offers</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($offers)): ?>
        <div class="alert alert-info">No offers found. <a href="upload_offer.php">Upload your first offer</a>.</div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h5>All Offers</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Price (₹)</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offers as $offer): ?>
                            <tr>
                                <td><?php echo $offer['id']; ?></td>
                                <td>
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
                                        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($offer['title']); ?></td>
                                <td><?php echo htmlspecialchars($offer['category_name'] ?? 'N/A'); ?></td>
                                <td>₹<?php echo number_format($offer['price'], 2); ?></td>
                                <td><?php echo date('M j, Y', strtotime($offer['created_at'])); ?></td>
                                <td>
                                    <a href="edit_offer.php?id=<?php echo $offer['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="manage_offers.php?action=delete&id=<?php echo $offer['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this offer? This action cannot be undone.')">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="mt-3">
        <a href="upload_offer.php" class="btn btn-primary">Upload New Offer</a>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>