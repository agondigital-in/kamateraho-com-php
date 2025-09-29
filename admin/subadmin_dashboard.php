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
$pending_wallet_count = 0;
$pending_withdraw_count = 0;

if (in_array('pending_wallet_approvals', $permissions)) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM wallet_history WHERE status = 'pending'");
        $pending_wallet_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
    } catch (PDOException $e) {
        // Handle error silently
    }
}

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

<h2>Sub-Admin Dashboard</h2>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row mb-4">
    <?php if (in_array('pending_wallet_approvals', $permissions)): ?>
    <div class="col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="number"><?php echo $pending_wallet_count; ?></div>
                        <div class="label">Pending Wallet Approvals</div>
                    </div>
                    <i class="bi bi-wallet fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (in_array('pending_withdraw_requests', $permissions)): ?>
    <div class="col-md-6">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="number"><?php echo $pending_withdraw_count; ?></div>
                        <div class="label">Pending Withdraw Requests</div>
                    </div>
                    <i class="bi bi-cash-stack fs-1"></i>
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
            <ul>
                <?php foreach ($permissions as $permission): ?>
                    <li><?php echo ucfirst(str_replace('_', ' ', $permission)); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include 'subadmin_footer.php'; ?>