-- Create offer_images table for storing multiple images per offer
CREATE TABLE IF NOT EXISTS offer_images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    offer_id INT(11) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE,
    INDEX idx_offer_images_offer_id (offer_id)
);