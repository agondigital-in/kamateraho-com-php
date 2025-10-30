<?php
// Test script to verify banners are fetched correctly
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    $stmt = $pdo->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY sequence_id ASC, created_at DESC");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Active Banners:</h2>";
    if (empty($banners)) {
        echo "<p>No active banners found.</p>";
    } else {
        echo "<ul>";
        foreach ($banners as $banner) {
            echo "<li>";
            echo "<strong>" . htmlspecialchars($banner['title']) . "</strong><br>";
            echo "Image: <a href='" . htmlspecialchars($banner['image_url']) . "' target='_blank'>" . htmlspecialchars($banner['image_url']) . "</a><br>";
            echo "Redirect: " . htmlspecialchars($banner['redirect_url']) . "<br>";
            echo "Sequence: " . $banner['sequence_id'] . "<br>";
            echo "</li><br>";
        }
        echo "</ul>";
    }
} catch(PDOException $e) {
    echo "Error fetching banners: " . $e->getMessage();
}
?>