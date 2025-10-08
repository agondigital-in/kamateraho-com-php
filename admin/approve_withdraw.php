<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Handle redirects first, before any output
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process approval/rejection if action is specified
if (isset($action) && in_array($action, ['approve', 'reject']) && $id > 0) {
    // Include authentication first
    include 'approve_auth.php';
    include '../config/db.php';
    
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
                        
                        // Update wallet history status to approved
                        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE user_id = ? AND amount = ? AND type = 'credit' AND status = 'pending' AND description = 'Purchase/Application request submitted'");
                        $stmt->execute([$request['user_id'], $request['amount']]);
                        
                        // Add additional entry to wallet history for the approval
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
                    // This is a regular withdrawal that was already processed
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
    
    // Clean the output buffer and redirect with message
    ob_clean();
    if (isset($error)) {
        header('Location: ?id=' . $id . '&error=' . urlencode($error));
    } else {
        header('Location: ?id=' . $id . '&message=' . urlencode($message));
    }
    exit;
}

// If no action or redirect, show the page
$page_title = "Approve Withdraw Request";
include '../config/db.php';
include 'approve_auth.php'; // This includes auth check without output
include 'includes/admin_layout.php'; // This includes the full layout

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($id <= 0 || !in_array($action, ['approve', 'reject'])) {
    ob_clean();
    header('Location: index.php');
    exit;
}

// Get withdraw request details
try {
    $stmt = $pdo->prepare("SELECT wr.*, u.name, u.email, u.id as user_id, u.wallet_balance FROM withdraw_requests wr 
                           JOIN users u ON wr.user_id = u.id 
                           WHERE wr.id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        $error = "Withdraw request not found.";
    }
} catch(PDOException $e) {
    $error = "Error fetching request details: " . $e->getMessage();
}

// Flush the output buffer
ob_end_flush();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Withdraw Request Details</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif (isset($_GET['message'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($request)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Request Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>User ID:</th>
                                        <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td class="fw-bold">₹<?php echo number_format($request['amount'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Current Wallet Balance:</th>
                                        <td>₹<?php echo number_format($request['wallet_balance'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <?php if ($request['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($request['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($request['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Request Type:</th>
                                        <td>
                                            <?php if (strpos($request['upi_id'], 'purchase@') === 0): ?>
                                                <span class="badge bg-success">Purchase Request</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Withdrawal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>UPI ID:</th>
                                        <td><?php echo htmlspecialchars($request['upi_id']); ?></td>
                                    </tr>
                                    <?php if (!empty($request['offer_title'])): ?>
                                        <tr>
                                            <th>Offer Title:</th>
                                            <td><?php echo htmlspecialchars($request['offer_title']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Offer Description:</th>
                                            <td>
                                                <div class="text-truncate-slider" style="max-width: 300px; overflow-x: auto; overflow-y: hidden; white-space: nowrap;">
                                                    <div class="text-slider-content">
                                                        <?php echo htmlspecialchars($request['offer_description']); ?>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Requested On:</th>
                                        <td><?php echo date('d M Y, h:i A', strtotime($request['created_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <?php if ($request['status'] == 'pending'): ?>
                                <a href="?id=<?php echo $id; ?>&action=approve" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Approve Request
                                </a>
                                <a href="?id=<?php echo $id; ?>&action=reject" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Reject Request
                                </a>
                            <?php else: ?>
                                <a href="index.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php if ($request['status'] == 'pending'): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Approval Confirmation</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle"></i> Action Required</h6>
                                <p>Please confirm your action for this withdraw request:</p>
                                <ul>
                                    <li><strong>Approve:</strong> <?php echo (strpos($request['upi_id'], 'purchase@') === 0) ? 'Add amount to user wallet' : 'Confirm withdrawal'; ?></li>
                                    <li><strong>Reject:</strong> Refund amount to user wallet</li>
                                </ul>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-2">
                                <a href="?id=<?php echo $id; ?>&action=approve" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle"></i> Confirm Approval
                                </a>
                                <a href="?id=<?php echo $id; ?>&action=reject" class="btn btn-danger btn-lg">
                                    <i class="bi bi-x-circle"></i> Confirm Rejection
                                </a>
                                <a href="index.php" class="btn btn-secondary btn-lg">
                                    <i class="bi bi-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>