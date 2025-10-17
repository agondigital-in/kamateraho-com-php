<?php
include 'config/db.php';

echo "<h2>Test Credit Card Toggle Functionality</h2>";

if ($pdo) {
    // Get a credit card to test with
    try {
        $stmt = $pdo->query("SELECT id, title, is_active FROM credit_cards LIMIT 1");
        $card = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($card) {
            echo "<p>Testing with card ID: " . $card['id'] . " - Title: " . htmlspecialchars($card['title']) . "</p>";
            echo "<p>Current status: " . ($card['is_active'] ? 'Active' : 'Inactive') . "</p>";
            
            // Toggle the status
            $new_status = $card['is_active'] ? 0 : 1;
            echo "<p>Attempting to change status to: " . ($new_status ? 'Active' : 'Inactive') . "</p>";
            
            // Update the status
            $stmt = $pdo->prepare("UPDATE credit_cards SET is_active = ? WHERE id = ?");
            $result = $stmt->execute([$new_status, $card['id']]);
            
            if ($result) {
                echo "<p style='color: green;'>✓ Status updated successfully in database</p>";
                
                // Verify the change
                $stmt = $pdo->prepare("SELECT is_active FROM credit_cards WHERE id = ?");
                $stmt->execute([$card['id']]);
                $updated_card = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($updated_card) {
                    echo "<p>Verified status in database: " . ($updated_card['is_active'] ? 'Active' : 'Inactive') . "</p>";
                    echo "<p>Match expected: " . ($updated_card['is_active'] == $new_status ? 'Yes' : 'No') . "</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ Failed to update status</p>";
            }
        } else {
            echo "<p>No credit cards found in database to test with.</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?>