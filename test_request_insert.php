<?php
// Test script to verify withdraw request insertion
include 'config/db.php';

try {
    // Insert a test request
    $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot, offer_title, offer_description) VALUES (?, ?, ?, ?, ?, ?)");
    $result = $stmt->execute([
        1, 
        100.00, 
        "purchase@" . time(), 
        "",
        "Test Offer",
        "Test Description"
    ]);
    
    if ($result) {
        $request_id = $pdo->lastInsertId();
        echo "Test request inserted successfully with ID: " . $request_id . "\n";
        
        // Fetch the request to verify it exists
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            echo "Request found in database:\n";
            print_r($request);
        } else {
            echo "Request not found in database\n";
        }
    } else {
        echo "Failed to insert test request\n";
    }
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>