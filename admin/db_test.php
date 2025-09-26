<?php
include '../config/db.php';

echo "Database connection test:\n";

try {
    // Test database connection
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "Database connection: SUCCESS\n";
    
    // Check if users table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "Users table row count: " . $result['count'] . "\n";
    
    // Check if withdraw_requests table exists and has data
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM withdraw_requests");
    $result = $stmt->fetch();
    echo "Withdraw requests table row count: " . $result['count'] . "\n";
    
    // Display table structures
    echo "\nTable structures:\n";
    
    // Users table
    echo "Users table:\n";
    $stmt = $pdo->query("DESCRIBE users");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\nWithdraw requests table:\n";
    $stmt = $pdo->query("DESCRIBE withdraw_requests");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>