<?php
// Test database connection and insertion
include 'config/db.php';

echo "<h2>Database Connection Test</h2>";

if ($pdo) {
    echo "<p>✅ Database connection successful</p>";
    
    // Test inserting a record with the exact same structure as product_details.php
    try {
        echo "<h3>Testing Insertion (Exact Copy of product_details.php Logic)</h3>";
        
        // Simulate the data that would be inserted from product_details.php
        $user_id = 1;
        $amount = 100.00;
        $upi_id = "purchase@" . time();
        $screenshot = "";
        $offer_title = "Test Offer";
        $offer_description = "Test Description";
        
        echo "<p>Inserting data:</p>";
        echo "<ul>";
        echo "<li>User ID: " . $user_id . "</li>";
        echo "<li>Amount: " . $amount . "</li>";
        echo "<li>UPI ID: " . $upi_id . "</li>";
        echo "<li>Offer Title: " . $offer_title . "</li>";
        echo "</ul>";
        
        // Insert withdraw request with special UPI ID to identify it as a purchase
        $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot, offer_title, offer_description) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $user_id, 
            $amount, 
            $upi_id, 
            $screenshot,
            $offer_title,
            $offer_description
        ]);
        
        if ($result) {
            $request_id = $pdo->lastInsertId();
            echo "<p>✅ Record inserted successfully with ID: " . $request_id . "</p>";
            
            // Test fetching the record
            $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
            $stmt->execute([$request_id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($request) {
                echo "<p>✅ Record found in database:</p>";
                echo "<pre>";
                print_r($request);
                echo "</pre>";
                
                // Check if it would appear in the admin panel
                if (strpos($request['upi_id'], 'purchase@') === 0) {
                    echo "<p>✅ This request would appear as a 'Purchase Request' in the admin panel</p>";
                } else {
                    echo "<p>❌ This request would NOT appear as a 'Purchase Request' in the admin panel</p>";
                }
            } else {
                echo "<p>❌ Record not found in database</p>";
            }
            
            // Clean up - delete the test record
            $stmt = $pdo->prepare("DELETE FROM withdraw_requests WHERE id = ?");
            $stmt->execute([$request_id]);
            echo "<p>🧹 Test record cleaned up</p>";
        } else {
            echo "<p>❌ Failed to insert record</p>";
            echo "<p>Error info: ";
            print_r($stmt->errorInfo());
            echo "</p>";
        }
    } catch(PDOException $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
    
    // Test fetching pending requests with the exact same query as admin panel
    try {
        echo "<h3>Testing Fetching Pending Requests (Exact Copy of Admin Panel Query)</h3>";
        $sql = "SELECT wr.*, u.name, u.email, u.id as user_id FROM withdraw_requests wr 
                JOIN users u ON wr.user_id = u.id 
                WHERE wr.status = 'pending' AND wr.upi_id LIKE 'purchase@%'";
        
        echo "<p>Executing query: " . $sql . "</p>";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Found " . count($requests) . " pending purchase requests</p>";
        
        if (!empty($requests)) {
            echo "<p>Sample pending request:</p>";
            echo "<pre>";
            print_r($requests[0]);
            echo "</pre>";
        } else {
            echo "<p>No pending purchase requests found</p>";
        }
    } catch(PDOException $e) {
        echo "<p>❌ Error fetching pending requests: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>❌ Database connection failed</p>";
    if (isset($db_error)) {
        echo "<p>Error: " . $db_error . "</p>";
    }
}
?>