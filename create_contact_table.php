<?php
// Script to create contact_messages table
include 'config/db.php';

// Check if PDO connection is successful
if ($pdo === null) {
    die("Database connection failed\n");
}

echo "Database connection successful\n";

$sql = "
-- Create contact_messages table for handling user inquiries and admin replies
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    reply TEXT NULL,
    status ENUM('pending', 'replied') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    replied_at TIMESTAMP NULL
);
";

try {
    $pdo->exec($sql);
    echo "Table contact_messages created successfully\n";
    
    // Verify table creation
    $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
    if ($stmt->rowCount() > 0) {
        echo "Table verification successful\n";
    } else {
        echo "Table may not have been created\n";
    }
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>