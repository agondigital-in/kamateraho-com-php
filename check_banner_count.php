<?php
// Check banner count
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    // Check how many active banners exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM banners WHERE is_active = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $activeBannerCount = $result['count'];
    
    echo "Active banners: $activeBannerCount\n";
    
    // Check how many inactive banners exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM banners WHERE is_active = 0");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $inactiveBannerCount = $result['count'];
    
    echo "Inactive banners: $inactiveBannerCount\n";
    
    // Display active banners
    if ($activeBannerCount > 0) {
        $stmt = $pdo->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY sequence_id ASC, created_at DESC");
        $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nActive banners details:\n";
        foreach ($banners as $banner) {
            echo "- {$banner['title']} ({$banner['media_type']}" . 
                 (!empty($banner['video_type']) ? "/{$banner['video_type']}" : "") . 
                 ") - Sequence: {$banner['sequence_id']}\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>