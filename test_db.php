<?php
include 'config/db.php';

echo "<h1>Database Connection Test</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    echo "<p>Please check your database configuration in the .env file.</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>The database server is running</li>";
    echo "<li>The database 'kamateraho' exists</li>";
    echo "<li>The username and password are correct</li>";
    echo "</ul>";
    echo "<p>You can create the database by running <a href='init.php'>init.php</a></p>";
} else {
    echo "<p style='color: green; font-weight: bold;'>Database connection successful!</p>";
    
    // Try to list tables
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Existing tables: " . implode(', ', $tables) . "</p>";
    } catch (PDOException $e) {
        echo "<p>Error fetching tables: " . $e->getMessage() . "</p>";
        echo "<p>The database might be empty. Run <a href='init.php'>init.php</a> to create the tables.</p>";
    }
}

echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>