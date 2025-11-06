<?php
// Migration to add screenshot column to contact_messages table
include __DIR__ . '/../config/db.php';

try {
    // Add screenshot column to contact_messages table
    $stmt = $pdo->prepare("ALTER TABLE contact_messages ADD COLUMN screenshot VARCHAR(255) NULL AFTER message");
    $stmt->execute();
    
    echo "Migration successful: Added screenshot column to contact_messages table\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
?>