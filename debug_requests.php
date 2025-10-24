<?php
// Debug script to check requests in database
include 'config/db.php';

echo "<h2>Debug Requests</h2>";

if ($pdo) {
    echo "<p>✅ Database connection successful</p>";
    
    try {
        // Get all pending requests
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE status = 'pending' ORDER BY created_at DESC LIMIT 20");
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Pending Requests (" . count($requests) . " found)</h3>";
        
        if (!empty($requests)) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>Amount</th>
                        <th>UPI ID</th>
                        <th>Offer Title</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Is Purchase</th>
                    </tr>";
            
            foreach ($requests as $request) {
                $isPurchase = strpos($request['upi_id'], 'purchase@') === 0;
                echo "<tr>
                        <td>" . htmlspecialchars($request['id']) . "</td>
                        <td>" . htmlspecialchars($request['user_id']) . "</td>
                        <td>" . htmlspecialchars($request['amount']) . "</td>
                        <td>" . htmlspecialchars($request['upi_id']) . "</td>
                        <td>" . htmlspecialchars($request['offer_title']) . "</td>
                        <td>" . htmlspecialchars($request['status']) . "</td>
                        <td>" . htmlspecialchars($request['created_at']) . "</td>
                        <td>" . ($isPurchase ? 'Yes' : 'No') . "</td>
                      </tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No pending requests found</p>";
        }
        
        // Get all users
        $userStmt = $pdo->prepare("SELECT id, name, email FROM users ORDER BY id LIMIT 20");
        $userStmt->execute();
        $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Users (" . count($users) . " found)</h3>";
        
        if (!empty($users)) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>";
            
            foreach ($users as $user) {
                echo "<tr>
                        <td>" . htmlspecialchars($user['id']) . "</td>
                        <td>" . htmlspecialchars($user['name']) . "</td>
                        <td>" . htmlspecialchars($user['email']) . "</td>
                      </tr>";
            }
            
            echo "</table>";
        }
        
        // Get all offers
        $offerStmt = $pdo->prepare("SELECT id, title, price_type FROM offers ORDER BY id LIMIT 20");
        $offerStmt->execute();
        $offers = $offerStmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Offers (" . count($offers) . " found)</h3>";
        
        if (!empty($offers)) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Price Type</th>
                    </tr>";
            
            foreach ($offers as $offer) {
                echo "<tr>
                        <td>" . htmlspecialchars($offer['id']) . "</td>
                        <td>" . htmlspecialchars($offer['title']) . "</td>
                        <td>" . htmlspecialchars($offer['price_type']) . "</td>
                      </tr>";
            }
            
            echo "</table>";
        }
    } catch(PDOException $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Database connection failed</p>";
    if (isset($db_error)) {
        echo "<p>Error: " . $db_error . "</p>";
    }
}
?>