<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

// Handle redirects first, before any output
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Check if we should process immediately or show form first
$should_process_now = true;
if (isset($action) && in_array($action, ['approve', 'reject']) && $id > 0) {
    // For GET requests on percentage-based offers, show form first
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Include database connection
        include '../config/db.php';
        
        // Get request details to check if it's a percentage-based offer
        $stmt = $pdo->prepare("SELECT wr.*, o.price_type FROM withdraw_requests wr LEFT JOIN offers o ON wr.offer_title = o.title WHERE wr.id = ?");
        $stmt->execute([$id]);
        $request_check = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($request_check && !empty($request_check['price_type']) && $request_check['price_type'] !== 'fixed') {
            // This is a percentage-based offer, don't process immediately
            $should_process_now = false;
        }
    }
}

// Process approval/rejection if action is specified and we should process now
if ($should_process_now && isset($action) && in_array($action, ['approve', 'reject']) && $id > 0) {
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
                
                // Check if admin has provided a transaction amount for percentage-based offers
                $final_amount = $request['amount'];
                if (isset($_POST['transaction_amount']) && is_numeric($_POST['transaction_amount']) && 
                    isset($_POST['custom_amount']) && is_numeric($_POST['custom_amount'])) {
                    // For percentage-based offers, use the calculated reward amount
                    $final_amount = (float)$_POST['custom_amount'];
                    $transaction_amount = (float)$_POST['transaction_amount'];
                } else if (isset($_POST['custom_amount']) && is_numeric($_POST['custom_amount'])) {
                    // For cases where only custom amount is provided
                    $final_amount = (float)$_POST['custom_amount'];
                    $transaction_amount = 0;
                }
                
                // Update withdraw request status to approved and potentially update amount
                if ($final_amount != $request['amount']) {
                    $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved', amount = ? WHERE id = ?");
                    $stmt->execute([$final_amount, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved' WHERE id = ?");
                    $stmt->execute([$id]);
                }
                
                // Update wallet history status to approved with potentially updated amount
                if ($final_amount != $request['amount']) {
                    // First update the existing wallet history entry
                    $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved', amount = ? WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                    $stmt->execute([$final_amount, $request['user_id'], $request['amount']]);
                } else {
                    $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                    $stmt->execute([$request['user_id'], $request['amount']]);
                }
                
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
                    $stmt->execute([$final_amount, $request['user_id']]);
                    
                    // Check if the update was successful
                    if ($stmt->rowCount() > 0) {
                        // Get user's new wallet balance after addition
                        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                        $stmt->execute([$request['user_id']]);
                        $new_balance = $stmt->fetchColumn();
                        
                        // Add entry to wallet history
                        $description = "Purchase/Application approved: " . $request['offer_title'];
                        if (isset($transaction_amount) && $transaction_amount > 0) {
                            $description .= " | Transaction: ₹" . number_format($transaction_amount, 2);
                        }
                        $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                        $stmt->execute([$request['user_id'], $final_amount, $description]);
                        
                        error_log("User's new wallet balance: " . $new_balance);
                        error_log("Expected balance: " . ($current_balance + $final_amount));
                        
                        // Commit transaction
                        $pdo->commit();
                        $message = "Purchase/Application request approved successfully! Amount (₹" . number_format($final_amount, 2) . ") added to user's wallet. New balance: ₹" . number_format($new_balance, 2);
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
                // Check if admin has provided a custom amount for percentage-based offers
                $final_amount = $request['amount'];
                if (isset($_POST['custom_amount']) && is_numeric($_POST['custom_amount'])) {
                    // For percentage-based offers, use the calculated reward amount if provided
                    $final_amount = (float)$_POST['custom_amount'];
                }
                
                // Update withdraw request status to rejected
                $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$id]);
                
                // Update wallet history status to rejected
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                $stmt->execute([$request['user_id'], $request['amount']]);
                
                // Refund the amount to user's wallet
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $stmt->execute([$final_amount, $request['user_id']]);
                
                // Add entry to wallet history for refund
                $description = "Withdrawal request rejected - amount refunded";
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                $stmt->execute([$request['user_id'], $final_amount, $description]);
                
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
    // Modified query to include offer information if available
    $stmt = $pdo->prepare("SELECT wr.*, u.name, u.email, u.id as user_id, u.wallet_balance FROM withdraw_requests wr 
                           JOIN users u ON wr.user_id = u.id 
                           WHERE wr.id = ?");
    $stmt->execute([$id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        $error = "Withdraw request not found.";
    } else {
        // Try to get price_type and price from offers table using LIKE for more flexible matching
        try {
            // First try with the exact title
            $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title = ?");
            $offerStmt->execute([$request['offer_title']]);
            $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($offer) {
                $request['price_type'] = $offer['price_type'];
                $request['offer_price'] = $offer['price'];
                error_log("Found price_type (exact match): " . $request['price_type'] . " and price: " . $request['offer_price'] . " for offer: " . $request['offer_title']);
            } else {
                // Try with LIKE for partial matches
                $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title LIKE ? OR title LIKE ?");
                $offerStmt->execute(['%' . $request['offer_title'] . '%', $request['offer_title'] . '%']);
                $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($offer) {
                    $request['price_type'] = $offer['price_type'];
                    $request['offer_price'] = $offer['price'];
                    error_log("Found price_type (LIKE match): " . $request['price_type'] . " and price: " . $request['offer_price'] . " for offer: " . $request['offer_title']);
                } else {
                    // Try to get any offer with similar title from offer_description in withdraw_requests
                    if (!empty($request['offer_description'])) {
                        // Extract offer percentage from description if available
                        if (preg_match('/Offer percentage: ([\d.]+)%/', $request['offer_description'], $matches)) {
                            $request['offer_price'] = (float)$matches[1];
                            // Determine price type from description
                            if (strpos($request['offer_description'], 'Upto Percent') !== false) {
                                $request['price_type'] = 'upto_percent';
                            } else if (strpos($request['offer_description'], 'Flat Percent') !== false) {
                                $request['price_type'] = 'flat_percent';
                            } else {
                                $request['price_type'] = 'fixed';
                            }
                            error_log("Extracted offer info from description: price_type=" . $request['price_type'] . ", price=" . $request['offer_price']);
                        } else {
                            $request['price_type'] = '';
                            $request['offer_price'] = 0;
                            error_log("No offer info found in description for: " . $request['offer_title']);
                        }
                    } else {
                        $request['price_type'] = '';
                        $request['offer_price'] = 0;
                        error_log("No offer found for title: " . $request['offer_title']);
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Error fetching offer price_type: " . $e->getMessage());
            $request['price_type'] = '';
            $request['offer_price'] = 0;
        }
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
                                        <th>Amount / Reward:</th>
                                        <td class="fw-bold">
                                            <?php 
                                            // For percentage-based offers, show that admin needs to determine amount
                                            if (!empty($request['price_type']) && $request['price_type'] !== 'fixed') {
                                                echo '<span class="text-warning">To be determined by admin</span>';
                                                if (!empty($request['offer_price'])) {
                                                    echo '<br><small class="text-muted">Offer percentage: ' . number_format($request['offer_price'], 2) . '%</small>';
                                                } else {
                                                    echo '<br><small class="text-muted">Percentage not available</small>';
                                                }
                                            } else {
                                                // For fixed price offers, show the amount
                                                echo '₹' . number_format($request['amount'], 2);
                                            }
                                            ?>
                                        </td>
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
                                    <?php if (!empty($request['price_type'])): ?>
                                        <tr>
                                            <th>Price Type:</th>
                                            <td>
                                                <?php 
                                                switch($request['price_type']) {
                                                    case 'fixed':
                                                        echo '<span class="badge bg-success">Fixed (₹)</span>';
                                                        break;
                                                    case 'flat_percent':
                                                        echo '<span class="badge bg-primary">Flat Percent (%)</span>';
                                                        break;
                                                    case 'upto_percent':
                                                        echo '<span class="badge bg-warning">Upto Percent (%)</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-secondary">Unknown</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
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
                                <?php if (!empty($request['price_type']) && $request['price_type'] !== 'fixed'): ?>
                                    <p class="mt-2"><strong>Note:</strong> This request is for an offer with <?php 
                                        switch($request['price_type']) {
                                            case 'flat_percent':
                                                echo 'a flat percentage reward';
                                                break;
                                            case 'upto_percent':
                                                echo 'an upto percentage reward';
                                                break;
                                        }
                                    ?>. Please verify the amount is correct before approving.</p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($request['price_type']) && $request['price_type'] !== 'fixed'): ?>
                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-exclamation-triangle"></i> Percentage-Based Offer</h6>
                                    <p>This is a percentage-based offer. Please enter the required values to calculate the reward:</p>
                                    <?php 
                                    if (!empty($request['offer_price']) && $request['offer_price'] > 0) {
                                        echo '<p><strong>Offer Details:</strong> ' . number_format($request['offer_price'], 2) . '% ' . 
                                             ($request['price_type'] === 'upto_percent' ? 'up to' : 'flat') . ' reward</p>';
                                    } else {
                                        echo '<p><strong>Offer Details:</strong> Percentage not available in database. Please enter manually.</p>';
                                    }
                                    ?>
                                    <form method="POST" action="?id=<?php echo $id; ?>&action=approve">
                                        <?php if ($request['price_type'] === 'flat_percent'): ?>
                                            <div class="mb-3">
                                                <label for="transaction_amount" class="form-label">Transaction Amount (₹)</label>
                                                <input type="number" class="form-control" id="transaction_amount" name="transaction_amount" 
                                                       step="0.01" min="0" required
                                                       placeholder="Enter transaction amount">
                                                <div class="form-text">Enter the actual transaction amount for which the user gets a flat <?php echo number_format($request['offer_price'] > 0 ? $request['offer_price'] : 0, 2); ?>% reward</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="custom_amount" class="form-label">Calculated Reward Amount (₹)</label>
                                                <input type="number" class="form-control" id="custom_amount" name="custom_amount" 
                                                       step="0.01" min="0" readonly>
                                                <div class="form-text">This will be automatically calculated as <?php echo number_format($request['offer_price'] > 0 ? $request['offer_price'] : 0, 2); ?>% of the transaction amount</div>
                                            </div>
                                        <?php elseif ($request['price_type'] === 'upto_percent'): ?>
                                            <div class="mb-3">
                                                <label for="transaction_amount" class="form-label">Transaction Amount (₹)</label>
                                                <input type="number" class="form-control" id="transaction_amount" name="transaction_amount" 
                                                       step="0.01" min="0" required
                                                       placeholder="Enter transaction amount">
                                                <div class="form-text">Enter the actual transaction amount for which the user gets up to <?php echo number_format($request['offer_price'] > 0 ? $request['offer_price'] : 0, 2); ?>% reward</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="same_percent" class="form-label">Same Percent (%)</label>
                                                <input type="number" class="form-control" id="same_percent" name="same_percent" 
                                                       step="0.01" min="0" max="100" required
                                                       placeholder="Enter same percent value">
                                                <div class="form-text">Enter the actual percentage to be applied (up to <?php echo number_format($request['offer_price'] > 0 ? $request['offer_price'] : 0, 2); ?>%)</div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="custom_amount" class="form-label">Calculated Reward Amount (₹)</label>
                                                <input type="number" class="form-control" id="custom_amount" name="custom_amount" 
                                                       step="0.01" min="0" readonly>
                                                <div class="form-text">This will be automatically calculated based on the transaction amount and same percent</div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <button type="submit" class="btn btn-success btn-lg">
                                            <i class="bi bi-check-circle"></i> Approve with Calculated Reward
                                        </button>
                                    </form>
                                    
                                    <script>
                                        // Calculate reward amount based on price type
                                        function calculateReward() {
                                            const transactionAmount = parseFloat(document.getElementById('transaction_amount').value) || 0;
                                            let calculatedAmount = 0;
                                            <?php if ($request['price_type'] === 'flat_percent'): ?>
                                                const percentage = <?php echo !empty($request['offer_price']) ? $request['offer_price'] : 0; ?>;
                                                calculatedAmount = (percentage / 100) * transactionAmount;
                                            <?php elseif ($request['price_type'] === 'upto_percent'): ?>
                                                const samePercent = parseFloat(document.getElementById('same_percent').value) || 0;
                                                // Ensure same percent doesn't exceed offer percent
                                                const maxPercent = <?php echo !empty($request['offer_price']) ? $request['offer_price'] : 0; ?>;
                                                const actualPercent = Math.min(samePercent, maxPercent);
                                                calculatedAmount = (actualPercent / 100) * transactionAmount;
                                            <?php endif; ?>
                                            document.getElementById('custom_amount').value = calculatedAmount.toFixed(2);
                                        }
                                        
                                        // Add event listeners
                                        document.getElementById('transaction_amount').addEventListener('input', calculateReward);
                                        <?php if ($request['price_type'] === 'upto_percent'): ?>
                                            document.getElementById('same_percent').addEventListener('input', calculateReward);
                                        <?php endif; ?>
                                    </script>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="?id=<?php echo $id; ?>&action=reject" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Reject Request
                                    </a>
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Cancel
                                    </a>
                                </div>
                            <?php else: ?>
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
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>