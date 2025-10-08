<?php
// Check if session is already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    // Use JavaScript redirect if headers already sent
    if (headers_sent()) {
        echo '<script>window.location.href = "login.php";</script>';
        exit;
    } else {
        header("Location: login.php");
        exit;
    }
}
?>