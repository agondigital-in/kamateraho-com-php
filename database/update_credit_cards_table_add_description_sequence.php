<?php
/**
 * Script to update the credit_cards table to:
 * 1. Increase the size of the link column
 * 2. Add description and sequence_id columns
 */

include 'config/db.php';

echo "<h2>Updating Credit Cards Table</h2>";

try {
    // Modify link column to be TEXT type to accommodate longer URLs
    // Add description column for card descriptions
    // Add sequence_id column for ordering cards
    $sql = "ALTER TABLE credit_cards 
            MODIFY COLUMN link TEXT,
            ADD COLUMN description TEXT,
            ADD COLUMN sequence_id INT(11) DEFAULT 0";
    
    $pdo->exec($sql);
    echo "<p style='color: green;'>Table 'credit_cards' updated successfully!</p>";
    echo "<ul>";
    echo "<li>Link column modified to TEXT type</li>";
    echo "<li>Description column added</li>";
    echo "<li>Sequence ID column added</li>";
    echo "</ul>";
} catch(PDOException $e) {
    // Check if the error is because columns already exist
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "<p style='color: blue;'>Some columns already exist in 'credit_cards' table.</p>";
    } else {
        echo "<p style='color: red;'>Error updating table: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='admin/manage_credit_cards.php'>Go to Manage Credit Cards</a></p>";
?>