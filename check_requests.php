<?php
include 'config/db.php';

try {
    $stmt = $pdo->prepare("SELECT id, user_id, amount, upi_id, offer_title, offer_description FROM withdraw_requests WHERE status = 'pending' LIMIT 10");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Pending Withdraw Requests:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Amount</th><th>UPI ID</th><th>Offer Title</th><th>Offer Description</th></tr>";
    
    foreach ($requests as $request) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($request['id']) . "</td>";
        echo "<td>" . htmlspecialchars($request['user_id']) . "</td>";
        echo "<td>" . htmlspecialchars($request['amount']) . "</td>";
        echo "<td>" . htmlspecialchars($request['upi_id']) . "</td>";
        echo "<td>" . htmlspecialchars($request['offer_title']) . "</td>";
        echo "<td>" . htmlspecialchars($request['offer_description']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Also check a specific request if we have one
    if (!empty($requests)) {
        $first_request = $requests[0];
        echo "<h2>Checking offer details for: " . htmlspecialchars($first_request['offer_title']) . "</h2>";
        
        // Try to find the offer
        $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title LIKE ? OR title LIKE ?");
        $offerStmt->execute([$first_request['offer_title'], '%' . $first_request['offer_title'] . '%']);
        $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($offer) {
            echo "<p>Found offer with price_type: " . htmlspecialchars($offer['price_type']) . " and price: " . htmlspecialchars($offer['price']) . "</p>";
        } else {
            echo "<p>No offer found with LIKE search. Trying exact match...</p>";
            
            $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title = ?");
            $offerStmt->execute([$first_request['offer_title']]);
            $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($offer) {
                echo "<p>Found offer with exact match - price_type: " . htmlspecialchars($offer['price_type']) . " and price: " . htmlspecialchars($offer['price']) . "</p>";
            } else {
                echo "<p>Still no offer found with exact match.</p>";
            }
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>