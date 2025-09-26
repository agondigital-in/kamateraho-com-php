<?php
include 'config/db.php';

if ($pdo === null) {
    echo "Database connection failed.\n";
    echo "Please check your database configuration in the .env file.\n";
    echo "Make sure the database 'kamateraho' exists.\n";
    echo "You can create it by running init.php\n";
} else {
    echo "Database connection successful!\n";
    
    // Try to list tables
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Existing tables: " . implode(', ', $tables) . "\n";
    } catch (PDOException $e) {
        echo "Error fetching tables: " . $e->getMessage() . "\n";
    }
}
?>