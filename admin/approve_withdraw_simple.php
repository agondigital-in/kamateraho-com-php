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
            
            // Deduct amount from user's wallet balance
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
            $stmt->execute([$request['amount'], $request['user_id']]);
            
            // Check if the update was successful
            if ($stmt->rowCount() > 0) {
                $message = "Withdraw request approved successfully! Amount deducted from user's wallet.";
            } else {
                $error = "Failed to deduct amount from user's wallet.";
            }
        } else {
            $error = "Withdraw request not found.";
        }
    } else {
        // Reject
        $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Withdraw request rejected!";
    }
} catch(PDOException $e) {
    $error = "Error processing request: " . $e->getMessage();
}

// Redirect back to admin dashboard
header('Location: index.php' . (isset($error) ? '?error=' . urlencode($error) : (isset($message) ? '?message=' . urlencode($message) : '')));
exit;
?>