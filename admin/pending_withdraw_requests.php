<?php
$page_title = "Pending Withdraw Requests";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Handle batch operations
if (isset($_POST['batch_action']) && isset($_POST['selected_requests']) && is_array($_POST['selected_requests'])) {
    $selected_ids = $_POST['selected_requests'];
    $action = $_POST['batch_action'];
    
    if (!empty($selected_ids)) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            if ($action === 'approve') {
                // Batch approve selected requests
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'approved' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                // Update wallet history status to approved
                foreach ($selected_ids as $id) {
                    // Get request details
                    $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
                    $stmt->execute([$id]);
                    $request = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($request) {
                        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE user_id = ? AND amount = ? AND type = 'debit' AND status = 'pending' AND description = 'Withdrawal request submitted'");
                        $stmt->execute([$request['user_id'], $request['amount']]);
                        
                        // Check if this is a purchase (identified by UPI ID starting with "purchase@")
                        if (strpos($request['upi_id'], 'purchase@') === 0) {
                            // Add amount to user's wallet balance
                            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                            $stmt->execute([$request['amount'], $request['user_id']]);
                            
                            // Add entry to wallet history
                            $description = "Purchase/Application approved: " . $request['offer_title'];
                            $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'credit', 'approved', ?)");
                            $stmt->execute([$request['user_id'], $request['amount'], $description]);
                        }
                    }
                }
                
                $message = count($selected_ids) . " request(s) approved successfully!";
            } elseif ($action === 'reject') {
                // Batch reject selected requests
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE withdraw_requests SET status = 'rejected' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                // Update wallet history status to rejected and refund amounts
                foreach ($selected_ids as $id) {
                    // Get request details
                    $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
                    $stmt->execute([$id]);
                    $request = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($request) {
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
                    }
                }
                
                $message = count($selected_ids) . " request(s) rejected successfully!";
            } elseif ($action === 'delete') {
                // Batch delete selected requests
                foreach ($selected_ids as $id) {
                    // Get request details
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
                    }
                }
                
                $message = count($selected_ids) . " request(s) deleted successfully!";
            }
            
            // Commit transaction
            $pdo->commit();
        } catch(PDOException $e) {
            // Rollback transaction on error
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
            $error = "Error processing batch action: " . $e->getMessage();
        }
    } else {
        $error = "No requests selected for batch action.";
    }
    
    // Redirect back to the page with message
    if (isset($error)) {
        header("Location: pending_withdraw_requests.php?error=" . urlencode($error));
    } else {
        header("Location: pending_withdraw_requests.php?message=" . urlencode($message));
    }
    exit;
}

// Get filter parameters
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Build query based on filters
$sql = "SELECT wr.*, u.name, u.email, u.id as user_id FROM withdraw_requests wr 
        JOIN users u ON wr.user_id = u.id 
        WHERE wr.status = 'pending'";

$params = [];

// Add type filter
if (!empty($filter_type)) {
    if ($filter_type === 'purchase') {
        $sql .= " AND wr.upi_id LIKE 'purchase@%'";
    } elseif ($filter_type === 'withdrawal') {
        $sql .= " AND wr.upi_id NOT LIKE 'purchase@%'";
    }
}

// Add User ID filter
if (!empty($filter_user_id)) {
    $sql .= " AND u.id = ?";
    $params[] = $filter_user_id;
}

$sql .= " ORDER BY wr.created_at DESC";

// Fetch pending withdraw requests with filters
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pending_withdraw_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching pending withdraw requests: " . $e->getMessage();
    $pending_withdraw_requests = [];
}

