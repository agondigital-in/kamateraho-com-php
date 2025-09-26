<?php
// Simple verification script to check if the fix is working
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed.");
}

echo "<h1>Fix Verification</h1>";

// Check if the required columns exist in withdraw_requests table
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM withdraw_requests LIKE 'offer_title'");
    $result = $stmt->fetchAll();
    
    if (count($result) > 0) {
        echo "<p>✓ offer_title column exists in withdraw_requests table</p>";
    } else {
        echo "<p>✗ offer_title column missing from withdraw_requests table</p>";
        echo "<p>Please run update_database.php to add the missing columns</p>";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM withdraw_requests LIKE 'offer_description'");
    $result = $stmt->fetchAll();
    
    if (count($result) > 0) {
        echo "<p>✓ offer_description column exists in withdraw_requests table</p>";
    } else {
        echo "<p>✗ offer_description column missing from withdraw_requests table</p>";
        echo "<p>Please run update_database.php to add the missing columns</p>";
    }
    
} catch(PDOException $e) {
    echo "<p class='text-danger'>Error checking table structure: " . $e->getMessage() . "</p>";
}

echo "<h3>Files Modified for Fix</h3>";
$files = [
    'product_details.php' => 'Modified to create purchase requests',
    'admin/index.php' => 'Updated to show purchase requests differently',
    'admin/approve_withdraw.php' => 'Updated to handle purchase requests',
    'update_database.php' => 'Database schema update script',
    'test_purchase_request.php' => 'Test script for verification',
    'SOLUTION_SUMMARY.md' => 'Documentation of changes'
];

echo "<ul>";
foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<li>✓ $file - $description</li>";
    } else {
        echo "<li>✗ $file - Missing</li>";
    }
}
echo "</ul>";

echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>Test the functionality by clicking 'Apply Now' on any product or credit card</li>";
echo "<li>Check the admin panel to see the request appear with a green 'Purchase Request' badge</li>";
echo "<li>Approve the request to see the amount added to the user's wallet</li>";
echo "</ol>";

echo "<p><a href='index.php' class='btn btn-primary'>Go to Homepage</a></p>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <?php // Content is generated above ?>
    </div>
</body>
</html>