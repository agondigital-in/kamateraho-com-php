<?php
// Script to add is_active column to offers table
include __DIR__ . '/../config/db.php';

try {
    // Check if the column already exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'is_active'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add is_active column to offers table with default value TRUE
        $sql = "ALTER TABLE offers ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER redirect_url";
        $pdo->exec($sql);
        
        echo "Column 'is_active' added to 'offers' table successfully!\n";
    } else {
        echo "Column 'is_active' already exists in 'offers' table.\n";
    }
    
    // Set all existing offers to active by default (in case they were NULL)
    $sql = "UPDATE offers SET is_active = TRUE WHERE is_active IS NULL";
    $pdo->exec($sql);
    
    echo "All existing offers set to active status.\n";
    echo "Database update completed successfully!\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>