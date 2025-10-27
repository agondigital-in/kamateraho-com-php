<?php
// Script to increase the length of redirect_url column in offers table
include __DIR__ . '/../config/db.php';

try {
    // Check current column definition
    $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'redirect_url'");
    $stmt->execute();
    $columnInfo = $stmt->fetch();
    
    if ($columnInfo) {
        echo "<h2>Current redirect_url column definition:</h2>";
        echo "<pre>";
        print_r($columnInfo);
        echo "</pre>";
        
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
        echo "<p style='color: red;'>Column 'redirect_url' not found in offers table</p>";
    }
    
    echo "<p style='color: blue; font-weight: bold;'>Database update completed successfully!</p>";
} catch(PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>Error: " . $e->getMessage() . "</p>";
}
?>