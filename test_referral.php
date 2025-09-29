<?php
include 'config/db.php';

echo "<h1>Test Referral Functionality</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
} else {
    try {
        // Test 1: Check if referral_code column exists
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $referral_column_exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'referral_code') {
                $referral_column_exists = true;
                break;
            }
        }
        
        if ($referral_column_exists) {
            echo "<p style='color: green; font-weight: bold;'>✓ Referral code column exists in users table.</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>✗ Referral code column does not exist in users table.</p>";
        }
        
        // Test 2: Insert a test user with referral code
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, city, state, password, wallet_balance, referral_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            'Test User',
            'test@example.com',
            '1234567890',
            'Test City',
            'Test State',
            password_hash('password123', PASSWORD_DEFAULT),
            50.00,
            'REF123456'
        ]);
        
        if ($result) {
            $user_id = $pdo->lastInsertId();
            echo "<p style='color: green; font-weight: bold;'>✓ Test user inserted successfully with ID: " . $user_id . "</p>";
            
            // Test 3: Query the user to verify referral code
            $stmt = $pdo->prepare("SELECT referral_code FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $user['referral_code'] === 'REF123456') {
                echo "<p style='color: green; font-weight: bold;'>✓ Referral code stored correctly: " . $user['referral_code'] . "</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>✗ Referral code not stored correctly.</p>";
            }
            
            // Clean up: Delete test user
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            echo "<p style='color: blue; font-weight: bold;'>✓ Test user cleaned up.</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>✗ Failed to insert test user.</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error during testing: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='register.php'>Try Registration</a> | <a href='index.php'>Back to Homepage</a></p>";
?>