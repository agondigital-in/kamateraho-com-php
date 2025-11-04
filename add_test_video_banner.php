<?php
// Add a test video banner
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test Video Banner',
        '', // No image URL for video
        'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
        'video',
        'https://example.com',
        0,
        1
    ]);
    
    echo "Test video banner added successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>