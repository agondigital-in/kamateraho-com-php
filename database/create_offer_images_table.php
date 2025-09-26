<?php
// Script to create the offer_images table
include 'config/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS offer_images (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        offer_id INT(11) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE,
        INDEX idx_offer_images_offer_id (offer_id)
    )";
    
    $pdo->exec($sql);
    echo "Table 'offer_images' created successfully!";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>