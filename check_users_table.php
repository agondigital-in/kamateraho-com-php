<?php
require 'config/db.php';

if ($pdo) {
    echo "Database connection successful!\n\n";
    
    // Check users table structure
    echo "Users table structure:\n";
    $stmt = $pdo->query('DESCRIBE users');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ") " . 
             ($column['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . " " . 
             ($column['Default'] ? 'DEFAULT ' . $column['Default'] : '') . "\n";
    }
    
    // Check if there's an email verification table
    echo "\nChecking for email verification table:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'email_verification'");
    $table_exists = $stmt->fetch();
    if ($table_exists) {
        echo "Email verification table exists\n";
        $stmt = $pdo->query('DESCRIBE email_verification');
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    } else {
        echo "No email verification table found\n";
    }
} else {
    echo "Database connection failed: " . ($db_error ?? 'Unknown error') . "\n";
}
?>