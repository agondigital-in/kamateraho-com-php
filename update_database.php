<?php
// Update database schema for withdraw_requests table
include 'config/db.php';

try {
    // Read the SQL file
    $sql = file_get_contents('database/update_users_table.sql');
    
    // Execute the SQL commands
    $pdo->exec($sql);
    
    echo "Database updated successfully! The users table now includes phone, city, and state columns.\n";
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>