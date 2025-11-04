<?php
// Add a test YouTube banner
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, video_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test YouTube Banner',
        '', // No image URL for video
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rickroll video
        'video',
        'youtube',
        'https://example.com',
        0,
        1
    ]);
    
    echo "Test YouTube banner added successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>