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

// Removed admin authentication check to allow direct access

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
    <link rel="icon" href="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" type="image/x-icon" />
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="assets/admin.css">
    <!-- Batch Action CSS -->
    <link rel="stylesheet" href="assets/admin_batch.css">
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
            overflow-y: auto;
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
            text-decoration: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
            min-width: 20px;
            text-align: center;
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
            padding: 0 15px;
        }
        
        .header .toggle-btn {
            margin-right: 10px;
        }
        
        .header .profile {
            display: flex;
            align-items: center;
            margin-left: auto;
            min-width: 0; /* Allow flex shrinking */
        }
        
        .header .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .header .profile div {
            min-width: 0; /* Allow flex shrinking */
        }
        
        .header .profile div div {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }
        
        @media (max-width: 576px) {
            .header .profile div div {
                max-width: 100px;
                font-size: 0.85rem;
            }
            
            .header .profile small {
                font-size: 0.7rem;
            }
        }
        
        @media (max-width: 400px) {
            .header .profile div div {
                display: none;
            }
            
            .header .profile small {
                display: none;
            }
            
            .header .profile img {
                margin-right: 0;
            }
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
        
        /* Ensure content area takes full height */
        .main-content {
            min-height: 100vh;
        }
        
        /* Scrollable content area */
        .content {
            overflow-y: auto;
            max-height: calc(100vh - var(--header-height));
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
        
        @media (max-width: 768px) {
            .stats-card .number {
                font-size: 1.75rem;
            }
            
            .stats-card .label {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 576px) {
            .stats-card .number {
                font-size: 1.5rem;
            }
            
            .stats-card .label {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 400px) {
            .stats-card .number {
                font-size: 1.25rem;
            }
            
            .stats-card .label {
                font-size: 0.75rem;
            }
        }
        
        /* Sidebar Backdrop */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        
        .sidebar-backdrop.active {
            display: block;
        }
        
        /* Prevent body scroll when sidebar is open */
        body.sidebar-open {
            overflow: hidden;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 220px;
            }
            
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
        
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 200px;
                --header-height: 50px;
            }
            
            .sidebar .logo h4 {
                font-size: 1rem;
            }
            
            .sidebar .nav-link {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .header .profile img {
                width: 35px;
                height: 35px;
            }
        }
        
        @media (max-width: 576px) {
            :root {
                --sidebar-width: 180px;
            }
            
            .sidebar .logo {
                padding: 0 15px;
            }
            
            .sidebar .nav-link {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            .sidebar .nav-link i {
                margin-right: 5px;
                font-size: 1rem;
            }
            
            .content {
                padding: 15px;
            }
            
            .card {
                margin-bottom: 15px;
            }
        }
        
        @media (max-width: 400px) {
            :root {
                --sidebar-width: 160px;
            }
            
            .sidebar .logo h4 {
                font-size: 0.9rem;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .sidebar .nav-link {
                justify-content: center;
                padding: 12px;
            }
        }
        
        /* Text Slider Styles */
        .slider-text {
            animation: slide-left 10s linear infinite;
            padding-left: 100%;
        }

        @keyframes slide-left {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        .text-truncate-slider:hover .slider-text {
            animation-play-state: running;
        }

        .text-truncate-slider .slider-text {
            animation-play-state: paused;
        }

        @media (max-width: 768px) {
            .text-truncate-slider {
                max-width: 150px;
            }
        }

        @media (max-width: 576px) {
            .text-truncate-slider {
                max-width: 100px;
            }
            
            .slider-text {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 400px) {
            .text-truncate-slider {
                max-width: 80px;
            }
            
            .slider-text {
                font-size: 0.75rem;
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
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                </li>
                                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'all_users.php') ? 'active' : ''; ?>" href="all_users.php">
                        <i class="bi bi-people-fill"></i> <span>All Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'add_category.php') ? 'active' : ''; ?>" href="add_category.php">
                        <i class="bi bi-plus-circle"></i> <span>Add Category</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_categories.php') ? 'active' : ''; ?>" href="manage_categories.php">
                        <i class="bi bi-list"></i> <span>Manage Categories</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'upload_offer.php') ? 'active' : ''; ?>" href="upload_offer.php">
                        <i class="bi bi-upload"></i> <span>Upload Offer</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_offers.php') ? 'active' : ''; ?>" href="manage_credit_cards.php">
                        <i class="bi bi-credit-card"></i> <span>Manage Credit Cards</span>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'wallet_management.php') ? 'active' : ''; ?>" href="wallet_management.php">
                        <i class="bi bi-wallet2"></i> <span>Users Management</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'verify_wallet_deduction.php') ? 'active' : ''; ?>" href="verify_wallet_deduction.php">
                        <i class="bi bi-cash-stack"></i> <span>Verify Wallet Deduction</span>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'check_wallet_status.php') ? 'active' : ''; ?>" href="check_wallet_status.php">
                        <i class="bi bi-graph-up"></i> <span>Wallet Status</span>
                    </a>
                </li> -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact_messages.php') ? 'active' : ''; ?>" href="contact_messages.php">
                        <i class="bi bi-envelope"></i> <span>Contact Messages</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_subadmins.php') ? 'active' : ''; ?>" href="manage_subadmins.php">
                        <i class="bi bi-people"></i> <span>Manage Sub-Admins</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'pending_withdraw_requests.php') ? 'active' : ''; ?>" href="pending_withdraw_requests.php">
                        <i class="bi bi-cash-stack"></i> <span>Users Requests</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'referral_stats.php') ? 'active' : ''; ?>" href="referral_stats.php">
                        <i class="bi bi-people"></i> <span>Referral Statistics</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'manage_sliders.php') ? 'active' : ''; ?>" href="manage_sliders.php">
                        <i class="bi bi-images"></i> <span>Manage Sliders</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Sidebar Backdrop (for mobile) -->
    <div class="sidebar-backdrop"></div>

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
        <script>
        // Pause animation when mouse leaves
        document.addEventListener('DOMContentLoaded', function() {
            const sliders = document.querySelectorAll('.text-truncate-slider');
            
            sliders.forEach(slider => {
                const textElement = slider.querySelector('.slider-text');
                
                slider.addEventListener('mouseenter', function() {
                    textElement.style.animationPlayState = 'running';
                });
                
                slider.addEventListener('mouseleave', function() {
                    textElement.style.animationPlayState = 'paused';
                });
            });
        });
        </script>
        <!-- Batch Action JavaScript -->
        <script src="assets/admin_batch.js"></script>
    </body>
</html>