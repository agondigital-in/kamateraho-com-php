<?php
/**
 * Web interface to update the credit_cards table to add amount, percentage, and flat_rate fields
 */

include 'config/db.php';

echo "<h2>Updating Credit Cards Table</h2>";

try {
    // Add amount, percentage, and flat_rate columns to credit_cards table
    $sql = "ALTER TABLE credit_cards 
            ADD COLUMN amount DECIMAL(10, 2) DEFAULT 0.00,
            ADD COLUMN percentage DECIMAL(5, 2) DEFAULT 0.00,
            ADD COLUMN flat_rate DECIMAL(10, 2) DEFAULT 0.00";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>Table 'credit_cards' updated successfully with amount fields!</p>";
} catch(PDOException $e) {
    // Check if the error is because columns already exist
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "<p style='color: blue;'>Columns already exist in 'credit_cards' table.</p>";
    } else {
        echo "<p style='color: red;'>Error updating table: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='admin/manage_credit_cards.php'>Go to Manage Credit Cards</a></p>";
?>