<?php
// Verify banners table structure after update
include __DIR__ . '/../config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    // Check table structure
    $stmt = $pdo->query("DESCRIBE banners");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Banners table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column['Field']}: {$column['Type']}";
        if ($column['Null'] === 'YES') {
            echo " (NULL)";
        }
        if (!empty($column['Default'])) {
            echo " DEFAULT {$column['Default']}";
        }
        echo "\n";
    }
    
    // Test inserting a video banner
    $stmt = $pdo->prepare("INSERT INTO banners (title, image_url, video_url, media_type, redirect_url, sequence_id, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Test Video Banner',
        '', // No image URL for video
        'https://example.com/test-video.mp4',
        'video',
        'https://example.com/redirect',
        0,
        1
    ]);
    
    echo "\nTest video banner inserted successfully!\n";
    
    // Fetch and display the inserted banner
    $stmt = $pdo->query("SELECT * FROM banners WHERE title = 'Test Video Banner' ORDER BY id DESC LIMIT 1");
    $banner = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($banner) {
        echo "\nInserted banner details:\n";
        foreach ($banner as $key => $value) {
            echo "- $key: $value\n";
        }
    }
    
    // Clean up test data
    $stmt = $pdo->prepare("DELETE FROM banners WHERE title = 'Test Video Banner'");
    $stmt->execute();
    
    echo "\nTest data cleaned up successfully!\n";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>