<?php
/**
 * Script to update the credit_cards table to add amount, percentage, and flat_rate fields
 */

include '../config/db.php';

try {
    // Add amount, percentage, and flat_rate columns to credit_cards table
    $sql = "ALTER TABLE credit_cards 
            ADD COLUMN amount DECIMAL(10, 2) DEFAULT 0.00,
            ADD COLUMN percentage DECIMAL(5, 2) DEFAULT 0.00,
            ADD COLUMN flat_rate DECIMAL(10, 2) DEFAULT 0.00";
    
    $pdo->exec($sql);
    echo "Table 'credit_cards' updated successfully with amount fields!\n";
} catch(PDOException $e) {
    // Check if the error is because columns already exist
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Columns already exist in 'credit_cards' table.\n";
    } else {
        echo "Error updating table: " . $e->getMessage() . "\n";
    }
}
?>