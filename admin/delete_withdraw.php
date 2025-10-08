<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Handle redirects first, before any output
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Process deletion if ID is specified
if ($id > 0) {
    // Include authentication first
    include 'approve_auth.php';
    include '../config/db.php';
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get withdraw request record
        $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
        $stmt->execute([$id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request) {
            // Delete the withdraw request
            $stmt = $pdo->prepare("DELETE FROM withdraw_requests WHERE id = ?");
            $stmt->execute([$id]);
            
            // Also delete associated wallet history entry
            $stmt = $pdo->prepare("DELETE FROM wallet_history WHERE user_id = ? AND amount = ? AND type = 'debit' AND description = 'Withdrawal request submitted'");
            $stmt->execute([$request['user_id'], $request['amount']]);
            
            // Commit transaction
            $pdo->commit();
            
            $message = "Request deleted successfully!";
        } else {
            $pdo->rollback();
            $error = "Withdraw request not found.";
        }
    } catch(PDOException $e) {
        // Rollback transaction on error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("Database error in delete_withdraw.php: " . $e->getMessage());
        $error = "Error deleting request: " . $e->getMessage();
    } catch(Exception $e) {
        // Rollback transaction on general error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("General error in delete_withdraw.php: " . $e->getMessage());
        $error = "Error deleting request: " . $e->getMessage();
    }
    
    // Clean the output buffer and redirect with message
    ob_clean();
    if (isset($error)) {
        header('Location: pending_withdraw_requests.php?error=' . urlencode($error));
    } else {
        header('Location: pending_withdraw_requests.php?message=' . urlencode($message));
    }
    exit;
}

// If no ID, redirect back
ob_clean();
header('Location: pending_withdraw_requests.php');
exit;
?>