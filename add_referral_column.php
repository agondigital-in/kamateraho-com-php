<?php
include 'config/db.php';

echo "<h1>Add Referral Code Column to Users Table</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
} else {
    try {
        // Add referral_code column to users table
        $sql = "ALTER TABLE users ADD COLUMN referral_code VARCHAR(50) AFTER state";
        
        $pdo->exec($sql);
        echo "<p style='color: green; font-weight: bold;'>Referral code column added successfully to users table.</p>";
        
        // Add index for better performance
        $sql = "CREATE INDEX idx_users_referral_code ON users(referral_code)";
        $pdo->exec($sql);
        echo "<p style='color: green; font-weight: bold;'>Index created for referral_code column.</p>";
        
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
        // Check if the column already exists
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "<p style='color: orange; font-weight: bold;'>Referral code column already exists in users table.</p>";
        } else {
            echo "<p style='color: red;'>Error adding referral code column: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<p><a href='register.php'>Try Registration</a> | <a href='index.php'>Back to Homepage</a></p>";
?>