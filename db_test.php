<?php
/**
 * Simple database connection test
 */

include 'config/db.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test connection
    $stmt = $pdo->query("SELECT 1");
    echo "<p style='color: green;'>Database connection successful!</p>";
    
    // Check if credit_cards table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'credit_cards'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>Credit cards table exists.</p>";
    } else {
        echo "<p style='color: red;'>Credit cards table does not exist.</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color: red;'>Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<p><a href='admin/manage_credit_cards.php'>Go to Manage Credit Cards</a></p>";
?>