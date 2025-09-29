<?php
// Start output buffering immediately to avoid "headers already sent" from earlier warnings/notices
if (!ob_get_level()) {
    ob_start();
}
// Start session if not already started - this should be at the very top
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include app configuration
include_once __DIR__ . '/../../config/app.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    if (!headers_sent()) {
        header("Location: login.php");
    } else {
        echo '<script>window.location.href = "login.php";</script>';
    }
    exit;
}

// Admin information (in a real application, this would come from a database)
$admin_name = "Admin User";
$admin_email = "admin@kamateraho.com";
$admin_avatar = "https://ui-avatars.com/api/?name=Admin+User&background=0D8ABC&color=fff";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?> - KamateRaho</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="assets/admin.css">
    <!-- Custom Admin CSS -->
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
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
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
        
        .sidebar .logo {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .logo img {
            height: 30px;
            margin-right: 10px;
        }
        
        .sidebar .logo h4 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
        }
        
        .header {
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
        
        .header .toggle-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark-color);
            cursor: pointer;
        }
        
        .header .profile {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .content {
            margin-top: var(--header-height);
            padding: 20px;
        }
        
        /* Card Styles */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }
        
        /* Stats Card */
        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .stats-card .number {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stats-card .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="assets/logo.svg" alt="KamateRaho Logo">
            <h4>Admin Panel</h4>
        </div>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_category.php') ? 'active' : ''; ?>" href="add_category.php">
                        <i class="bi bi-plus-circle"></i> Add Category
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_categories.php') ? 'active' : ''; ?>" href="manage_categories.php">
                        <i class="bi bi-list"></i> Manage Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'upload_offer.php') ? 'active' : ''; ?>" href="upload_offer.php">
                        <i class="bi bi-upload"></i> Upload Offer
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_credit_cards.php') ? 'active' : ''; ?>" href="manage_credit_cards.php">
                        <i class="bi bi-credit-card"></i> Manage Credit Cards
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'wallet_management.php') ? 'active' : ''; ?>" href="wallet_management.php">
                        <i class="bi bi-wallet2"></i> Wallet Management
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'verify_wallet_deduction.php') ? 'active' : ''; ?>" href="verify_wallet_deduction.php">
                        <i class="bi bi-cash-stack"></i> Verify Wallet Deduction
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'check_wallet_status.php') ? 'active' : ''; ?>" href="check_wallet_status.php">
                        <i class="bi bi-graph-up"></i> Wallet Status
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact_messages.php') ? 'active' : ''; ?>" href="contact_messages.php">
                        <i class="bi bi-envelope"></i> Contact Messages
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <button class="toggle-btn" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="profile">
                <img src="<?php echo $admin_avatar; ?>" alt="Admin">
                <div>
                    <div><?php echo $admin_name; ?></div>
                    <small class="text-muted"><?php echo $admin_email; ?></small>
                </div>
                <a href="../logout.php" class="btn btn-outline-danger ms-3">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
```

```
