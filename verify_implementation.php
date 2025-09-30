<?php
include 'config/db.php';

echo "<h1>Category Management Implementation Verification</h1>";

// Check if the required columns exist
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM categories LIKE 'price'");
    $priceColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SHOW COLUMNS FROM categories LIKE 'photo'");
    $photoColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($priceColumn && $photoColumn) {
        echo "<p style='color: green;'>✓ Database columns (price and photo) exist in categories table</p>";
    } else {
        echo "<p style='color: red;'>✗ Missing required columns in categories table</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error checking database columns: " . $e->getMessage() . "</p>";
}

// Check if the required files exist
$requiredFiles = [
    'admin/add_category.php',
    'admin/edit_category.php',
    'admin/manage_categories.php',
    'index.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ File exists: $file</p>";
    } else {
        echo "<p style='color: red;'>✗ Missing file: $file</p>";
    }
}

// Check if the upload directory exists
$uploadDir = 'uploads/categories/';
if (is_dir($uploadDir)) {
    echo "<p style='color: green;'>✓ Upload directory exists: $uploadDir</p>";
} else {
    echo "<p style='color: red;'>✗ Upload directory missing: $uploadDir</p>";
}

// Check if we can fetch categories with price and photo
try {
    $stmt = $pdo->query("SELECT id, name, price, photo FROM categories LIMIT 1");
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        echo "<p style='color: green;'>✓ Can fetch categories with price and photo columns</p>";
        echo "<h2>Sample Category Data:</h2>";
        echo "<pre>" . print_r($category, true) . "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠ No categories found in database</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error fetching categories: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>View Frontend</a> | <a href='admin/login.php'>Access Admin Panel</a></p>";
?>