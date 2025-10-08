<?php
$page_title = "Pending Wallet Approvals";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Handle batch operations
if (isset($_POST['batch_action']) && isset($_POST['selected_entries']) && is_array($_POST['selected_entries'])) {
    $selected_ids = $_POST['selected_entries'];
    $action = $_POST['batch_action'];
    
    if (!empty($selected_ids)) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            if ($action === 'approve') {
                // Batch approve selected entries
                foreach ($selected_ids as $id) {
                    // Get entry details
                    $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE id = ?");
                    $stmt->execute([$id]);
                    $entry = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($entry) {
                        // Update wallet history status
                        $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'approved' WHERE id = ?");
                        $stmt->execute([$id]);
                        
                        // Update user wallet balance
                        if ($entry['type'] === 'credit') {
                            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                        } else {
                            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                        }
                        $stmt->execute([$entry['amount'], $entry['user_id']]);
                    }
                }
                
                $message = count($selected_ids) . " entry(s) approved successfully!";
            } elseif ($action === 'reject') {
                // Batch reject selected entries
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE wallet_history SET status = 'rejected' WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                $message = count($selected_ids) . " entry(s) rejected successfully!";
            } elseif ($action === 'delete') {
                // Batch delete selected entries
                $placeholders = str_repeat('?,', count($selected_ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM wallet_history WHERE id IN ($placeholders)");
                $stmt->execute($selected_ids);
                
                $message = count($selected_ids) . " entry(s) deleted successfully!";
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
        $error = "No entries selected for batch action.";
    }
    
    // Redirect back to the page with message
    if (isset($error)) {
        header("Location: pending_wallet_approvals.php?error=" . urlencode($error));
    } else {
        header("Location: pending_wallet_approvals.php?message=" . urlencode($message));
    }
    exit;
}

// Get filter parameters
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_user_id = isset($_GET['user_id']) ? $_GET['user_id'] : '';

// Build query based on filters
$sql = "SELECT wh.*, u.name, u.email FROM wallet_history wh 
        JOIN users u ON wh.user_id = u.id 
        WHERE wh.status = 'pending'";

$params = [];

// Add type filter
if (!empty($filter_type)) {
    if ($filter_type === 'credit') {
        $sql .= " AND wh.type = 'credit'";
    } elseif ($filter_type === 'debit') {
        $sql .= " AND wh.type = 'debit'";
    }
}

// Add User ID filter
if (!empty($filter_user_id)) {
    $sql .= " AND u.id = ?";
    $params[] = $filter_user_id;
}

$sql .= " ORDER BY wh.created_at DESC";

// Fetch pending wallet history with filters
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pending_wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching pending wallet history: " . $e->getMessage();
    $pending_wallet_history = [];
}

// Get previously selected checkboxes from session or POST data
$selected_checkboxes = [];
if (isset($_POST['selected_entries']) && is_array($_POST['selected_entries'])) {
    $selected_checkboxes = $_POST['selected_entries'];
} elseif (isset($_SESSION['selected_wallet_entries']) && is_array($_SESSION['selected_wallet_entries'])) {
    $selected_checkboxes = $_SESSION['selected_wallet_entries'];
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Pending Wallet Approvals</h2>
    
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
            <h5>Filter Entries</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3" id="filterForm">
                <div class="col-md-4">
                    <label for="type" class="form-label">Entry Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="credit" <?php echo ($filter_type === 'credit') ? 'selected' : ''; ?>>Credit</option>
                        <option value="debit" <?php echo ($filter_type === 'debit') ? 'selected' : ''; ?>>Debit</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="user_id" class="form-label">User ID</label>
                    <input type="number" name="user_id" id="user_id" class="form-control" value="<?php echo htmlspecialchars($filter_user_id); ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="pending_wallet_approvals.php" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>All Pending Wallet Approvals</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pending_wallet_history)): ?>
                <p>No pending wallet approvals <?php echo (!empty($filter_type) || !empty($filter_user_id)) ? 'match your filters' : ''; ?>.</p>
            <?php else: ?>
                <form method="POST" id="batchForm">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>User</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Type</th>
                                    <th>Requested On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_wallet_history as $history): ?>
                                    <tr>
                                        <td><input type="checkbox" name="selected_entries[]" value="<?php echo $history['id']; ?>" 
                                            <?php echo in_array($history['id'], $selected_checkboxes) ? 'checked' : ''; ?>></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span><?php echo htmlspecialchars($history['name']); ?></span>
                                                <small class="text-muted"><?php echo htmlspecialchars($history['email']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 150px;">
                                                <?php echo htmlspecialchars($history['description']); ?>
                                            </div>
                                        </td>
                                        <td>₹<?php echo number_format($history['amount'], 2); ?></td>
                                        <td>
                                            <?php if ($history['type'] == 'credit'): ?>
                                                <span class="badge bg-success">Credit</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Debit</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($history['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="approve_wallet.php?id=<?php echo $history['id']; ?>&action=approve" 
                                                   class="btn btn-success">Approve</a>
                                                <a href="approve_wallet.php?id=<?php echo $history['id']; ?>&action=reject" 
                                                   class="btn btn-danger">Reject</a>
                                                <!-- Added Delete button that actually deletes the entry -->
                                                <a href="delete_wallet.php?id=<?php echo $history['id']; ?>" 
                                                   class="btn btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to permanently delete this entry? This action cannot be undone.')">Delete</a>
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
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to perform this action on selected entries?')">Apply</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Select all checkboxes functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_entries[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Store selected checkboxes in session storage to persist after page refresh
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="selected_entries[]"]');
    const selectAll = document.getElementById('selectAll');
    
    // Restore checkbox states from session storage
    checkboxes.forEach(checkbox => {
        const storedState = sessionStorage.getItem('wallet_checkbox_' + checkbox.value);
        if (storedState === 'true') {
            checkbox.checked = true;
        }
        
        // Save checkbox state when changed
        checkbox.addEventListener('change', function() {
            sessionStorage.setItem('wallet_checkbox_' + this.value, this.checked);
            
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
                sessionStorage.setItem('wallet_checkbox_' + checkbox.value, this.checked);
            });
        });
    }
    
    // Handle form submission to clear session storage after successful action
    const batchForm = document.getElementById('batchForm');
    if (batchForm) {
        batchForm.addEventListener('submit', function() {
            // Clear session storage for selected checkboxes after form submission
            checkboxes.forEach(checkbox => {
                sessionStorage.removeItem('wallet_checkbox_' + checkbox.value);
            });
            if (selectAll) {
                selectAll.checked = false;
            }
        });
    }
});
</script>

<?php include 'includes/admin_footer.php'; ?>