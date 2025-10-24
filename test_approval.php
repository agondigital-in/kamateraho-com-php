<?php
// Test script to verify the approval workflow
include 'config/db.php';

// Get a pending request for testing
try {
    $stmt = $pdo->prepare("SELECT id, offer_title FROM withdraw_requests WHERE status = 'pending' LIMIT 1");
    $stmt->execute();
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($request) {
        echo "<h2>Testing approval workflow for request ID: " . $request['id'] . "</h2>";
        echo "<p>Offer Title: " . htmlspecialchars($request['offer_title']) . "</p>";
        
        // Try to find the offer
        $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title = ?");
        $offerStmt->execute([$request['offer_title']]);
        $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($offer) {
            echo "<p>Found offer - Price Type: " . $offer['price_type'] . ", Price: " . $offer['price'] . "</p>";
        } else {
            echo "<p>No exact match found for offer title. Trying LIKE search...</p>";
            
            $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title LIKE ?");
            $offerStmt->execute(['%' . $request['offer_title'] . '%']);
            $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($offer) {
                echo "<p>Found offer with LIKE - Price Type: " . $offer['price_type'] . ", Price: " . $offer['price'] . "</p>";
            } else {
                echo "<p>Still no offer found.</p>";
            }
        }
        
        echo "<p><a href='admin/approve_withdraw.php?id=" . $request['id'] . "&action=approve'>Test Approval</a></p>";
    } else {
        echo "<p>No pending requests found.</p>";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>