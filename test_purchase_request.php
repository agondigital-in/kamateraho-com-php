<?php
// Test script to verify purchase request functionality
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Create a test user session for testing
    $_SESSION['user_id'] = 1; // Assuming user ID 1 exists
}

$user_id = $_SESSION['user_id'];

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("Test user not found. Please ensure user ID 1 exists in the database.");
    }
} catch(PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}

// Create a test purchase request
if (isset($_GET['create_test'])) {
    try {
        // Create a test purchase request
        $amount = 5000; // Test amount
        $upi_id = "purchase@" . time(); // Special UPI ID to identify purchases
        $screenshot = ""; // No screenshot needed for this type of request
        $offer_title = "Test Offer";
        $offer_description = "This is a test purchase request";
        
        // Insert withdraw request with special UPI ID to identify it as a purchase
        $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot, offer_title, offer_description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, 
            $amount, 
            $upi_id, 
            $screenshot,
            $offer_title,
            $offer_description
        ]);
        
        $request_id = $pdo->lastInsertId();
        echo "<div class='alert alert-success'>Test purchase request created successfully with ID: " . $request_id . "</div>";
    } catch(PDOException $e) {
        echo "<div class='alert alert-danger'>Error creating test request: " . $e->getMessage() . "</div>";
    }
}

// Display user's current wallet balance
echo "<h3>User Information</h3>";
echo "<p><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</p>";
echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
echo "<p><strong>Current Wallet Balance:</strong> ₹" . number_format($user['wallet_balance'], 2) . "</p>";

// Show button to create test request
echo "<a href='?create_test=1' class='btn btn-primary'>Create Test Purchase Request</a>";

// Show existing requests
try {
    $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3 class='mt-4'>Your Requests</h3>";
    if (empty($requests)) {
        echo "<p>No requests found.</p>";
    } else {
        echo "<table class='table'>";
        echo "<thead><tr><th>ID</th><th>Amount</th><th>UPI ID</th><th>Status</th><th>Created</th></tr></thead>";
        echo "<tbody>";
        foreach ($requests as $request) {
            echo "<tr>";
            echo "<td>" . $request['id'] . "</td>";
            echo "<td>₹" . number_format($request['amount'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($request['upi_id']) . "</td>";
            echo "<td>" . ucfirst($request['status']) . "</td>";
            echo "<td>" . $request['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Error fetching requests: " . $e->getMessage() . "</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Purchase Request</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Test Purchase Request Functionality</h1>
        <?php // Content is generated above ?>
    </div>
</body>
</html>