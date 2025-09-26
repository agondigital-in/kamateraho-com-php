<?php
include 'config/db.php';

echo "<h1>Update Users Table</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
} else {
    try {
        // Add phone, city, and state columns to users table
        $sql = "ALTER TABLE users 
                ADD COLUMN phone VARCHAR(20) AFTER email,
                ADD COLUMN city VARCHAR(100) AFTER phone,
                ADD COLUMN state VARCHAR(100) AFTER city";
        
        $pdo->exec($sql);
        echo "<p style='color: green; font-weight: bold;'>Users table updated successfully! Added phone, city, and state columns.</p>";
        
        // Verify the changes
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Updated 'users' table structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        foreach (array_keys($columns[0]) as $header) {
            echo "<th style='padding: 8px; text-align: left;'>" . htmlspecialchars($header) . "</th>";
        }
        echo "</tr>";
        
        foreach ($columns as $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($cell ?? 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error updating table: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='register.php'>Try Registration</a> | <a href='index.php'>Back to Homepage</a></p>";
?>