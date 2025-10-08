<?php
require_once 'config/db.php';

echo "<h2>Applying Notification System Updates</h2>";

// Create notifications table
try {
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
    echo "<p style='color: green;'>✓ Notifications table created successfully</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error creating notifications table: " . $e->getMessage() . "</p>";
}

// Add sample notifications for existing users
try {
    // Check if there are any users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount > 0) {
        // Get all user IDs
        $stmt = $pdo->query("SELECT id, name FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Insert sample notifications for each user
        foreach ($users as $user) {
            $message = "Welcome to KamateRaho, " . $user['name'] . "! Thank you for joining our platform.";
            $stmt = $pdo->prepare("INSERT IGNORE INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->execute([$user['id'], $message]);
        }
        
        echo "<p style='color: green;'>✓ Sample notifications added for existing users</p>";
    } else {
        echo "<p>No existing users found. No sample notifications added.</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error adding sample notifications: " . $e->getMessage() . "</p>";
}

echo "<p style='color: blue; font-weight: bold;'>All updates applied successfully!</p>";
echo "<p><a href='admin/admin_notifications.php'>Go to Admin Notifications</a></p>";
echo "<p><a href='user_notifications.php'>Go to User Notifications</a></p>";
?>