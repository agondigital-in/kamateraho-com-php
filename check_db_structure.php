<?php
require 'config/db.php';

if ($pdo) {
    echo "Database connection successful!\n\n";
    
    // Check users table structure
    echo "Users table structure:\n";
    $stmt = $pdo->query('DESCRIBE users');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
    
    echo "\nAll tables in database:\n";
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- " . $table . "\n";
    }
} else {
    echo "Database connection failed: " . ($db_error ?? 'Unknown error') . "\n";
}
?>