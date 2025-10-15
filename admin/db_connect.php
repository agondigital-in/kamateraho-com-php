<?php
// Database connection file specifically for admin pages that need database access without HTML output
include '../config/db.php';

// Check if session is already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include app configuration with correct relative path
include_once '../config/app.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    if (!headers_sent()) {
        header("Location: login.php");
    } else {
        echo '<script>window.location.href = "login.php";</script>';
    }
    exit;
}
?>