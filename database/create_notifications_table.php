<?php
// Script to create the notifications table
include '../config/db.php';

try {
    // Create notifications table
    $sql = "
    CREATE TABLE IF NOT EXISTS notifications (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_notifications_user_id (user_id),
        INDEX idx_notifications_is_read (is_read)
    )";
    
    $pdo->exec($sql);
    echo "Notifications table created successfully!\n";
    
    // Add sample notifications for testing
    $stmt = $pdo->prepare("SELECT id, name FROM users LIMIT 5");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($users as $user) {
        $message = "Welcome to KamateRaho, " . $user['name'] . "! This is a sample notification.";
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO notifications (user_id, message) VALUES (?, ?)");
        $insertStmt->execute([$user['id'], $message]);
    }
    
    echo "Sample notifications added for testing!\n";
    echo "You can now use the notification system.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>