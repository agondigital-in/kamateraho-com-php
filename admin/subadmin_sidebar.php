<?php
// Check if sub-admin is logged in
if (!isset($_SESSION['sub_admin_logged_in']) || !$_SESSION['sub_admin_logged_in']) {
    header("Location: subadmin_login.php");
    exit;
}

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
    // Handle error silently
}
?>

<style>
    :root {
        --sidebar-width: 250px;
        --header-height: 60px;
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --dark-color: #1d1e2c;
        --light-color: #f8f9fa;
    }
    
    /* Sidebar Styles */
    .subadmin-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: var(--sidebar-width);
        height: 100vh;
        background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
        color: white;
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .subadmin-sidebar .logo {
        height: var(--header-height);
        display: flex;
        align-items: center;
        padding: 0 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .subadmin-sidebar .logo h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .subadmin-sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 20px;
        margin: 5px 10px;
        border-radius: 8px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
    }
    
    .subadmin-sidebar .nav-link i {
        margin-right: 10px;
        font-size: 1.1rem;
    }
    
    .subadmin-sidebar .nav-link:hover, 
    .subadmin-sidebar .nav-link.active {
        color: white;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    /* Main Content Styles */
    .subadmin-main-content {
        margin-left: var(--sidebar-width);
        transition: all 0.3s;
    }
    
    .subadmin-header {
        height: var(--header-height);
        background-color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        right: 0;
        left: var(--sidebar-width);
        z-index: 999;
        display: flex;
        align-items: center;
        padding: 0 20px;
    }
    
    .subadmin-header .profile {
        display: flex;
        align-items: center;
        margin-left: auto;
    }
    
    .subadmin-content {
        margin-top: var(--header-height);
        padding: 20px;
    }
</style>

<!-- Sidebar -->
<div class="subadmin-sidebar">
    <div class="logo">
        <h4>Sub-Admin Panel</h4>
    </div>
    <div class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'subadmin_dashboard.php' ? 'active' : ''; ?>" href="subadmin_dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            
            <?php if (in_array('upload_offer', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'upload_offer.php' ? 'active' : ''; ?>" href="upload_offer.php">
                    <i class="bi bi-upload"></i> Upload Offer
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('manage_credit_cards', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_credit_cards.php' ? 'active' : ''; ?>" href="manage_credit_cards.php">
                    <i class="bi bi-credit-card"></i> Manage Credit Cards
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('pending_withdraw_requests', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'approve_withdraw.php' ? 'active' : ''; ?>" href="approve_withdraw.php">
                    <i class="bi bi-cash-stack"></i> Pending Withdraw Requests
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('pending_wallet_approvals', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'approve_wallet.php' ? 'active' : ''; ?>" href="approve_wallet.php">
                    <i class="bi bi-wallet2"></i> Pending Wallet Approvals
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('all_categories', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_categories.php' ? 'active' : ''; ?>" href="manage_categories.php">
                    <i class="bi bi-list"></i> All Categories
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('add_new_category', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_category.php' ? 'active' : ''; ?>" href="add_category.php">
                    <i class="bi bi-plus-circle"></i> Add New Category
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('contact_messages', $permissions)): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact_messages.php' ? 'active' : ''; ?>" href="contact_messages.php">
                    <i class="bi bi-envelope"></i> Contact Messages
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="subadmin-main-content">
    <!-- Header -->
    <div class="subadmin-header">
        <div class="profile">
            <div>
                <div><?php echo htmlspecialchars($_SESSION['sub_admin_name']); ?></div>
                <small class="text-muted">Sub-Admin</small>
            </div>
            <a href="subadmin_logout.php" class="btn btn-outline-danger ms-3">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="subadmin-content">