<?php
include 'config/db.php';

echo "<h1>Users Table Structure Check</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
} else {
    try {
        // Get table structure
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Current 'users' table structure:</h3>";
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
        echo "<p>Error describing table: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>