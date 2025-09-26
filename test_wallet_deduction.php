<?php
// Simple script to test wallet deduction functionality
include 'config/db.php';

echo "=== Wallet Deduction Test ===\n\n";

try {
    // Check database connection
    echo "1. Testing database connection...\n";
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "   ✓ Database connection successful\n\n";
    
    // Check if tables exist
    echo "2. Checking required tables...\n";
    $tables = ['users', 'withdraw_requests'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "   ✓ Table '$table' exists\n";
        } else {
            echo "   ✗ Table '$table' does not exist\n";
        }
    }
    echo "\n";
    
    // Check for pending withdrawal requests
    echo "3. Checking for pending withdrawal requests...\n";
    $stmt = $pdo->query("SELECT wr.*, u.name, u.wallet_balance 
                         FROM withdraw_requests wr 
                         JOIN users u ON wr.user_id = u.id 
                         WHERE wr.status = 'pending' 
                         LIMIT 1");
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($request) {
        echo "   ✓ Found pending withdrawal request:\n";
        echo "     - User: " . $request['name'] . "\n";
        echo "     - Current Wallet Balance: ₹" . number_format($request['wallet_balance'], 2) . "\n";
        echo "     - Withdrawal Amount: ₹" . number_format($request['amount'], 2) . "\n";
        echo "     - Request ID: " . $request['id'] . "\n\n";
        
        // Simulate the deduction process
        echo "4. Simulating wallet deduction...\n";
        $user_id = $request['user_id'];
        $amount = $request['amount'];
        
        // Get current balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $current_balance = $stmt->fetchColumn();
        echo "   Current balance: ₹" . number_format($current_balance, 2) . "\n";
        
        // Deduct amount
        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
        $result = $stmt->execute([$amount, $user_id]);
        
        if ($result) {
            echo "   ✓ Deduction query executed successfully\n";
            
            // Get new balance
            $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $new_balance = $stmt->fetchColumn();
            echo "   New balance: ₹" . number_format($new_balance, 2) . "\n";
            echo "   Expected balance: ₹" . number_format($current_balance - $amount, 2) . "\n";
            
            if (abs($new_balance - ($current_balance - $amount)) < 0.01) {
                echo "   ✓ Wallet balance updated correctly\n";
            } else {
                echo "   ✗ Wallet balance not updated correctly\n";
            }
        } else {
            echo "   ✗ Deduction query failed\n";
        }
    } else {
        echo "   No pending withdrawal requests found.\n\n";
        
        // Check if there are any users with wallet balances
        echo "5. Checking users with wallet balances...\n";
        $stmt = $pdo->query("SELECT id, name, wallet_balance FROM users WHERE wallet_balance > 0 LIMIT 3");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($users)) {
            echo "   Found " . count($users) . " users with wallet balances:\n";
            foreach ($users as $user) {
                echo "     - " . $user['name'] . ": ₹" . number_format($user['wallet_balance'], 2) . "\n";
            }
        } else {
            echo "   No users with wallet balances found.\n";
        }
    }
} catch(PDOException $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "   ✗ General error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>