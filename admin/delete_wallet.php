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
        
        // Get wallet history record
        $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE id = ?");
        $stmt->execute([$id]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($entry) {
            // Delete the wallet history entry
            $stmt = $pdo->prepare("DELETE FROM wallet_history WHERE id = ?");
            $stmt->execute([$id]);
            
            // Commit transaction
            $pdo->commit();
            
            $message = "Wallet entry deleted successfully!";
        } else {
            $pdo->rollback();
            $error = "Wallet entry not found.";
        }
    } catch(PDOException $e) {
        // Rollback transaction on error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("Database error in delete_wallet.php: " . $e->getMessage());
        $error = "Error deleting entry: " . $e->getMessage();
    } catch(Exception $e) {
        // Rollback transaction on general error
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        error_log("General error in delete_wallet.php: " . $e->getMessage());
        $error = "Error deleting entry: " . $e->getMessage();
    }
    
    // Clean the output buffer and redirect with message
    ob_clean();
    if (isset($error)) {
        header('Location: pending_wallet_approvals.php?error=' . urlencode($error));
    } else {
        header('Location: pending_wallet_approvals.php?message=' . urlencode($message));
    }
    exit;
}

// If no ID, redirect back
ob_clean();
header('Location: pending_wallet_approvals.php');
exit;
?>