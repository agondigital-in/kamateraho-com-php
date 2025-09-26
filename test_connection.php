<?php
include 'config/db.php';

if ($pdo) {
    echo "<h1>Database Connection Successful</h1>";
    echo "<p>Connected to kamateraho database.</p>";
    
    // Test if tables exist
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "<p>No tables found in the database.</p>";
            echo "<p><a href='init.php'>Click here to initialize the database</a></p>";
        } else {
            echo "<p>Existing tables: " . implode(", ", $tables) . "</p>";
            echo "<p><a href='index.php'>Go to Homepage</a></p>";
        }
    } catch(PDOException $e) {
        echo "<p>Error checking tables: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<h1>Database Connection Failed</h1>";
    echo "<p>Could not connect to the kamateraho database.</p>";
    echo "<p>Possible reasons:</p>";
    echo "<ul>";
    echo "<li>MySQL service is not running</li>";
    echo "<li>Database 'kamateraho' does not exist</li>";
    echo "<li>Incorrect database credentials in config/db.php</li>";
    echo "</ul>";
    echo "<p><a href='init.php'>Click here to initialize the database</a></p>";
}
?>