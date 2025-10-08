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
            if (strpos($request['upi_id'], 'purchase@') === 0) {
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
                
                $message = "Purchase/Application request rejected! The amount has been credited to the user's wallet.";
            } else {
                // For regular withdrawal requests, update wallet history status to rejected
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                $stmt->execute([$request['user_id'], $request['amount']]);
                
                // Refund the amount to user's wallet
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $stmt->execute([$request['amount'], $request['user_id']]);
                
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