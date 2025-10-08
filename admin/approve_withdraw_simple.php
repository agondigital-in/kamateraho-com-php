<?php
include '../config/db.php';
include 'auth.php'; // Admin authentication check

// Get parameters
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id <= 0 || !in_array($action, ['approve', 'reject'])) {
    header('Location: index.php');
    exit;
}

try {
    if ($action === 'approve') {
        // Get withdraw request record
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Update withdraw request status
            $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Update wallet history status to approved
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
            $stmt->execute([$request['user_id'], $request['amount']]);
            
            // Check if this is a purchase (identified by UPI ID starting with "purchase@")
            if (strpos($request['upi_id'], 'purchase@') === 0) {
                // This is a purchase/application request, so add amount to user's wallet
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $stmt->execute([$request['amount'], $request['user_id']]);
                
                // Update wallet history status to approved
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description = 'Purchase/Application request submitted'");
                $stmt->execute([$request['user_id'], $request['amount']]);
                
                // Add entry to wallet history
                $description = "Purchase/Application approved: " . $request['offer_title'];
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                $stmt->execute([$request['user_id'], $request['amount'], $description]);
                
                $message = "Purchase/Application request approved successfully! Amount added to user's wallet.";
            } else {
                // Regular withdrawal already processed
                $message = "Withdraw request approved successfully! The amount was already deducted from the user's wallet.";
            }
        } else {
            $error = "Withdraw request not found.";
        }
    } else {
        // Reject - refund the money to user's wallet
        // Get withdraw request record
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Update withdraw request status to rejected
            $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Check if this is a purchase request (identified by UPI ID starting with "purchase@")
            $is_purchase_request = (strpos($request['upi_id'], 'purchase@') === 0);
            error_log("Processing rejection for request ID: " . $id . ", Is purchase request: " . ($is_purchase_request ? 'Yes' : 'No') . ", UPI ID: " . $request['upi_id']);
            
            if ($is_purchase_request) {
                // For purchase requests, we need to add the amount to user's wallet since it was never deducted
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $result = $stmt->execute([$request['amount'], $request['user_id']]);
                error_log("User wallet balance update for purchase request: " . ($result ? "success" : "failed"));
                
                // Update wallet history status to rejected with improved query
                // Try LIKE match first
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description LIKE 'Purchase/Application request submitted%'");
                $stmt->execute([$request['user_id'], $request['amount']]);
                
                // Check if we updated any rows
                $rows_affected = $stmt->rowCount();
                error_log("Wallet history rows updated (LIKE match): " . $rows_affected);
                
                // If no rows were updated, try exact match
                if ($rows_affected == 0) {
                    $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description = 'Purchase/Application request submitted'");
                    $stmt->execute([$request['user_id'], $request['amount']]);
                    $rows_affected = $stmt->rowCount();
                    error_log("Wallet history rows updated (exact match): " . $rows_affected);
                }
                
                // Add entry to wallet history for the refund
                $description = "Purchase/Application request rejected - amount credited";
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                $stmt->execute([$request['user_id'], $request['amount'], $description]);
                error_log("Added refund entry to wallet history for purchase request");
                
                $message = "Purchase/Application request rejected! The amount has been credited to the user's wallet.";
            } else {
                // For regular withdrawal requests, update wallet history status to rejected
                // Try the exact match first
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                $stmt->execute([$request['user_id'], $request['amount']]);
                
                // Check if we updated any rows
                $rows_affected = $stmt->rowCount();
                error_log("Wallet history exact match update rows affected: " . $rows_affected);
                
                // If no rows were updated, try a more flexible match
                if ($rows_affected == 0) {
                    error_log("Trying flexible match for wallet history update");
                    $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description LIKE 'Withdrawal request submitted%'");
                    $stmt->execute([$request['user_id'], $request['amount']]);
                    $rows_affected = $stmt->rowCount();
                    error_log("Wallet history flexible match update rows affected: " . $rows_affected);
                }
                
                // Refund the amount to user's wallet
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $result = $stmt->execute([$request['amount'], $request['user_id']]);
                error_log("User wallet balance update result: " . ($result ? "success" : "failed"));
                
                // Add entry to wallet history for refund
                $description = "Withdrawal request rejected - amount refunded";
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                $stmt->execute([$request['user_id'], $request['amount'], $description]);
                
                $message = "Withdraw request rejected! The amount has been refunded to the user's wallet.";
            }
        } else {
            $error = "Withdraw request not found.";
        }
    }
} catch(PDOException $e) {
    $error = "Error processing request: " . $e->getMessage();
}

// Redirect back to admin dashboard
header('Location: index.php' . (isset($error) ? '?error=' . urlencode($error) : (isset($message) ? '?message=' . urlencode($message) : '')));
exit;
?>