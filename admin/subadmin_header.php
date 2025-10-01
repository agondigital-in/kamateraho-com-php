<?php
// Check if sub-admin is logged in
if (!isset($_SESSION['sub_admin_logged_in']) || !$_SESSION['sub_admin_logged_in']) {
    header("Location: subadmin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Sub-Admin Panel'; ?> - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            padding-top: var(--header-height);
        }
        
        /* Header Styles */
        .header {
            height: var(--header-height);
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }
        
        .header .logo {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color);
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
        
        /* Main Content Styles */
        .content {
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
            transform: translateY(-3px);
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
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .content {
                padding: 15px;
            }
            
            .card {
                margin-bottom: 15px;
            }
            
            .stats-card .number {
                font-size: 1.5rem;
            }
            
            .stats-card .label {
                font-size: 0.8rem;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding-top: 50px;
            }
            
            .header {
                height: 50px;
                padding: 0 10px;
            }
            
            .header .logo {
                font-size: 1.2rem;
            }
            
            .content {
                padding: 10px;
            }
            
            .card {
                margin-bottom: 10px;
            }
            
            .stats-card .number {
                font-size: 1.25rem;
            }
            
            .stats-card .label {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">Sub-Admin Panel</div>
        <div class="profile">
            <div>
                <div><?php echo htmlspecialchars($_SESSION['sub_admin_name']); ?></div>
                <small class="text-muted">Sub-Admin</small>
            </div>
            <a href="subadmin_logout.php" class="btn btn-outline-danger ms-3">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">