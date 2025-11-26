<?php
// Simple test to verify redirect functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Redirect to the blog index page
    header("Location: ../kamateraho/blog/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Redirect</title>
</head>
<body>
    <h2>Test Redirect Functionality</h2>
    <form method="POST">
        <button type="submit">Test Redirect</button>
    </form>
</body>
</html>