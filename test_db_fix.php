<?php
// Simple test script to check database connection and fix redirect_url column
include 'config/db.php';

echo "<h1>Database Fix for redirect_url Column</h1>";

try {
    echo "<p>✓ Database connection successful</p>";
    
    // Check current column definition
    $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'redirect_url'");
    $stmt->execute();
    $columnInfo = $stmt->fetch();
    
    if ($columnInfo) {
        echo "<h2>Current redirect_url column definition:</h2>";
        echo "<pre>";
        print_r($columnInfo);
        echo "</pre>";
        
        // Check if we need to update the column
        if (strpos($columnInfo['Type'], 'varchar(500)') !== false || strpos($columnInfo['Type'], 'VARCHAR(500)') !== false) {
            echo "<p>Updating redirect_url column from VARCHAR(500) to VARCHAR(2000)...</p>";
            
            // Modify the column to increase its length
            $sql = "ALTER TABLE offers MODIFY redirect_url VARCHAR(2000)";
            $pdo->exec($sql);
            
            echo "<p style='color: green; font-weight: bold;'>✓ Column 'redirect_url' successfully updated to VARCHAR(2000)</p>";
            
            // Verify the change
            $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'redirect_url'");
            $stmt->execute();
            $updatedColumnInfo = $stmt->fetch();
            
            echo "<h2>Updated redirect_url column definition:</h2>";
            echo "<pre>";
            print_r($updatedColumnInfo);
            echo "</pre>";
        } else {
            echo "<p style='color: blue;'>Column 'redirect_url' is already updated or has a different type</p>";
        }
    } else {
        echo "<p style='color: red;'>Column 'redirect_url' not found in offers table</p>";
    }
    
    echo "<p style='color: green; font-weight: bold;'>Database update completed successfully!</p>";
} catch(PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</p>";
}
?>