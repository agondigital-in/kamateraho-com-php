<?php
// Include subadmin authentication instead of regular auth
include 'subadmin_auth.php'; // Sub-admin authentication check

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check permissions for sub-admin
if ($isSubAdmin) {
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'pending_withdraw_requests'");
        $stmt->execute([$subAdminId]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$permission || !$permission['allowed']) {
            // Redirect to sub-admin dashboard if no permission
            header("Location: subadmin_dashboard.php");
            exit;
        }
    } catch (PDOException $e) {
        // Redirect on error
        header("Location: subadmin_dashboard.php");
        exit;
    }
}

// Get parameters
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($id <= 0 || !in_array($action, ['approve', 'reject'])) {
    if ($isAdmin) {
        header('Location: index.php');
    } else {
        header('Location: subadmin_dashboard.php');
    }
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
            
            // Update withdraw request status to approved
            $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Update wallet history status to approved
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
            $stmt->execute([$request['user_id'], $request['amount']]);
            
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
                    
                    // Log activity for sub-admin
                    if ($isSubAdmin) {
                        try {
                            $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                            $activityStmt->execute([$subAdminId, 'approve_withdraw', 'Approved withdraw request ID: ' . $id . ' for amount: ₹' . $request['amount']]);
                        } catch (PDOException $e) {
                            // Silently fail on activity logging
                        }
                    }
                    
                    // Commit transaction
                    $pdo->commit();
                    $message = "Purchase/Application request approved successfully! Amount (₹" . number_format($request['amount'], 2) . ") added to user's wallet. New balance: ₹" . number_format($new_balance, 2);
                } else {
                    // Rollback transaction if wallet update failed
                    $pdo->rollback();
                    $error = "Failed to add amount to user's wallet.";
                }
            } else {
                // This is a regular withdrawal that was already processed
                // Log activity for sub-admin
                if ($isSubAdmin) {
                    try {
                        $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                        $activityStmt->execute([$subAdminId, 'approve_withdraw', 'Approved withdraw request ID: ' . $id . ' for amount: ₹' . $request['amount']]);
                    } catch (PDOException $e) {
                        // Silently fail on activity logging
                    }
                }
                
                // Commit transaction
                $pdo->commit();
                $message = "Withdraw request approved successfully! The amount was already deducted from the user's wallet when the request was submitted.";
            }
        } else {
            $pdo->rollback();
            $error = "Withdraw request not found.";
        }
    } else {
        // Reject - refund the money to user's wallet
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get withdraw request record
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Update withdraw request status to rejected
            $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Update wallet history status to rejected
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
            $stmt->execute([$request['user_id'], $request['amount']]);
            
            // Refund the amount to user's wallet
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            $stmt->execute([$request['amount'], $request['user_id']]);
            
            // Add entry to wallet history for refund
            $description = "Withdrawal request rejected - amount refunded";
            $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
            $stmt->execute([$request['user_id'], $request['amount'], $description]);
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
                    $activityStmt->execute([$id]);
                    $request = $activityStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($request) {
                        $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                        $activityStmt->execute([$subAdminId, 'reject_withdraw', 'Rejected withdraw request ID: ' . $id . ' for amount: ₹' . $request['amount']]);
                    }
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            // Commit transaction
            $pdo->commit();
            $message = "Request rejected! The amount has been refunded to the user's wallet.";
        } else {
            $pdo->rollback();
            $error = "Withdraw request not found.";
        }
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

// Redirect back to appropriate dashboard
if ($isAdmin) {
    $redirect_url = 'index.php';
} else {
    $redirect_url = 'subadmin_dashboard.php';
}

if (isset($error)) {
    $redirect_url .= '?error=' . urlencode($error);
} elseif (isset($message)) {
    $redirect_url .= '?message=' . urlencode($message);
}

error_log("Redirecting to: " . $redirect_url);
header('Location: ' . $redirect_url);
exit;
?>