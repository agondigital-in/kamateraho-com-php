<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to sub-admin login page
header("Location: subadmin_login.php");
exit;
?>