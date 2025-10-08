<?php
// Simple script to run database updates through web browser
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed!");
}

echo "<h2>Running Database Updates for Notification System</h2>";

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
    echo "<p style='color: green;'>✓ Notifications table created successfully</p>";
    
    // Add sample notifications for testing (first 3 users)
    $stmt = $pdo->query("SELECT id, name FROM users ORDER BY id LIMIT 3");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $notificationCount = 0;
    foreach ($users as $user) {
        $message = "Welcome to KamateRaho, " . $user['name'] . "! This is a sample notification to test the system.";
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO notifications (user_id, message) VALUES (?, ?)");
        $insertStmt->execute([$user['id'], $message]);
        $notificationCount++;
    }
    
    echo "<p style='color: green;'>✓ Added sample notifications for " . $notificationCount . " users</p>";
    
    echo "<h3>Notification System is Ready!</h3>";
    echo "<p>You can now:</p>";
    echo "<ul>";
    echo "<li><a href='admin/send_notification.php'>Send notifications to users from the admin panel</a></li>";
    echo "<li><a href='admin/admin_notifications.php'>View all notifications in the admin panel</a></li>";
    echo "<li>Users can check their notifications in their dashboard</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>← Back to Home</a></p>";
?>