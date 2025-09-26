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
        // Get wallet history record
        $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE id = ?");
        $stmt->execute([$id]);
        $history = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($history) {
            // Update wallet history status
            $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE id = ?");
            $stmt->execute([$id]);
            
            // Update user wallet balance
            if ($history['type'] === 'credit') {
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
            } else {
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
            }
            $stmt->execute([$history['amount'], $history['user_id']]);
            
            $message = "Wallet entry approved successfully!";
        }
    } else {
        // Reject
        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Wallet entry rejected!";
    }
} catch(PDOException $e) {
    $error = "Error processing request: " . $e->getMessage();
}

// Redirect back to admin dashboard
header('Location: index.php' . (isset($message) ? '?message=' . urlencode($message) : ''));
exit;
?>