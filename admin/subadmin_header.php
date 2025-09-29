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
</head>
<body>
    <?php include 'subadmin_sidebar.php'; ?>