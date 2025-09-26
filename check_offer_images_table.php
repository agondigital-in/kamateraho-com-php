<?php
include 'config/db.php';

echo "<h1>Check offer_images Table</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
} else {
    try {
        // Check if the offer_images table exists
        $stmt = $pdo->prepare("SHOW TABLES LIKE 'offer_images'");
        $stmt->execute();
        $tableExists = $stmt->fetch();
        
        if ($tableExists) {
            echo "<p style='color: green; font-weight: bold;'>offer_images table exists</p>";
            
            // Show table structure
            $stmt = $pdo->query("DESCRIBE offer_images");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Table structure:</h3>";
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
        } else {
            echo "<p style='color: red; font-weight: bold;'>offer_images table does not exist</p>";
            echo "<p>You need to create the offer_images table by running the SQL script:</p>";
            echo "<p><code>database/create_offer_images_table.sql</code></p>";
        }
        
    } catch (PDOException $e) {
        echo "<p>Error checking table: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='admin/upload_offer.php'>Try uploading again</a></p>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>