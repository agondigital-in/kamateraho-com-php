<?php
session_start();
$page_title = "Manage Offers";
include '../config/db.php';
include '../config/app.php';
include '../includes/price_helper.php'; // Include price helper functions

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

// Handle activate/deactivate request
if (isset($_GET['action']) && in_array($_GET['action'], ['activate', 'deactivate']) && isset($_GET['id'])) {
    $offer_id = $_GET['id'];
    $is_active = ($_GET['action'] == 'activate') ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE offers SET is_active = ? WHERE id = ?");
        $stmt->execute([$is_active, $offer_id]);
        
        $_SESSION['message'] = "Offer " . ($_GET['action'] == 'activate' ? 'activated' : 'deactivated') . " successfully!";
        header("Location: manage_offers.php");
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating offer status: " . $e->getMessage();
    }
}

// Fetch all offers with category names
try {
    $stmt = $pdo->query("
        SELECT o.*, c.name as category_name 
        FROM offers o 
        LEFT JOIN categories c ON o.category_id = c.id 
        ORDER BY o.sequence_id ASC, o.created_at DESC
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Offers</h2>
        <a href="upload_offer.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Offer
        </a>
    </div>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger rounded-3 shadow-sm">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($offers)): ?>
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body text-center py-5">
                <i class="bi bi-tag-fill text-muted" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No Offers Found</h4>
                <p class="text-muted">Get started by creating your first offer.</p>
                <a href="upload_offer.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Upload Offer
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 rounded-top-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2 text-primary"></i>All Offers
                    </h5>
                    <div class="d-flex">
                        <div class="input-group input-group-sm me-2" style="max-width: 250px;">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="offerSearch" placeholder="Search offers...">
                        </div>
                        <button class="btn btn-outline-secondary btn-sm" type="button" id="refreshBtn">
                            <i class="bi bi-arrow-repeat"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-4 py-3">ID</th>
                                <th scope="col" class="px-4 py-3">Image</th>
                                <th scope="col" class="px-4 py-3">Category</th>
                                <th scope="col" class="px-4 py-3">Price</th>
                                <th scope="col" class="px-4 py-3">Sequence</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                                <th scope="col" class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offers as $offer): ?>
                            <tr class="align-middle">
                                <td class="px-4 py-3 fw-bold"><?php echo $offer['id']; ?></td>
                                <td class="px-4 py-3">
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
                                        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-info-subtle text-info-emphasis">
                                        <?php echo htmlspecialchars($offer['category_name'] ?? 'N/A'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="fw-bold text-success">
                                        <?php echo display_price($offer['price'], $offer['price_type'] ?? 'fixed'); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-primary-subtle text-primary-emphasis">
                                        <?php echo $offer['sequence_id']; ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($offer['is_active']): ?>
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            <i class="bi bi-check-circle-fill me-1"></i>Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            <i class="bi bi-x-circle-fill me-1"></i>Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <?php if ($offer['is_active']): ?>
                                            <a href="manage_offers.php?action=deactivate&id=<?php echo $offer['id']; ?>" class="btn btn-sm btn-outline-warning" title="Deactivate">
                                                <i class="bi bi-eye-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="manage_offers.php?action=activate&id=<?php echo $offer['id']; ?>" class="btn btn-sm btn-outline-success" title="Activate">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="edit_offer.php?id=<?php echo $offer['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="manage_offers.php?action=delete&id=<?php echo $offer['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Are you sure you want to delete this offer? This action cannot be undone.')" 
                                           title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 rounded-bottom-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing <?php echo count($offers); ?> offers
                    </div>
                    <div>
                        <nav>
                            <ul class="pagination mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                                <li class="page-item active">
                                    <a class="page-link" href="#">1</a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="upload_offer.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Upload New Offer
        </a>
    </div>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>

<!-- Custom styles for the new theme -->
<style>
:root {
    --primary-color: #4361ee;
    --success-color: #4cc9f0;
    --warning-color: #f72585;
    --info-color: #4895ef;
    --dark-color: #1d1e2c;
    --light-color: #f8f9fa;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background-color: #fff;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 1rem;
    border-bottom-width: 1px;
}

.table-hover > tbody > tr:hover {
    background-color: rgba(67, 97, 238, 0.05);
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    border-radius: 6px;
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-outline-primary:hover,
.btn-outline-primary:focus {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.btn-outline-success:hover,
.btn-outline-success:focus {
    background-color: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.btn-outline-warning:hover,
.btn-outline-warning:focus {
    background-color: var(--warning-color);
    border-color: var(--warning-color);
    color: white;
}

.btn-outline-danger:hover,
.btn-outline-danger:focus {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}

.alert {
    border: none;
    border-radius: 8px;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.pagination .page-link {
    color: var(--primary-color);
    border-color: #dee2e6;
}

.pagination .page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table th, .table td {
        padding: 0.6rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .card-header {
        padding: 0.75rem;
    }
    
    h2 {
        font-size: 1.3rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.75rem;
    }
    
    .table th, .table td {
        padding: 0.4rem;
    }
    
    .btn-sm {
        padding: 0.15rem 0.3rem;
        font-size: 0.7rem;
    }
    
    h2 {
        font-size: 1.25rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }
    
    .card-footer .d-flex {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }
    
    .pagination {
        margin-top: 0.5rem;
    }
    
    .input-group {
        max-width: 150px !important;
    }
}

@media (max-width: 400px) {
    .table-responsive {
        font-size: 0.7rem;
    }
    
    .table th, .table td {
        padding: 0.3rem;
    }
    
    .btn-sm {
        padding: 0.1rem 0.25rem;
        font-size: 0.65rem;
    }
    
    h2 {
        font-size: 1.1rem;
    }
    
    .gap-2 {
        gap: 0.25rem !important;
    }
    
    .input-group {
        max-width: 120px !important;
        font-size: 0.8rem;
    }
    
    .input-group-text {
        padding: 0.25rem;
    }
    
    .form-control {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
}

/* Action buttons stacking on small screens */
@media (max-width: 576px) {
    .d-flex.justify-content-end.gap-2 {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: flex-end;
    }
    
    .d-flex.justify-content-end.gap-2 > .btn {
        margin-bottom: 0.25rem;
        width: auto;
    }
}

/* Extra small devices */
@media (max-width: 400px) {
    .d-flex.justify-content-end.gap-2 {
        align-items: center;
        flex-wrap: wrap;
    }
    
    .d-flex.justify-content-end.gap-2 > .btn {
        max-width: 35px;
        margin-bottom: 0.2rem;
        padding: 0.1rem 0.2rem;
        font-size: 0.6rem;
    }
}
</style>

<!-- Simple JavaScript for search functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('offerSearch');
    const tableRows = document.querySelectorAll('tbody tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Refresh button functionality
    const refreshBtn = document.getElementById('refreshBtn');
    refreshBtn.addEventListener('click', function() {
        location.reload();
    });
});
</script>
