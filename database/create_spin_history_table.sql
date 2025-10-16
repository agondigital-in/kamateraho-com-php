-- Create spin_history table for tracking user spins
CREATE TABLE IF NOT EXISTS spin_history (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    reward_amount DECIMAL(10, 2) DEFAULT 0.00,
    spin_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, spin_date)
);