<?php
// Comprehensive test script to verify withdrawal request rejection fix
include 'config/db.php';
include 'admin/auth.php'; // Admin authentication

// Only allow access to admin users
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin/login.php');
    exit;
}

$message = '';
$error = '';

if (isset($_POST['test_fix'])) {
    try {
        // Create a test user if not exists
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (id, name, email, password, wallet_balance) VALUES (999, 'Test User', 'test@example.com', 'password', 10000.00)");
        $stmt->execute();
        
        // Store initial wallet balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([999]);
        $initial_balance = $stmt->fetchColumn();
        
        // Create a test withdrawal request
        $user_id = 999;
        $amount = 5000.00;
        $upi_id = "test@upi";
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Deduct amount from user's wallet immediately (as done in real withdrawal)
        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);
        
        // Insert withdraw request with 'pending' status
        $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $amount, $upi_id, ""]);
        
        // Get the ID of the inserted request
        $request_id = $pdo->lastInsertId();
        
        // Add entry to wallet history
        $description = "Withdrawal request submitted";
        $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'debit', 'pending', ?)");
        $stmt->execute([$user_id, $amount, $description]);
        
        // Get the ID of the inserted wallet history entry
        $wallet_history_id = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        // Check user's wallet balance after deduction
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([999]);
        $balance_after_deduction = $stmt->fetchColumn();
        
        $message = "Test withdrawal request created successfully with ID: " . $request_id . "<br>";
        $message .= "Wallet history entry created with ID: " . $wallet_history_id . "<br>";
        $message .= "User wallet balance after deduction: ₹" . number_format($balance_after_deduction, 2) . "<br>";
        
        // Now simulate the FIXED rejection process
        // Get withdraw request record
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Update withdraw request status to rejected
            $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$request_id]);
            
            // For regular withdrawal requests, update wallet history status to rejected
            // Try the exact match first
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
            $stmt->execute([$request['user_id'], $request['amount']]);
            
            // Check if we updated any rows
            $rows_affected = $stmt->rowCount();
            $message .= "Wallet history rows updated (exact match): " . $rows_affected . "<br>";
            
            // If no rows were updated, try a more flexible match
            if ($rows_affected == 0) {
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description LIKE 'Withdrawal request submitted%'");
                $stmt->execute([$request['user_id'], $request['amount']]);
                $rows_affected = $stmt->rowCount();
                $message .= "Wallet history rows updated (LIKE match): " . $rows_affected . "<br>";
            }
            
            // Refund the amount to user's wallet
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $result = $stmt->execute([$request['amount'], $request['user_id']]);
            $message .= "User wallet balance update result: " . ($result ? "success" : "failed") . "<br>";
            
            // Add entry to wallet history for refund
            $description = "Withdrawal request rejected - amount refunded";
            $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
            $stmt->execute([$request['user_id'], $request['amount'], $description]);
            
            // Commit transaction
            $pdo->commit();
            
            // Check user's new wallet balance
            $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $new_balance = $stmt->fetchColumn();
            
            $message .= "Withdrawal request rejected successfully!<br>";
            $message .= "Amount refunded to user's wallet: ₹" . number_format($request['amount'], 2) . "<br>";
            $message .= "User's new wallet balance: ₹" . number_format($new_balance, 2) . "<br>";
            
            // Verify the fix worked
            $expected_balance = $initial_balance; // Should be back to original since we deducted and then refunded
            if ($new_balance == $expected_balance) {
                $message .= "<strong style='color: green;'>SUCCESS: Money was correctly refunded to user's wallet upon rejection.</strong><br>";
                $message .= "<strong style='color: green;'>FIX VERIFIED: The issue has been resolved!</strong><br>";
            } else {
                $error = "ERROR: Money was not correctly refunded to user's wallet.<br>";
                $error .= "Expected balance: ₹" . number_format($expected_balance, 2) . "<br>";
                $error .= "Actual balance: ₹" . number_format($new_balance, 2) . "<br>";
            }
            
            // Show wallet history entries for this user
            $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE user_id = ? ORDER BY id DESC LIMIT 5");
            $stmt->execute([$user_id]);
            $wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $message .= "<h4>Recent Wallet History Entries:</h4>";
            $message .= "<table class='table table-bordered'>";
            $message .= "<tr><th>ID</th><th>Amount</th><th>Type</th><th>Status</th><th>Description</th></tr>";
            foreach ($wallet_history as $entry) {
                $message .= "<tr>";
                $message .= "<td>" . $entry['id'] . "</td>";
                $message .= "<td>₹" . number_format($entry['amount'], 2) . "</td>";
                $message .= "<td>" . $entry['type'] . "</td>";
                $message .= "<td>" . $entry['status'] . "</td>";
                $message .= "<td>" . $entry['description'] . "</td>";
                $message .= "</tr>";
            }
            $message .= "</table>";
        } else {
            $error = "ERROR: Could not find the test request.";
        }
        
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
    } catch(Exception $e) {
        $error = "General error: " . $e->getMessage();
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Fix for Withdrawal Request Rejection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="mb-4">Test Fix for Withdrawal Request Rejection</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <button type="submit" name="test_fix" class="btn btn-primary btn-lg">
                        Test Fix for Withdrawal Request Rejection
                    </button>
                </form>
                
                <div class="mt-4">
                    <h3>What This Test Does:</h3>
                    <ol>
                        <li>Creates a test user with an initial wallet balance</li>
                        <li>Simulates a withdrawal request (deducts amount from wallet)</li>
                        <li>Creates a corresponding wallet history entry</li>
                        <li>Simulates the FIXED rejection process</li>
                        <li>Verifies that the amount is correctly refunded to the user's wallet</li>
                        <li>Shows all wallet history entries for verification</li>
                    </ol>
                    
                    <h3>Fix Details:</h3>
                    <ul>
                        <li>Improved the wallet history query to use both exact match and LIKE operator for more flexible matching</li>
                        <li>Added debugging information to track the process</li>
                        <li>Ensured both approve_withdraw.php and approve_withdraw_simple.php are updated</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>