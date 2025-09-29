<?php
// Include subadmin authentication instead of regular auth
include 'subadmin_auth.php'; // Sub-admin authentication check

// Check permissions for sub-admin
if ($isSubAdmin) {
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'pending_wallet_approvals'");
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
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'approve_wallet', 'Approved wallet entry ID: ' . $id . ' for amount: ₹' . $history['amount']]);
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            $message = "Wallet entry approved successfully!";
        }
    } else {
        // Reject
        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$id]);
        
        // Log activity for sub-admin
        if ($isSubAdmin) {
            try {
                $activityStmt = $pdo->prepare("SELECT * FROM wallet_history WHERE id = ?");
                $activityStmt->execute([$id]);
                $history = $activityStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($history) {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'reject_wallet', 'Rejected wallet entry ID: ' . $id . ' for amount: ₹' . $history['amount']]);
                }
            } catch (PDOException $e) {
                // Silently fail on activity logging
            }
        }
        
        $message = "Wallet entry rejected!";
    }
} catch(PDOException $e) {
    $error = "Error processing request: " . $e->getMessage();
}

// Redirect back to appropriate dashboard
if ($isAdmin) {
    $redirect_url = 'index.php';
} else {
    $redirect_url = 'subadmin_dashboard.php';
}

if (isset($message)) {
    $redirect_url .= '?message=' . urlencode($message);
}

header('Location: ' . $redirect_url);
exit;
?>