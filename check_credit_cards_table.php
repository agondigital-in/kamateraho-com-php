<?php
include 'config/db.php';

echo "<h2>Credit Cards Table Structure</h2>";

if ($pdo) {
    try {
        // Check if credit_cards table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'credit_cards'");
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "<p style='color: green;'>✓ credit_cards table exists</p>";
            
            // Show table structure
            $stmt = $pdo->query("DESCRIBE credit_cards");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Table structure:</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            foreach ($columns as $column) {
                echo "<tr>";
                echo "<td>" . $column['Field'] . "</td>";
                echo "<td>" . $column['Type'] . "</td>";
                echo "<td>" . $column['Null'] . "</td>";
                echo "<td>" . $column['Key'] . "</td>";
                echo "<td>" . ($column['Default'] ?? 'NULL') . "</td>";
                echo "<td>" . $column['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Check a few sample records
            $stmt = $pdo->query("SELECT id, title, is_active FROM credit_cards LIMIT 5");
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Sample records (id, title, is_active):</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr><th>ID</th><th>Title</th><th>is_active</th></tr>";
            
            foreach ($records as $record) {
                echo "<tr>";
                echo "<td>" . $record['id'] . "</td>";
                echo "<td>" . htmlspecialchars($record['title']) . "</td>";
                echo "<td>" . $record['is_active'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>✗ credit_cards table does not exist</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?>