<?php
// Simple web interface to create notifications table
// Access this file through your browser: http://localhost/kmt/create_notifications_table_web.php

echo "<!DOCTYPE html>
<html>
<head>
    <title>Create Notifications Table</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f7fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .success { color: green; padding: 15px; background: #e8f5e9; border-radius: 5px; margin: 15px 0; }
        .error { color: red; padding: 15px; background: #ffebee; border-radius: 5px; margin: 15px 0; }
        .info { color: #1a2a6c; padding: 15px; background: #e3f2fd; border-radius: 5px; margin: 15px 0; }
        button { background: #1a2a6c; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #2c3e8f; }
        a { color: #1a2a6c; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Create Notifications Table</h1>";

if (isset($_POST['create_table'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kamateraho";
    
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
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
        echo "<div class='success'>✓ Notifications table created successfully!</div>";
        
        // Add sample notifications for first 3 users
        try {
            $stmt = $pdo->query("SELECT id, name FROM users ORDER BY id LIMIT 3");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $notificationCount = 0;
            foreach ($users as $user) {
                $message = "Welcome to KamateRaho, " . $user['name'] . "! This is a sample notification to test the system.";
                $insertStmt = $pdo->prepare("INSERT IGNORE INTO notifications (user_id, message) VALUES (?, ?)");
                $insertStmt->execute([$user['id'], $message]);
                $notificationCount++;
            }
            
            echo "<div class='success'>✓ Added sample notifications for " . $notificationCount . " users</div>";
        } catch (PDOException $e) {
            echo "<div class='info'>Note: Could not add sample notifications, but table was created successfully.</div>";
        }
        
        echo "<div class='info'>
                <h3>Next Steps:</h3>
                <ol>
                    <li><a href='admin/send_notification.php'>Send notifications to users from the admin panel</a></li>
                    <li><a href='admin/admin_notifications.php'>View all notifications in the admin panel</a></li>
                    <li>Users can check their notifications in their dashboard</li>
                </ol>
              </div>";
        
    } catch(PDOException $e) {
        echo "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='info'>
            <p>This script will create the notifications table in your database and add some sample notifications for testing.</p>
            <p>Click the button below to proceed:</p>
          </div>
          <form method='POST'>
              <button type='submit' name='create_table'>Create Notifications Table</button>
          </form>";
}

echo "<p style='margin-top: 30px;'><a href='index.php'>← Back to Home</a></p>
    </div>
</body>
</html>";
?>