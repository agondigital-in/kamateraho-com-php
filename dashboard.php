<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // User not found, logout
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch(PDOException $e) {
    $error = "Error fetching user data: " . $e->getMessage();
    $user = null;
}

// Fetch wallet history
try {
    $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching wallet history: " . $e->getMessage();
    $wallet_history = [];
}

// Fetch withdraw requests
try {
    $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $withdraw_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching withdraw requests: " . $e->getMessage();
    $withdraw_requests = [];
}

// Fetch revenue data (user-specific)
$today_revenue = 0.00;
$week_revenue = 0.00;
$month_revenue = 0.00;
$total_revenue = 0.00;

try {
    // Today's revenue (approved credits)
    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM wallet_history WHERE user_id = ? AND type = 'credit' AND status = 'approved' AND DATE(created_at) = CURDATE()");
    $stmt->execute([$user_id]);
    $today_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
    
    // This week's revenue
    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM wallet_history WHERE user_id = ? AND type = 'credit' AND status = 'approved' AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
    $stmt->execute([$user_id]);
    $week_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
    
    // This month's revenue
    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM wallet_history WHERE user_id = ? AND type = 'credit' AND status = 'approved' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
    $stmt->execute([$user_id]);
    $month_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
    
    // Total revenue
    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM wallet_history WHERE user_id = ? AND type = 'credit' AND status = 'approved'");
    $stmt->execute([$user_id]);
    $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0.00;
} catch(PDOException $e) {
    $error = "Error calculating revenue: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KamateRaho</title>
    <link rel="icon" href="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-RMM38DLZLM');
    </script>
    <style>
       :root {
    --primary-color: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
    --secondary-color: rgb(164, 155, 168);
    --accent-color: #e74c3c;
    --light-color: #f3e5f5; 
    --dark-color: rgb(180, 167, 185);
    --success-color: #27ae60;
    --warning-color: #f39c12;
    --info-color: #3498db;
    /* New text colors - mix of black and blue */
    --text-primary: #00008B; /* Dark blue */
    --text-secondary: #0000CD; /* Medium blue */
    --text-accent: #000080; /* Navy blue */
    --text-light: #4169E1; /* Royal blue */
    --text-dark: #0000A0; /* Dark navy blue */
}
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f4 100%);
            min-height: 100vh;
            color: var(--text-primary); /* Changed text color to black and blue mix */
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--text-light); /* Changed text color to black and blue mix */
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light); /* Changed text color to black and blue mix */
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-accent); /* Changed text color to black and blue mix */
        }
        
        .stats-label {
            font-size: 1rem;
            color: var(--text-secondary); /* Changed text color to black and blue mix */
        }
        
        .section-title {
            color: var(--text-accent); /* Changed text color to black and blue mix */
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .activity-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .bg-credit {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }
        
        .bg-debit {
            background: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
        }
        
        .bg-pending {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .bg-approved {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }
        
        .bg-rejected {
            background: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
        }
        
        .quick-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        
        .action-btn {
            flex: 1;
            min-width: 150px;
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #eee;
            color: var(--text-dark); /* Changed text color to black and blue mix */
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-color: var(--secondary-color);
        }
        
        .action-btn i {
            font-size: 2rem;
            color: var(--text-accent); /* Changed text color to black and blue mix */
            margin-bottom: 1rem;
        }
        
        .badge-custom {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
      
        
        /* Additional text color improvements - mix of black and blue */
        .navbar-brand {
            color: var(--text-accent) !important;
            font-weight: 700;
        }
        
        .navbar-text {
            color: var(--text-light) !important;
        }
        
        .display-6 {
            color: var(--text-light);
        }
        
        .lead {
            color: var(--text-light);
        }
        
        .card-header h5 {
            color: var(--text-accent);
        }
        
        .fw-bold {
            color: var(--text-dark);
        }
        
        .text-muted {
            color: var(--text-secondary) !important;
        }
        
        .btn-outline-primary {
            color: var(--text-accent);
            border-color: var(--text-accent);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--text-accent);
            color: white;
        }
        
        /* Revenue Overview Section Styles */
        .revenue-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }
        
        .revenue-title {
            color: var(--text-accent);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .revenue-stat {
            text-align: center;
            padding: 1rem;
        }
        
        .revenue-amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-accent);
            margin: 0.5rem 0;
        }
        
        .revenue-label {
            font-size: 1rem;
            color: var(--text-secondary);
        }
        
        /* Responsive styles for all devices */
        @media (max-width: 1200px) {
            .stats-number {
                font-size: 1.8rem;
            }
            
            .revenue-amount {
                font-size: 1.6rem;
            }
            
            .action-btn {
                min-width: 140px;
                padding: 1.2rem;
            }
        }
        
        @media (max-width: 992px) {
            .welcome-banner {
                padding: 1.5rem 0;
                margin-bottom: 1.5rem;
            }
            
            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
            
            .stats-number {
                font-size: 1.6rem;
            }
            
            .stats-label {
                font-size: 0.9rem;
            }
            
            .action-btn {
                min-width: 130px;
                padding: 1rem;
            }
            
            .action-btn i {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }
            
            .revenue-amount {
                font-size: 1.4rem;
            }
        }
        
        @media (max-width: 768px) {
            .welcome-banner {
                padding: 1.2rem 0;
                margin-bottom: 1.2rem;
                border-radius: 0 0 15px 15px;
            }
            
            .display-6 {
                font-size: 1.8rem;
            }
            
            .lead {
                font-size: 1rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .stats-icon {
                width: 45px;
                height: 45px;
                font-size: 1rem;
                margin-right: 0.8rem;
            }
            
            .stats-number {
                font-size: 1.4rem;
            }
            
            .stats-label {
                font-size: 0.85rem;
            }
            
            .section-title {
                font-size: 1.3rem;
                margin-bottom: 1rem;
            }
            
            .quick-actions {
                gap: 0.8rem;
                margin: 1rem 0;
            }
            
            .action-btn {
                min-width: 120px;
                padding: 0.8rem;
            }
            
            .action-btn h6 {
                font-size: 0.9rem;
                margin-bottom: 0.3rem;
            }
            
            .action-btn p {
                font-size: 0.75rem;
            }
            
            .activity-item {
                padding: 0.8rem;
            }
            
            .revenue-card {
                padding: 1rem;
                margin-bottom: 1rem;
            }
            
            .revenue-amount {
                font-size: 1.2rem;
                margin: 0.3rem 0;
            }
            
            .revenue-label {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 576px) {
            .welcome-banner {
                padding: 1rem 0;
                margin-bottom: 1rem;
                border-radius: 0 0 10px 10px;
            }
            
            .display-6 {
                font-size: 1.5rem;
            }
            
            .lead {
                font-size: 0.9rem;
            }
            
            .stats-card .card-body {
                padding: 1rem;
            }
            
            .stats-icon {
                width: 40px;
                height: 40px;
                font-size: 0.9rem;
                margin-right: 0.5rem;
            }
            
            .stats-number {
                font-size: 1.2rem;
            }
            
            .stats-label {
                font-size: 0.8rem;
            }
            
            .section-title {
                font-size: 1.2rem;
                margin-bottom: 0.8rem;
            }
            
            .quick-actions {
                gap: 0.5rem;
                margin: 0.8rem 0;
            }
            
            .action-btn {
                min-width: 100%;
                padding: 0.7rem;
                margin-bottom: 0.5rem;
            }
            
            .action-btn i {
                font-size: 1.3rem;
                margin-bottom: 0.3rem;
            }
            
            .action-btn h6 {
                font-size: 0.85rem;
                margin-bottom: 0.2rem;
            }
            
            .action-btn p {
                font-size: 0.7rem;
            }
            
            .activity-card .card-body {
                padding: 0.8rem;
            }
            
            .activity-item {
                padding: 0.7rem;
                flex-direction: column;
                align-items: flex-start;
            }
            
            .activity-item .flex-grow-1 {
                margin-bottom: 0.5rem;
            }
            
            .revenue-card {
                padding: 0.8rem;
                margin-bottom: 0.8rem;
            }
            
            .revenue-amount {
                font-size: 1.1rem;
                margin: 0.2rem 0;
            }
            
            .revenue-label {
                font-size: 0.8rem;
            }
            
          
        @media (max-width: 400px) {
            .welcome-banner {
                padding: 0.8rem 0;
                margin-bottom: 0.8rem;
            }
            
            .display-6 {
                font-size: 1.3rem;
            }
            
            .lead {
                font-size: 0.8rem;
            }
            
            .stats-icon {
                width: 35px;
                height: 35px;
                font-size: 0.8rem;
            }
            
            .stats-number {
                font-size: 1rem;
            }
            
            .stats-label {
                font-size: 0.75rem;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            .action-btn {
                padding: 0.6rem;
            }
            
            .action-btn i {
                font-size: 1.1rem;
            }
            
            .action-btn h6 {
                font-size: 0.8rem;
            }
            
            .activity-item {
                padding: 0.6rem;
            }
            
            .revenue-amount {
                font-size: 1rem;
            }
            
            .revenue-label {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
     <?php include 'includes/navbar.php'; ?>
    
    <div class="welcome-banner">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-6 fw-bold mb-2">Welcome to Your Dashboard</h1>
                    <p class="lead mb-0">Manage your finances and track your transactions</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['withdraw_success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>Withdraw request submitted successfully! The amount has been deducted from your wallet and will be processed within 24-48 hours.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Stats Section -->
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <div class="stats-number">₹<?php echo number_format($user['wallet_balance'], 2); ?></div>
                                <div class="stats-label">Wallet Balance</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </div>
                            <div>
                                <div class="stats-number"><?php echo count($wallet_history); ?></div>
                                <div class="stats-label">Transactions</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div>
                                <div class="stats-number"><?php echo count($withdraw_requests); ?></div>
                                <div class="stats-label">Requests</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="section-title">Quick Actions</div>
        <div class="quick-actions">
            <a href="withdraw.php" class="action-btn text-decoration-none">
                <i class="fas fa-money-bill-wave"></i>
                <h6>Withdraw Money</h6>
                <p class="text-muted small mb-0">Request withdrawal</p>
            </a>
            
            <a href="index.php" class="action-btn text-decoration-none">
                <i class="fas fa-shopping-cart"></i>
                <h6>Explore Offers</h6>
                <p class="text-muted small mb-0">Find new deals</p>
            </a>
            
            <a href="#" class="action-btn text-decoration-none">
                <i class="fas fa-history"></i>
                <h6>Transaction History</h6>
                <p class="text-muted small mb-0">View all activity</p>
            </a>
            
            <a href="contact.php" class="action-btn text-decoration-none">
                <i class="fas fa-headset"></i>
                <h6>Support</h6>
                <p class="text-muted small mb-0">Get help</p>
            </a>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <!-- Recent Wallet Activity -->
                <div class="activity-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Recent Wallet Activity
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($wallet_history)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-wallet fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No wallet activity yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($wallet_history as $history): ?>
                                <div class="activity-item d-flex align-items-center">
                                    <div class="activity-icon <?php echo $history['type'] === 'credit' ? 'bg-credit' : 'bg-debit'; ?>">
                                        <i class="fas fa-<?php echo $history['type'] === 'credit' ? 'arrow-down' : 'arrow-up'; ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?php echo htmlspecialchars($history['description']); ?></div>
                                        <div class="small text-muted"><?php echo date('d M Y, H:i', strtotime($history['created_at'])); ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold <?php echo $history['type'] === 'credit' ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo $history['type'] === 'credit' ? '+' : '-'; ?>₹<?php echo number_format($history['amount'], 2); ?>
                                        </div>
                                        <span class="badge badge-custom bg-<?php 
                                            echo $history['status'] === 'approved' ? 'success' : 
                                               ($history['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                            <?php echo ucfirst($history['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-primary btn-sm">View All Transactions</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Recent Withdraw Requests -->
                <div class="activity-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Recent Withdraw Requests
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($withdraw_requests)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-invoice fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">No withdraw requests yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($withdraw_requests as $request): ?>
                                <div class="activity-item d-flex align-items-center">
                                    <div class="activity-icon <?php echo $request['status'] === 'approved' ? 'bg-approved' : ($request['status'] === 'rejected' ? 'bg-rejected' : 'bg-pending'); ?>">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">Withdraw Request</div>
                                        <div class="small text-muted">UPI: <?php echo htmlspecialchars(substr($request['upi_id'], 0, 15)); ?>...</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">₹<?php echo number_format($request['amount'], 2); ?></div>
                                        <span class="badge badge-custom bg-<?php 
                                            echo $request['status'] === 'approved' ? 'success' : 
                                               ($request['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-3">
                                <a href="#" class="btn btn-outline-primary btn-sm">View All Requests</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Overview Section -->
        <div class="row">
            <div class="col-12">
                <div class="revenue-title">Revenue Overview</div>
            </div>
            
            <div class="col-md-3">
                <div class="revenue-card">
                    <div class="revenue-stat">
                        <i class="fas fa-rupee-sign fa-2x text-success"></i>
                        <div class="revenue-amount">₹<?php echo number_format($today_revenue, 2); ?></div>
                        <div class="revenue-label">Today's Revenue</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="revenue-card">
                    <div class="revenue-stat">
                        <i class="fas fa-rupee-sign fa-2x text-primary"></i>
                        <div class="revenue-amount">₹<?php echo number_format($week_revenue, 2); ?></div>
                        <div class="revenue-label">This Week</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="revenue-card">
                    <div class="revenue-stat">
                        <i class="fas fa-rupee-sign fa-2x text-info"></i>
                        <div class="revenue-amount">₹<?php echo number_format($month_revenue, 2); ?></div>
                        <div class="revenue-label">This Month</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="revenue-card">
                    <div class="revenue-stat">
                        <i class="fas fa-rupee-sign fa-2x text-warning"></i>
                        <div class="revenue-amount">₹<?php echo number_format($total_revenue, 2); ?></div>
                        <div class="revenue-label">Total Revenue</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
       <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>