<?php
// Migration to add user_id column to contact_messages table
include __DIR__ . '/../config/db.php';

try {
    // Add user_id column to contact_messages table
    $stmt = $pdo->prepare("ALTER TABLE contact_messages ADD COLUMN user_id INT NULL AFTER id");
    $stmt->execute();
    
    // Add foreign key constraint
    $stmt = $pdo->prepare("ALTER TABLE contact_messages ADD CONSTRAINT fk_contact_messages_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL");
    $stmt->execute();
    
    echo "Migration successful: Added user_id column to contact_messages table\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>