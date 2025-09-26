<?php
// Test database connection
include 'db.php';

try {
    // Check if connection is successful
    echo "Database connection successful!";
    
    // Check if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<br>No tables found. Please create tables.";
    } else {
        echo "<br>Existing tables: " . implode(", ", $tables);
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>