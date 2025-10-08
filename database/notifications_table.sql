-- Create notifications table for admin messaging system
-- Run this SQL directly in your MySQL database

USE kamateraho;

CREATE TABLE IF NOT EXISTS notifications (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user_id (user_id),
    INDEX idx_notifications_is_read (is_read)
);

-- Add sample notifications for testing (optional)
-- Uncomment the following lines if you want to add sample notifications

-- INSERT INTO notifications (user_id, message) 
-- SELECT id, CONCAT('Welcome to KamateRaho, ', name, '! This is a sample notification.') 
-- FROM users 
-- LIMIT 5;