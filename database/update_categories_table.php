<?php
include __DIR__ . '/../config/db.php';

try {
    // Check if price column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM categories LIKE 'price'");
    $priceColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$priceColumn) {
        // Add price column to categories table
        $stmt = $pdo->prepare("ALTER TABLE categories ADD COLUMN price DECIMAL(10, 2) DEFAULT NULL");
        $stmt->execute();
        echo "Price column added to categories table.\n";
    } else {
        echo "Price column already exists in categories table.\n";
    }
    
    // Check if photo column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM categories LIKE 'photo'");
    $photoColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$photoColumn) {
        // Add photo column to categories table
        $stmt = $pdo->prepare("ALTER TABLE categories ADD COLUMN photo VARCHAR(255) DEFAULT NULL");
        $stmt->execute();
        echo "Photo column added to categories table.\n";
    } else {
        echo "Photo column already exists in categories table.\n";
    }
    
} catch (PDOException $e) {
    echo "Error updating categories table: " . $e->getMessage() . "\n";
}
?>