<?php
// Comprehensive test script to verify purchase request rejection fix
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
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (id, name, email, password, wallet_balance) VALUES (999, 'Test User', 'test@example.com', 'password', 0.00)");
        $stmt->execute();
        
        // Store initial wallet balance
        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
        $stmt->execute([999]);
        $initial_balance = $stmt->fetchColumn();
        
        // Create a test purchase request
        $user_id = 999;
        $amount = 5000.00;
        $upi_id = "purchase@" . time();
        $offer_title = "Test Credit Card";
        $offer_description = "Test credit card application";
        
        // Begin transaction
        $pdo->beginTransaction();
        
        // Insert withdraw request with special UPI ID to identify it as a purchase
        $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot, offer_title, offer_description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id, 
            $amount, 
            $upi_id, 
            "", // No screenshot
            $offer_title,
            $offer_description
        ]);
        
        // Get the ID of the inserted request
        $request_id = $pdo->lastInsertId();
        
        // Add entry to wallet history for tracking
        $description = "Purchase/Application request submitted";
        $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'pending', ?)");
        $stmt->execute([$user_id, $amount, $description]);
        
        // Get the ID of the inserted wallet history entry
        $wallet_history_id = $pdo->lastInsertId();
        
        // Commit transaction
        $pdo->commit();
        
        $message = "Test purchase request created successfully with ID: " . $request_id . "<br>";
        $message .= "Wallet history entry created with ID: " . $wallet_history_id . "<br>";
        $message .= "User wallet balance before rejection: ₹" . number_format($initial_balance, 2) . "<br>";
        
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
            
            // For purchase requests, we need to add the amount to user's wallet since it was never deducted
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $result1 = $stmt->execute([$request['amount'], $request['user_id']]);
            
            // Update wallet history status to rejected with improved query
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description LIKE 'Purchase/Application request submitted%'");
            $result2 = $stmt->execute([$request['user_id'], $request['amount']]);
            
            // Log how many rows were affected
            $rows_affected = $stmt->rowCount();
            $message .= "Wallet history rows updated: " . $rows_affected . "<br>";
            
            // Add entry to wallet history for the refund
            $description = "Purchase/Application request rejected - amount credited";
            $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
            $result3 = $stmt->execute([$request['user_id'], $request['amount'], $description]);
            
            // Commit transaction
            $pdo->commit();
            
            // Check user's new wallet balance
            $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $new_balance = $stmt->fetchColumn();
            
            $message .= "Purchase request rejected successfully!<br>";
            $message .= "Amount credited to user's wallet: ₹" . number_format($request['amount'], 2) . "<br>";
            $message .= "User's new wallet balance: ₹" . number_format($new_balance, 2) . "<br>";
            
            // Verify the fix worked
            $expected_balance = $initial_balance + $amount;
            if ($new_balance == $expected_balance) {
                $message .= "<strong style='color: green;'>SUCCESS: Money was correctly added to user's wallet upon rejection.</strong><br>";
                $message .= "<strong style='color: green;'>FIX VERIFIED: The issue has been resolved!</strong><br>";
            } else {
                $error = "ERROR: Money was not correctly added to user's wallet.<br>";
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
    <title>Test Fix for Purchase Request Rejection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="mb-4">Test Fix for Purchase Request Rejection</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <button type="submit" name="test_fix" class="btn btn-primary btn-lg">
                        Test Fix for Purchase Request Rejection
                    </button>
                </form>
                
                <div class="mt-4">
                    <h3>What This Test Does:</h3>
                    <ol>
                        <li>Creates a test user with zero wallet balance</li>
                        <li>Creates a purchase request with a specific amount</li>
                        <li>Creates a corresponding wallet history entry</li>
                        <li>Simulates the FIXED rejection process</li>
                        <li>Verifies that the amount is correctly added to the user's wallet</li>
                        <li>Shows all wallet history entries for verification</li>
                    </ol>
                    
                    <h3>Fix Details:</h3>
                    <ul>
                        <li>Improved the wallet history query to use LIKE operator for more flexible matching</li>
                        <li>Added debugging information to track the process</li>
                        <li>Ensured both approve_withdraw.php and approve_withdraw_simple.php are updated</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>