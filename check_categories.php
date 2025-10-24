<?php
// Script to check available categories
include 'config/db.php';

try {
    $stmt = $pdo->query('SELECT id, name FROM categories');
    echo "Available categories:\n";
    while ($row = $stmt->fetch()) {
        echo $row['id'] . ': ' . $row['name'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage() . "\n";
}
?>