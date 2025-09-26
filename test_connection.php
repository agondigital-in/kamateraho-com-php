<?php
include 'config/db.php';

echo "<h1>Database Connection Test</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    echo "<p>Please check your database configuration in the .env file.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>The MySQL service is running</li>";
    echo "<li>The database credentials in .env are correct</li>";
    echo "<li>The database 'kamateraho' exists</li>";
    echo "</ul>";
} else {
    echo "<p style='color: green; font-weight: bold;'>Database connection successful!</p>";
    
    // Try to list tables
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (count($tables) > 0) {
            echo "<p>Existing tables: " . implode(', ', $tables) . "</p>";
        } else {
            echo "<p>No tables found in the database.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error fetching tables: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='setup_db.php'>Setup Database</a> | <a href='index.php'>Back to Homepage</a></p>";
?>