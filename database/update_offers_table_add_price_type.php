<?php
// Add price_type column to offers table
include __DIR__ . '/../config/db.php';

try {
    // Check if price_type column already exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'price_type'");
    $stmt->execute();
    $columnExists = $stmt->fetch();
    
    if (!$columnExists) {
        // Add price_type column to offers table
        $sql = "ALTER TABLE offers ADD COLUMN price_type ENUM('fixed', 'flat_percent', 'upto_percent') DEFAULT 'fixed' AFTER price";
        $pdo->exec($sql);
        
        echo "Column 'price_type' added to 'offers' table successfully.\n";
        
        // Create index on price_type for better performance
        $sql = "CREATE INDEX idx_offers_price_type ON offers(price_type)";
        $pdo->exec($sql);
        
        echo "Index 'idx_offers_price_type' created successfully.\n";
        
        // Set all existing offers to 'fixed' type
        $sql = "UPDATE offers SET price_type = 'fixed'";
        $pdo->exec($sql);
        
        echo "Set all existing offers to 'fixed' price type.\n";
    } else {
        echo "Column 'price_type' already exists in 'offers' table.\n";
    }
    
    echo "Migration completed successfully!\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>