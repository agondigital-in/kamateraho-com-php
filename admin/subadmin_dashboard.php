<?php
session_start();
include '../config/db.php';

// Check if sub-admin is logged in
if (!isset($_SESSION['sub_admin_logged_in']) || !$_SESSION['sub_admin_logged_in']) {
    header("Location: subadmin_login.php");
    exit;
}

$page_title = "Dashboard";

// Get sub-admin permissions
$permissions = [];
try {
    $stmt = $pdo->prepare("SELECT permission, allowed FROM sub_admin_permissions WHERE sub_admin_id = ?");
    $stmt->execute([$_SESSION['sub_admin_id']]);
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($perms as $perm) {
        if ($perm['allowed']) {
            $permissions[] = $perm['permission'];
        }
    }
} catch (PDOException $e) {
    $error = "Error fetching permissions: " . $e->getMessage();
}

// Fetch counts for dashboard
$pending_withdraw_count = 0;

if (in_array('pending_withdraw_requests', $permissions)) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM withdraw_requests WHERE status = 'pending'");
        $pending_withdraw_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    } catch (PDOException $e) {
        // Handle error silently
    }
}

include 'subadmin_header.php';
?>

<div class="container-fluid">
    <h2 class="mb-4">Sub-Admin Dashboard</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <?php if (in_array('pending_withdraw_requests', $permissions)): ?>
        <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number"><?php echo $pending_withdraw_count; ?></div>
                            <div class="label">Pending Withdraw Requests</div>
                        </div>
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                    <div class="mt-3">
                        <a href="pending_withdraw_requests.php" class="btn btn-sm btn-light w-100">View Requests</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Your Permissions</h5>
        </div>
        <div class="card-body">
            <?php if (empty($permissions)): ?>
                <p>You don't have any permissions assigned yet.</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($permissions as $permission): ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <?php echo ucfirst(str_replace('_', ' ', $permission)); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'subadmin_footer.php'; ?>