// Get previously selected checkboxes from session or POST data
$selected_checkboxes = [];
if (isset($_POST['selected_requests']) && is_array($_POST['selected_requests'])) {
    $selected_checkboxes = $_POST['selected_requests'];
} elseif (isset($_SESSION['selected_withdraw_requests']) && is_array($_SESSION['selected_withdraw_requests'])) {
    $selected_checkboxes = $_SESSION['selected_withdraw_requests'];
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Pending Withdraw Requests</h2>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Filter Requests</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3" id="filterForm">
                <div class="col-md-4">
                    <label for="type" class="form-label">Request Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="purchase" <?php echo ($filter_type === 'purchase') ? 'selected' : ''; ?>>Purchase Requests</option>
                        <option value="withdrawal" <?php echo ($filter_type === 'withdrawal') ? 'selected' : ''; ?>>Withdrawals</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="number" name="user_id" id="user_id" class="form-control" value="<?php echo htmlspecialchars($filter_user_id); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="pending_withdraw_requests.php" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>All Pending Withdraw Requests</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pending_withdraw_requests)): ?>
                <p>No pending withdraw requests <?php echo (!empty($filter_type) || !empty($filter_user_id)) ? 'match your filters' : ''; ?>.</p>
            <?php else: ?>
                <form method="POST" id="batchForm">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>User ID</th>
                                    <th>Email</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Requested On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_withdraw_requests as $request): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected_requests[]" value="<?php echo $request['id']; ?>" 
                                            <?php echo in_array($request['id'], $selected_checkboxes) ? 'checked' : ''; ?>></td>
                                        <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                                        <td>₹<?php echo number_format($request['amount'], 2); ?></td>
                                        <td>
                                            <?php if (strpos($request['upi_id'], 'purchase@') === 0): ?>
                                                <span class="badge bg-success">Purchase Request</span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Withdrawal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($request['offer_title'])): ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($request['offer_title']); ?></strong><br>
                                                    <div class="text-truncate-slider" style="max-width: 200px; overflow: hidden; position: relative;">
                                                        <div class="slider-text" style="white-space: nowrap; display: inline-block;">
                                                            <?php echo htmlspecialchars($request['offer_description']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-truncate-slider" style="max-width: 200px; overflow: hidden; position: relative;">
                                                    <div class="slider-text" style="white-space: nowrap; display: inline-block;">
                                                        <?php echo htmlspecialchars($request['upi_id']); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($request['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=approve" 
                                                   class="btn btn-success">Approve</a>
                                                <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=reject" 
                                                   class="btn btn-danger">Reject</a>
                                                <!-- Added Delete button that actually deletes the request -->
                                                <a href="delete_withdraw.php?id=<?php echo $request['id']; ?>" 
                                                   class="btn btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to permanently delete this request? This action cannot be undone.')">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Batch action buttons -->
                    <div class="mt-3">
                        <select name="batch_action" class="form-select d-inline-block w-auto">
                            <option value="">Select action...</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to perform this action on selected requests?')">Apply</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Select all checkboxes functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_requests[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Store selected checkboxes in session storage to persist after page refresh
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_requests[]"]');
    const selectAll = document.getElementById('selectAll');
    
    // Restore checkbox states from session storage
    checkboxes.forEach(checkbox => {
        const storedState = sessionStorage.getItem('withdraw_checkbox_' + checkbox.value);
        if (storedState === 'true') {
            checkbox.checked = true;
        }
        
        // Save checkbox state when changed
        checkbox.addEventListener('change', function() {
            sessionStorage.setItem('withdraw_checkbox_' + this.value, this.checked);
            
            // Update select all checkbox state
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            if (selectAll) {
                selectAll.checked = allChecked;
            }
        });
    });
    
    // Select all functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                sessionStorage.setItem('withdraw_checkbox_' + checkbox.value, this.checked);
            });
        });
    }
    
    // Handle form submission to clear session storage after successful action
    const batchForm = document.getElementById('batchForm');
    if (batchForm) {
        batchForm.addEventListener('submit', function() {
            // Clear session storage for selected checkboxes after form submission
            checkboxes.forEach(checkbox => {
                sessionStorage.removeItem('withdraw_checkbox_' + checkbox.value);
            });
            if (selectAll) {
                selectAll.checked = false;
            }
        });
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>