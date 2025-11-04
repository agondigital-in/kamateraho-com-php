<?php
// Cleanup test banners
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    $stmt = $pdo->prepare('DELETE FROM banners WHERE title LIKE "Test % Banner"');
    $stmt->execute();
    echo 'Test data cleaned up successfully!';
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>