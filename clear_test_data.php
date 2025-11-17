<?php
require_once 'config/db.php';

if ($pdo) {
    // Clear test data
    $stmt = $pdo->prepare('DELETE FROM email_verification WHERE email = ?');
    $stmt->execute(['test@example.com']);
    
    $stmt = $pdo->prepare('DELETE FROM users WHERE email = ?');
    $stmt->execute(['test@example.com']);
    
    echo "Test data cleared!\n";
} else {
    echo "Database connection failed!\n";
}
?>