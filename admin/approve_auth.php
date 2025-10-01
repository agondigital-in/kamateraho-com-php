<?php
// Start session if not already started - this should be at the very top
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include app configuration
include_once dirname(__DIR__) . '/../config/app.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    if (!headers_sent()) {
        header("Location: login.php");
    } else {
        echo '<script>window.location.href = "login.php";</script>';
    }
    exit;
}

// Initialize variables
$isSubAdmin = false;
$isAdmin = true;
$subAdminId = null;
?>