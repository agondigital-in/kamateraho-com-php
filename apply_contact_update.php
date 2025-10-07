<?php
// Apply contact messages table update
include 'config/db.php';

try {
    // Read the SQL file
    $sql = file_get_contents('database/update_users_table.sql');
    
    // Execute the SQL commands
    $pdo->exec($sql);
    
    echo "Database updated successfully! The contact_messages table now includes user_id column.\n";
    echo "<br><a href='index.php'>Go back to homepage</a>";
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>