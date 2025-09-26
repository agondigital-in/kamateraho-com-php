<?php
include '../config/db.php';
include 'auth.php'; // Admin authentication check

// Test function to check wallet balance before and after withdrawal
function testWithdrawal($pdo, $user_id, $withdraw_amount) {
    try {
        // Get current wallet balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $before_balance = $stmt->fetchColumn();
        
        echo "Before withdrawal: ₹" . number_format($before_balance, 2) . "\n";
        
        // Simulate withdrawal deduction
        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
        $stmt->execute([$withdraw_amount, $user_id]);
        
        // Get updated wallet balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $after_balance = $stmt->fetchColumn();
        
        echo "After withdrawal: ₹" . number_format($after_balance, 2) . "\n";
        echo "Expected: ₹" . number_format($before_balance - $withdraw_amount, 2) . "\n";
        
        if (abs($after_balance - ($before_balance - $withdraw_amount)) < 0.01) {
            echo "Test PASSED: Wallet balance updated correctly\n";
        } else {
            echo "Test FAILED: Wallet balance not updated correctly\n";
        }
    } catch(PDOException $e) {
        echo "Test ERROR: " . $e->getMessage() . "\n";
    }
}

// Run test if parameters are provided
if (isset($_GET['user_id']) && isset($_GET['amount'])) {
    testWithdrawal($pdo, (int)$_GET['user_id'], (float)$_GET['amount']);
} else {
    echo "Usage: test_withdraw.php?user_id=1&amount=100\n";
    echo "This will test deducting ₹100 from user ID 1's wallet.\n";
}
?>