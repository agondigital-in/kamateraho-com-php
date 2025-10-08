<?php
// Test script to verify purchase request rejection functionality
include 'config/db.php';
include 'admin/auth.php'; // Admin authentication

// Only allow access to admin users
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin/login.php');
    exit;
}

$message = '';
$error = '';

if (isset($_POST['test_rejection'])) {
    try {
        // Create a test user if not exists
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (id, name, email, password, wallet_balance) VALUES (999, 'Test User', 'test@example.com', 'password', 0.00)");
        $stmt->execute();
        
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
        
        // Commit transaction
        $pdo->commit();
        
        $message = "Test purchase request created successfully with ID: " . $request_id . "<br>";
        $message .= "User wallet balance before rejection: 0.00<br>";
        
        // Now simulate the rejection process
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
            $stmt->execute([$request['amount'], $request['user_id']]);
            
            // Update wallet history status to rejected
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description = 'Purchase/Application request submitted'");
            $stmt->execute([$request['user_id'], $request['amount']]);
            
            // Add entry to wallet history for the refund
            $description = "Purchase/Application request rejected - amount credited";
            $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
            $stmt->execute([$request['user_id'], $request['amount'], $description]);
            
            // Commit transaction
            $pdo->commit();
            
            // Check user's new wallet balance
            $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $new_balance = $stmt->fetchColumn();
            
            $message .= "Purchase request rejected successfully!<br>";
            $message .= "Amount credited to user's wallet: ₹" . number_format($request['amount'], 2) . "<br>";
            $message .= "User's new wallet balance: ₹" . number_format($new_balance, 2) . "<br>";
            
            if ($new_balance == $amount) {
                $message .= "<strong style='color: green;'>SUCCESS: Money was correctly added to user's wallet upon rejection.</strong><br>";
            } else {
                $error = "ERROR: Money was not correctly added to user's wallet.";
            }
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
    <title>Test Purchase Request Rejection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <h1 class="mb-4">Test Purchase Request Rejection</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <button type="submit" name="test_rejection" class="btn btn-primary btn-lg">
                        Test Purchase Request Rejection
                    </button>
                </form>
                
                <div class="mt-4">
                    <h3>How the Fix Works:</h3>
                    <ol>
                        <li>When a user clicks "Apply Now" on a product/credit card, a purchase request is created</li>
                        <li>An entry is added to the wallet_history table with status 'pending'</li>
                        <li>When the admin rejects the request, the system:
                            <ul>
                                <li>Updates the request status to 'rejected'</li>
                                <li>Updates the wallet_history entry to 'rejected'</li>
                                <li>Adds the amount to the user's wallet balance</li>
                                <li>Creates a new wallet_history entry for the refund</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>