<?php
// Update banners table to add video_type column
include __DIR__ . '/../config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    // Check if video_type column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM banners LIKE 'video_type'");
    if ($stmt->rowCount() == 0) {
        // Add video_type column to distinguish between direct video and YouTube
        $sql = "ALTER TABLE banners ADD COLUMN video_type ENUM('direct', 'youtube') DEFAULT 'direct' AFTER media_type";
        $pdo->exec($sql);
        echo "Added video_type column\n";
    } else {
        echo "video_type column already exists\n";
    }
    
    echo "Banners table updated successfully with video type support!";
} catch(PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>