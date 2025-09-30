<?php
include 'config/db.php';

// Add a test category with price and photo
try {
    $stmt = $pdo->prepare("INSERT INTO categories (name, price, photo) VALUES (?, ?, ?)");
    $stmt->execute([
        'Test Category with Price and Photo',
        99.99,
        'uploads/categories/test-photo.jpg'
    ]);
    
    echo "Test category added successfully!";
} catch (PDOException $e) {
    echo "Error adding test category: " . $e->getMessage();
}
?>