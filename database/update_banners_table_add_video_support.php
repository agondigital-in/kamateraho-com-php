<?php
// Update banners table to add video support
include __DIR__ . '/../config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    // Check if video_url column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM banners LIKE 'video_url'");
    if ($stmt->rowCount() == 0) {
        // Add video_url column if it doesn't exist
        $sql = "ALTER TABLE banners ADD COLUMN video_url VARCHAR(500) NULL AFTER image_url";
        $pdo->exec($sql);
        echo "Added video_url column\n";
    } else {
        echo "video_url column already exists\n";
    }
    
    // Check if media_type column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM banners LIKE 'media_type'");
    if ($stmt->rowCount() == 0) {
        // Add media_type column to distinguish between image and video
        $sql = "ALTER TABLE banners ADD COLUMN media_type ENUM('image', 'video') DEFAULT 'image' AFTER video_url";
        $pdo->exec($sql);
        echo "Added media_type column\n";
    } else {
        echo "media_type column already exists\n";
    }
    
    echo "Banners table updated successfully with video support!";
} catch(PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>