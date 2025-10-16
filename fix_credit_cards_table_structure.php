<?php
/**
 * Manual script to update the credit_cards table structure
 * Run this script if you encounter database errors with the credit cards feature
 */

// Include database connection
include 'config/db.php';

// Check if we can connect to the database
if ($pdo === null) {
    die("Database connection failed. Please check your database configuration.");
}

echo "<h2>Credit Cards Table Update Script</h2>\n";

try {
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE credit_cards");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $has_description = in_array('description', $columns);
    $has_sequence_id = in_array('sequence_id', $columns);
    
    // Check link column type
    $stmt = $pdo->query("SHOW COLUMNS FROM credit_cards LIKE 'link'");
    $link_column = $stmt->fetch(PDO::FETCH_ASSOC);
    $link_is_text = strpos(strtoupper($link_column['Type']), 'TEXT') !== false;
    
    echo "<p>Current table status:</p>\n";
    echo "<ul>\n";
    echo "<li>Description column: " . ($has_description ? "Exists" : "Missing") . "</li>\n";
    echo "<li>Sequence ID column: " . ($has_sequence_id ? "Exists" : "Missing") . "</li>\n";
    echo "<li>Link column type: " . ($link_is_text ? "TEXT (OK)" : "VARCHAR (Needs update)") . "</li>\n";
    echo "</ul>\n";
    
    // Perform updates only if needed
    $updates_needed = [];
    
    if (!$has_description) {
        $updates_needed[] = "ADD COLUMN description TEXT";
    }
    
    if (!$has_sequence_id) {
        $updates_needed[] = "ADD COLUMN sequence_id INT(11) DEFAULT 0";
    }
    
    if (!$link_is_text) {
        $updates_needed[] = "MODIFY COLUMN link TEXT";
    }
    
    if (count($updates_needed) > 0) {
        echo "<p>Performing updates...</p>\n";
        
        $sql = "ALTER TABLE credit_cards " . implode(", ", $updates_needed);
        $pdo->exec($sql);
        
        echo "<p style='color: green;'>Table updated successfully!</p>\n";
        echo "<p>Changes made:</p>\n";
        echo "<ul>\n";
        foreach ($updates_needed as $update) {
            echo "<li>$update</li>\n";
        }
        echo "</ul>\n";
    } else {
        echo "<p style='color: green;'>Table structure is already up to date. No changes needed.</p>\n";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database permissions and try again.</p>\n";
}

echo "<p><a href='admin/manage_credit_cards.php'>Go to Manage Credit Cards</a></p>\n";
?>