<?php
// Debug script specifically for purchase request rejection issue
include 'config/db.php';
include 'admin/auth.php'; // Admin authentication

// Only allow access to admin users
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin/login.php');
    exit;
}

$message = '';
$error = '';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'debug') {
        try {
            // Get withdraw request record
            $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
            $stmt->execute([$id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($request) {
                $message .= "<h3>Withdraw Request Details:</h3>";
                $message .= "<pre>" . print_r($request, true) . "</pre>";
                
                // Check if this is a purchase request
                $is_purchase = (strpos($request['upi_id'], 'purchase@') === 0);
                $message .= "<p>Is Purchase Request: " . ($is_purchase ? 'Yes' : 'No') . "</p>";
                $message .= "<p>UPI ID: " . $request['upi_id'] . "</p>";
                $message .= "<p>UPI ID starts with 'purchase@': " . (strpos($request['upi_id'], 'purchase@') === 0 ? 'True' : 'False') . "</p>";
                
                // Check wallet history entries for this user and amount
                $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE user_id = ? AND amount = ? ORDER BY id DESC");
                $stmt->execute([$request['user_id'], $request['amount']]);
                $wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $message .= "<h3>Wallet History Entries:</h3>";
                if (count($wallet_history) > 0) {
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
                    $message .= "<p>No wallet history entries found for this user and amount.</p>";
                }
                
                // Check user's current wallet balance
                $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                $stmt->execute([$request['user_id']]);
                $wallet_balance = $stmt->fetchColumn();
                
                $message .= "<h3>User Wallet Balance:</h3>";
                $message .= "<p>₹" . number_format($wallet_balance, 2) . "</p>";
            } else {
                $error = "Withdraw request not found.";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    } else if ($action === 'test_reject') {
        try {
            // Get withdraw request record
            $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
            $stmt->execute([$id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($request) {
                $message .= "<h3>Processing Rejection for Request ID: " . $id . "</h3>";
                
                // Begin transaction
                $pdo->beginTransaction();
                
                // Update withdraw request status to rejected
                $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$id]);
                $message .= "<p>Updated withdraw request status to rejected</p>";
                
                // Check if this is a purchase request (identified by UPI ID starting with "purchase@")
                $is_purchase = (strpos($request['upi_id'], 'purchase@') === 0);
                $message .= "<p>Is Purchase Request: " . ($is_purchase ? 'Yes' : 'No') . "</p>";
                
                if ($is_purchase) {
                    $message .= "<p>Processing as Purchase Request</p>";
                    
                    // For purchase requests, we need to add the amount to user's wallet since it was never deducted
                    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                    $result = $stmt->execute([$request['amount'], $request['user_id']]);
                    $message .= "<p>User wallet balance update result: " . ($result ? "success" : "failed") . "</p>";
                    
                    // Update wallet history status to rejected with improved query
                    $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description LIKE 'Purchase/Application request submitted%'");
                    $stmt->execute([$request['user_id'], $request['amount']]);
                    
                    // Check if we updated any rows
                    $rows_affected = $stmt->rowCount();
                    $message .= "<p>Wallet history rows updated: " . $rows_affected . "</p>";
                    
                    // If no rows were updated, try exact match
                    if ($rows_affected == 0) {
                        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description = 'Purchase/Application request submitted'");
                        $stmt->execute([$request['user_id'], $request['amount']]);
                        $rows_affected = $stmt->rowCount();
                        $message .= "<p>Wallet history rows updated (exact match): " . $rows_affected . "</p>";
                    }
                    
                    // Add entry to wallet history for the refund
                    $description = "Purchase/Application request rejected - amount credited";
                    $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                    $stmt->execute([$request['user_id'], $request['amount'], $description]);
                    $message .= "<p>Added refund entry to wallet history</p>";
                    
                    $message .= "<p><strong>Purchase request rejected successfully!</strong></p>";
                } else {
                    $message .= "<p>Processing as Regular Withdrawal Request</p>";
                    
                    // For regular withdrawal requests, update wallet history status to rejected
                    // Try the exact match first
                    $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                    $stmt->execute([$request['user_id'], $request['amount']]);
                    
                    // Check if we updated any rows
                    $rows_affected = $stmt->rowCount();
                    $message .= "<p>Wallet history rows updated (exact match): " . $rows_affected . "</p>";
                    
                    // If no rows were updated, try a more flexible match
                    if ($rows_affected == 0) {
                        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description LIKE 'Withdrawal request submitted%'");
                        $stmt->execute([$request['user_id'], $request['amount']]);
                        $rows_affected = $stmt->rowCount();
                        $message .= "<p>Wallet history rows updated (LIKE match): " . $rows_affected . "</p>";
                    }
                    
                    // Refund the amount to user's wallet
                    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                    $result = $stmt->execute([$request['amount'], $request['user_id']]);
                    $message .= "<p>User wallet balance update result: " . ($result ? "success" : "failed") . "</p>";
                    
                    // Add entry to wallet history for refund
                    $description = "Withdrawal request rejected - amount refunded";
                    $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                    $stmt->execute([$request['user_id'], $request['amount'], $description]);
                    $message .= "<p>Added refund entry to wallet history</p>";
                    
                    $message .= "<p><strong>Withdrawal request rejected successfully!</strong></p>";
                }
                
                // Commit transaction
                $pdo->commit();
                
                // Check user's new wallet balance
                $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                $stmt->execute([$request['user_id']]);
                $new_balance = $stmt->fetchColumn();
                
                $message .= "<p>Amount processed: ₹" . number_format($request['amount'], 2) . "</p>";
                $message .= "<p>User's new wallet balance: ₹" . number_format($new_balance, 2) . "</p>";
            } else {
                $error = "Withdraw request not found.";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Purchase Request Rejection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="mb-4">Debug Purchase Request Rejection</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="id" class="form-label">Withdraw Request ID:</label>
                            <input type="number" class="form-control" id="id" name="id" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" name="action" value="debug" class="btn btn-primary me-2">
                                Debug Request
                            </button>
                            <button type="submit" name="action" value="test_reject" class="btn btn-danger">
                                Test Rejection
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-4">
                    <h3>How to Use:</h3>
                    <ol>
                        <li>Find a pending purchase request in the admin panel</li>
                        <li>Copy the request ID from the URL or database</li>
                        <li>Enter the ID above and click "Debug Request" to see details</li>
                        <li>Click "Test Rejection" to simulate the fixed rejection process</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>