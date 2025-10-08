<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include_once __DIR__ . '/../config/db.php';

// Include notifications functionality
require_once __DIR__ . '/../admin/notifications.php';

// Initialize wallet balance and notifications count
$wallet_balance = 0.00;
$notifications_count = 0;

// Fetch wallet balance and notifications if user is logged in
if (isset($_SESSION['user_id']) && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT wallet_balance, name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $wallet_balance = $user['wallet_balance'];
            $user_name = $user['name'];
        }
        
        // Get unread notifications count for the user
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->execute([$_SESSION['user_id']]);
        $notifications_count = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Handle error silently
        $wallet_balance = 0.00;
        $notifications_count = 0;
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <img src="kamateraho/img/logo.png" alt="KamateRaho Logo" style="height: 50px; width: auto;">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Offers</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                   
                <li class="nav-item">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#referralModal">Refer Friend & Earn</a>
                    <?php else: ?>
                        <a class="nav-link" href="register.php">Refer Friend & Earn</a>
                    <?php endif; ?>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Notifications Icon -->
                    <a href="user_notifications.php" class="me-3 position-relative text-decoration-none">
                        <i class="fas fa-bell text-primary" style="font-size: 1.2rem;"></i>
                        <?php if ($notifications_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                <?php echo $notifications_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Wallet Balance Display - Now clickable to go to dashboard -->
                    <a href="dashboard.php" class="me-3 d-flex align-items-center wallet-display text-decoration-none">
                        <i class="fas fa-wallet text-primary me-1"></i>
                        <span class="fw-bold balance-amount">₹<?php echo number_format($wallet_balance, 2); ?></span>
                    </a>
                    
                    <!-- Withdraw Button -->
                    <a href="withdraw.php" class="btn btn-withdraw btn-sm me-3">
                        <i class="fas fa-money-bill-wave me-1"></i>Withdraw
                    </a>
                    
                    <!-- Simplified User Profile Dropdown - Only Profile and Logout -->
                    <div class="dropdown">
                        <a class="btn btn-user-profile btn-sm dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($user_name ?? 'User'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="reset_password.php"><i class="fas fa-key me-2"></i>Reset Password</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-login btn-sm me-2">Login</a>
                    <a href="register.php" class="btn btn-register btn-sm">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>