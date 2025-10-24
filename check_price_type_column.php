<?php
// Script to check the price_type column structure
include 'config/db.php';

try {
    $stmt = $pdo->query("SHOW COLUMNS FROM offers LIKE 'price_type'");
    $row = $stmt->fetch();
    print_r($row);
} catch (PDOException $e) {
    echo "Error checking price_type column: " . $e->getMessage() . "\n";
}
?>