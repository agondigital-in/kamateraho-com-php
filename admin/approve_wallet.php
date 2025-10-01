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
$page_title = "Approve Wallet Entry";
include '../config/db.php';
include 'approve_auth.php'; // This includes auth check without output
include 'includes/admin_layout.php'; // This includes the full layout

if ($id <= 0 || !in_array($action, ['approve', 'reject'])) {
    ob_clean();
    header('Location: index.php');
    exit;
}

// Get wallet history details
try {
    $stmt = $pdo->prepare("SELECT wh.*, u.name, u.email, u.wallet_balance FROM wallet_history wh 
                           JOIN users u ON wh.user_id = u.id 
                           WHERE wh.id = ?");
    $stmt->execute([$id]);
    $history = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$history) {
        $error = "Wallet entry not found.";
    }
} catch(PDOException $e) {
    $error = "Error fetching entry details: " . $e->getMessage();
}

// Flush the output buffer
ob_end_flush();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Wallet Entry Details</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif (isset($_GET['message'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($history)): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Entry Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>User:</th>
                                        <td><?php echo htmlspecialchars($history['name']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?php echo htmlspecialchars($history['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td class="fw-bold">
                                            <?php if ($history['type'] == 'credit'): ?>
                                                <span class="text-success">+₹<?php echo number_format($history['amount'], 2); ?></span>
                                            <?php else: ?>
                                                <span class="text-danger">-₹<?php echo number_format($history['amount'], 2); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Current Wallet Balance:</th>
                                        <td>₹<?php echo number_format($history['wallet_balance'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>
                                            <?php if ($history['type'] == 'credit'): ?>
                                                <span class="badge bg-success">Credit</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Debit</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Description:</th>
                                        <td><?php echo htmlspecialchars($history['description']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <?php if ($history['status'] == 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php elseif ($history['status'] == 'approved'): ?>
                                                <span class="badge bg-success">Approved</span>
                                            <?php elseif ($history['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Requested On:</th>
                                        <td><?php echo date('d M Y, h:i A', strtotime($history['created_at'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <?php if ($history['status'] == 'pending'): ?>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="?id=<?php echo $id; ?>&action=approve" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Approve Entry
                                </a>
                                <a href="?id=<?php echo $id; ?>&action=reject" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Reject Entry
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="index.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($history['status'] == 'pending'): ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Approval Confirmation</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="bi bi-info-circle"></i> Action Required</h6>
                                <p>Please confirm your action for this wallet entry:</p>
                                <ul>
                                    <li><strong>Approve:</strong> <?php echo ($history['type'] == 'credit') ? 'Add amount to user wallet' : 'Deduct amount from user wallet'; ?></li>
                                    <li><strong>Reject:</strong> Cancel this transaction</li>
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