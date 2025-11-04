<?php
// Test script to verify single media banner functionality
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    // Test 1: Add an image banner
    $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test Image Banner',
        'https://sample-videos.com/images/jpg/1.jpg',
        '', // Empty video URL for image banner
        'image',
        'https://example.com',
        0,
        1
    ]);
    echo "Test 1: Image banner added successfully!\n";
    
    // Test 2: Add a direct video banner
    $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, video_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test Direct Video Banner',
        '', // Empty image URL for video banner
        'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4',
        'video',
        'direct',
        'https://example.com',
        0,
        1
    ]);
    echo "Test 2: Direct video banner added successfully!\n";
    
    // Test 3: Add a YouTube video banner
    $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, video_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test YouTube Banner',
        '', // Empty image URL for video banner
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'video',
        'youtube',
        'https://example.com',
        0,
        1
    ]);
    echo "Test 3: YouTube video banner added successfully!\n";
    
    // Display all banners
    $stmt = $pdo->query("SELECT * FROM banners ORDER BY id DESC LIMIT 3");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nRecently added banners:\n";
    foreach ($banners as $banner) {
        echo "- {$banner['title']} ({$banner['media_type']}" . (!empty($banner['video_type']) ? "/{$banner['video_type']}" : "") . ")\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>