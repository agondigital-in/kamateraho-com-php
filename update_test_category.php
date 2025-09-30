<?php
include 'config/db.php';

// Update the test category with the actual image path
try {
    $stmt = $pdo->prepare("UPDATE categories SET photo = ? WHERE name = ?");
    $stmt->execute([
        'uploads/categories/test-photo.jpg',
        'Test Category with Price and Photo'
    ]);
    
    echo "Test category updated with image path successfully!";
} catch (PDOException $e) {
    echo "Error updating test category: " . $e->getMessage();
}
?>