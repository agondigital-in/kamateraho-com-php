<?php
/**
 * Script to create the credit_cards table
 * This ensures the table exists with the correct structure
 */

include '../config/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS credit_cards (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        image VARCHAR(255) NOT NULL,
        link VARCHAR(500) NOT NULL,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Table 'credit_cards' created successfully or already exists!\n";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>