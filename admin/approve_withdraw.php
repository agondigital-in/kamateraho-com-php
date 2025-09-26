<?php
include '../config/db.php';
include 'auth.php'; // Admin authentication check

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get parameters
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id <= 0 || !in_array($action, ['approve', 'reject'])) {
    header('Location: index.php');
    exit;
}

try {
    if ($action === 'approve') {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get withdraw request record
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Log the request details
            error_log("Processing withdrawal approval for request ID: " . $id);
            error_log("User ID: " . $request['user_id'] . ", Amount: " . $request['amount']);
            
            // Update withdraw request status
            $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Check if this is a purchase (identified by UPI ID starting with "purchase@")
            if (strpos($request['upi_id'], 'purchase@') === 0) {
                // This is a purchase/application request, so add amount to user's wallet
                // Get user's current wallet balance before addition
                $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                $stmt->execute([$request['user_id']]);
                $current_balance = $stmt->fetchColumn();
                
                error_log("User's current wallet balance: " . $current_balance);
                
                // Add amount to user's wallet balance
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $stmt->execute([$request['amount'], $request['user_id']]);
                
                // Check if the update was successful
                if ($stmt->rowCount() > 0) {
                    // Get user's new wallet balance after addition
                    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                    $stmt->execute([$request['user_id']]);
                    $new_balance = $stmt->fetchColumn();
                    
                    // Add entry to wallet history
                    $description = "Purchase/Application approved: " . $request['offer_title'];
                    $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                    $stmt->execute([$request['user_id'], $request['amount'], $description]);
                    
                    error_log("User's new wallet balance: " . $new_balance);
                    error_log("Expected balance: " . ($current_balance + $request['amount']));
                    
                    // Commit transaction
                    $pdo->commit();
                    $message = "Purchase/Application request approved successfully! Amount (₹" . number_format($request['amount'], 2) . ") added to user's wallet. New balance: ₹" . number_format($new_balance, 2);
                } else {
                    // Rollback transaction if wallet update failed
                    $pdo->rollback();
                    $error = "Failed to add amount to user's wallet.";
                }
            } else {
                // This is a regular withdrawal, so deduct amount from user's wallet
                // Get user's current wallet balance before deduction
                $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                $stmt->execute([$request['user_id']]);
                $current_balance = $stmt->fetchColumn();
                
                error_log("User's current wallet balance: " . $current_balance);
                
                // Deduct amount from user's wallet balance
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                $stmt->execute([$request['amount'], $request['user_id']]);
                
                // Check if the update was successful
                if ($stmt->rowCount() > 0) {
                    // Get user's new wallet balance after deduction
                    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                    $stmt->execute([$request['user_id']]);
                    $new_balance = $stmt->fetchColumn();
                    
                    error_log("User's new wallet balance: " . $new_balance);
                    error_log("Expected balance: " . ($current_balance - $request['amount']));
                    
                    // Commit transaction
                    $pdo->commit();
                    $message = "Withdraw request approved successfully! Amount (₹" . number_format($request['amount'], 2) . ") deducted from user's wallet. New balance: ₹" . number_format($new_balance, 2);
                } else {
                    // Rollback transaction if wallet update failed
                    $pdo->rollback();
                    $error = "Failed to deduct amount from user's wallet.";
                }
            }
        } else {
            $pdo->rollback();
            $error = "Withdraw request not found.";
        }
    } else {
        // Reject - only update the status, no wallet changes needed
        $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Request rejected!";
    }
} catch(PDOException $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log("Database error in approve_withdraw.php: " . $e->getMessage());
    $error = "Error processing request: " . $e->getMessage();
} catch(Exception $e) {
    // Rollback transaction on general error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log("General error in approve_withdraw.php: " . $e->getMessage());
    $error = "Error processing request: " . $e->getMessage();
}

// Redirect back to admin dashboard
$redirect_url = 'index.php';
if (isset($error)) {
    $redirect_url .= '?error=' . urlencode($error);
} elseif (isset($message)) {
    $redirect_url .= '?message=' . urlencode($message);
}

error_log("Redirecting to: " . $redirect_url);
header('Location: ' . $redirect_url);
exit;
?>