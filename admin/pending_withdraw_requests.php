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
// Use a more flexible JOIN to match offers
$sql = "SELECT wr.*, u.name, u.email, u.id as user_id, o.price_type FROM withdraw_requests wr 
        JOIN users u ON wr.user_id = u.id 
        LEFT JOIN offers o ON wr.offer_title = o.title OR wr.offer_title LIKE CONCAT('%', o.title, '%')
        WHERE wr.status = 'pending'";

$params = [];

// Add type filter
if (!empty($filter_type)) {
    error_log("Applying filter type: " . $filter_type);
    if ($filter_type === 'purchase') {
        $sql .= " AND wr.upi_id LIKE 'purchase@%'";
        error_log("Added purchase filter to SQL");
    } elseif ($filter_type === 'withdrawal') {
        $sql .= " AND wr.upi_id NOT LIKE 'purchase@%'";
        error_log("Added withdrawal filter to SQL");
    }
} else {
    error_log("No filter type specified - showing all pending requests");
    // By default, show all pending requests (both purchase and withdrawal)
}

// Add User ID filter
if (!empty($filter_user_id)) {
    $sql .= " AND u.id = ?";
    $params[] = $filter_user_id;

}

$sql .= " ORDER BY wr.created_at DESC";

// Debug: Log the SQL query
error_log("Pending requests SQL: " . $sql);
error_log("Pending requests params: " . json_encode($params));

// Fetch pending withdraw requests with filters
try {
    error_log("Executing SQL query: " . $sql);
    error_log("With parameters: " . json_encode($params));
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pending_withdraw_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Found " . count($pending_withdraw_requests) . " pending requests");
    
    // Log details of each request for debugging
    foreach ($pending_withdraw_requests as $request) {
        error_log("Request ID: " . $request['id'] . 
                  ", User ID: " . $request['user_id'] . 
                  ", Amount: " . $request['amount'] . 
                  ", UPI ID: " . $request['upi_id'] . 
                  ", Offer Title: " . $request['offer_title'] . 
                  ", Status: " . $request['status'] .
                  ", Is Purchase: " . (strpos($request['upi_id'], 'purchase@') === 0 ? 'Yes' : 'No'));
    }
    
    // Also log all requests without filters to see what's in the database
    $allStmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE status = 'pending' ORDER BY created_at DESC LIMIT 10");
    $allStmt->execute();
    $allRequests = $allStmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Total pending requests in database (no filters): " . count($allRequests));
    foreach ($allRequests as $request) {
        error_log("All requests - ID: " . $request['id'] . 
                  ", User ID: " . $request['user_id'] . 
                  ", Amount: " . $request['amount'] . 
                  ", UPI ID: " . $request['upi_id'] . 
                  ", Offer Title: " . $request['offer_title'] . 
                  ", Status: " . $request['status'] .
                  ", Is Purchase: " . (strpos($request['upi_id'], 'purchase@') === 0 ? 'Yes' : 'No'));
    }
    
    // Log all users to see if there's a mismatch
    $userStmt = $pdo->prepare("SELECT id, name, email FROM users ORDER BY id LIMIT 20");
    $userStmt->execute();
    $users = $userStmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("Users in database:");
    foreach ($users as $user) {
        error_log("User ID: " . $user['id'] . ", Name: " . $user['name'] . ", Email: " . $user['email']);
    }
} catch(PDOException $e) {
    error_log("Database error fetching pending withdraw requests: " . $e->getMessage());
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

<style>
    :root {
        --primary-color: #6f42c1;
        --secondary-color: #5a32a3;
        --accent-color: #00c9a7;
        --light-bg: #f8f9fa;
        --dark-text: #212529;
        --light-text: #6c757d;
        --border-color: #dee2e6;
        --success-color: #20c997;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
    }
    
    .wallet-header {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .page-title {
        font-weight: 700;
        font-size: 1.75rem;
        margin-bottom: 0;
        color: var(--primary-color);
    }
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 15px;
        overflow: hidden;
    }
    
    .card-header {
        background: white;
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
    }
    
    .card-title {
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 0;
        font-size: 1.25rem;
    }
    
    .filter-container {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .filter-title {
        font-weight: 600;
        color: var(--dark-text);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead {
        background-color: var(--light-bg);
    }
    
    .table th {
        font-weight: 600;
        color: var(--dark-text);
        border-bottom: 2px solid var(--border-color);
        padding: 12px 15px;
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-color: var(--border-color);
    }
    
    .badge-custom {
        padding: 0.5em 0.75em;
        font-weight: 500;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    
    /* Status badges with black text */
    .badge-success {
        background-color: rgba(32, 201, 151, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(32, 201, 151, 0.3) !important;
    }
    
    .badge-danger {
        background-color: rgba(220, 53, 69, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(220, 53, 69, 0.3) !important;
    }
    
    .badge-warning {
        background-color: rgba(255, 193, 7, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(255, 193, 7, 0.3) !important;
    }
    
    .badge-primary {
        background-color: rgba(111, 66, 193, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(111, 66, 193, 0.3) !important;
    }
    
    .badge-info {
        background-color: rgba(13, 202, 240, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(13, 202, 240, 0.3) !important;
    }
    
    .badge-secondary {
        background-color: rgba(108, 117, 125, 0.15) !important;
        color: #000000 !important;
        border: 1px solid rgba(108, 117, 125, 0.3) !important;
    }
    
    .btn-action {
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: 5px;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #20c997, #1aa179);
        border: none;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc3545, #bd2130);
        border: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
    }
    
    .btn-outline-danger {
        border-color: #dc3545;
        color: #dc3545;
    }
    
    .btn-outline-danger:hover {
        background: linear-gradient(135deg, #dc3545, #bd2130);
        border-color: #dc3545;
        color: white;
    }
    
    .batch-actions {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    
    .form-select {
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .btn-group-sm .btn {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    
    .alert-success {
        background-color: rgba(32, 201, 151, 0.1);
        color: var(--success-color);
    }
    
    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: var(--danger-color);
    }
    
    /* Responsive styles */
    @media (max-width: 992px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .wallet-header, .filter-container {
            padding: 15px;
        }
    }
    
    @media (max-width: 768px) {
        .wallet-header, .filter-container, .card-header {
            padding: 12px 15px;
        }
        
        .page-title {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }
        
        .card-title {
            font-size: 1.1rem;
        }
        
        .table th, .table td {
            padding: 10px 8px;
            font-size: 0.85rem;
        }
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.7rem;
            margin-right: 3px;
            margin-bottom: 3px;
        }
        
        .form-select, .form-control {
            padding: 6px 10px;
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 576px) {
        .wallet-header, .filter-container, .card-header {
            padding: 10px 12px;
        }
        
        .page-title {
            font-size: 1.1rem;
        }
        
        .filter-title {
            font-size: 1rem;
            margin-bottom: 10px;
        }
        
        .table th, .table td {
            padding: 8px 5px;
            font-size: 0.75rem;
        }
        
        .btn-group-sm .btn {
            padding: 4px 6px;
            font-size: 0.7rem;
        }
        
        .batch-actions {
            padding: 12px;
        }
        
        .form-select, .form-control {
            padding: 5px 8px;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 400px) {
        .table th, .table td {
            padding: 6px 3px;
            font-size: 0.7rem;
        }
        
        .btn-action {
            font-size: 0.65rem;
            padding: 3px 5px;
        }
        
        .form-select, .form-control {
            padding: 4px 6px;
            font-size: 0.75rem;
        }
    }
    
    /* Text slider styles */
    .text-truncate-slider {
        max-width: 150px;
        overflow: hidden;
        position: relative;
    }
    
    .slider-text {
        white-space: nowrap;
        display: inline-block;
        animation: slide-left 10s linear infinite;
        padding-left: 100%;
    }
    
    @keyframes slide-left {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-100%);
        }
    }
    
    .text-truncate-slider:hover .slider-text {
        animation-play-state: running;
    }
</style>

<div class="container-fluid">
    <div class="wallet-header">
        <h1 class="page-title">Pending Withdraw Requests</h1>
    </div>
    
    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Filter Form -->
    <div class="filter-container">
        <h5 class="filter-title">Filter Requests</h5>
        <form method="GET" class="row g-3" id="filterForm">
            <div class="col-md-4 col-sm-6">
                <label for="type" class="form-label">Request Type</label>
                <select name="type" id="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="purchase" <?php echo ($filter_type === 'purchase') ? 'selected' : ''; ?>>Purchase Requests</option>
                    <option value="withdrawal" <?php echo ($filter_type === 'withdrawal') ? 'selected' : ''; ?>>Withdrawals</option>
                </select>
            </div>
            <div class="col-md-4 col-sm-6">
                <label for="user_id" class="form-label">User ID</label>
                <input type="number" name="user_id" id="user_id" class="form-control" value="<?php echo htmlspecialchars($filter_user_id); ?>" placeholder="Enter User ID">
            </div>
            <div class="col-md-4 col-sm-12 d-flex align-items-end">
                <div class="btn-group w-100" role="group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Apply Filters
                    </button>
                    <a href="pending_withdraw_requests.php" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Pending Withdraw Requests</h5>
            <span class="badge bg-secondary"><?php echo count($pending_withdraw_requests); ?> requests</span>
        </div>
        <div class="card-body">
            <?php if (empty($pending_withdraw_requests)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">No pending withdraw requests <?php echo (!empty($filter_type) || !empty($filter_user_id)) ? 'match your filters' : ''; ?>.</p>
                    <?php if (!empty($filter_type) || !empty($filter_user_id)): ?>
                        <a href="pending_withdraw_requests.php" class="btn btn-primary mt-3">
                            <i class="bi bi-eye me-1"></i> View All Requests
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <form method="POST" id="batchForm">
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>User ID</th>
                                    <th>Email</th>
                                    <th>Amount / Reward</th>
                                    <th>Price Type</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Requested On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_withdraw_requests as $request): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="selected_requests[]" value="<?php echo $request['id']; ?>" 
                                                    <?php echo in_array($request['id'], $selected_checkboxes) ? 'checked' : ''; ?>>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($request['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($request['email']); ?></td>
                                        <td>
                                            <?php 
                                            // Try to get price_type from offers table if not already in request
                                            $price_type = '';
                                            $offer_price = 0;
                                            if (!empty($request['price_type'])) {
                                                $price_type = $request['price_type'];
                                                error_log("Price type from JOIN: " . $price_type . " for request ID: " . $request['id']);
                                            } else {
                                                // Try to fetch price_type from offers table
                                                try {
                                                    $offerStmt = $pdo->prepare("SELECT price_type, price FROM offers WHERE title = ?");
                                                    $offerStmt->execute([$request['offer_title']]);
                                                    $offer = $offerStmt->fetch(PDO::FETCH_ASSOC);
                                                    if ($offer) {
                                                        $price_type = $offer['price_type'];
                                                        $offer_price = $offer['price'];
                                                        error_log("Price type from separate query: " . $price_type . " for request ID: " . $request['id'] . ", Offer Title: " . $request['offer_title']);
                                                    } else {
                                                        error_log("No offer found for title: " . $request['offer_title'] . " in request ID: " . $request['id']);
                                                    }
                                                } catch (PDOException $e) {
                                                    error_log("Error fetching offer price_type: " . $e->getMessage() . " for request ID: " . $request['id']);
                                                }
                                            }
                                            
                                            // Display amount information
                                            if (!empty($price_type) && $price_type !== 'fixed') {
                                                // For percentage-based offers, show that admin needs to determine amount
                                                echo '<span class="text-warning">To be determined</span>';
                                                echo '<br><small class="text-muted">(' . number_format($offer_price, 2) . '% offer)</small>';
                                                echo '<br><small class="text-info">Click "Approve" to enter values</small>';
                                            } else {
                                                // For fixed price offers, show the amount
                                                echo '₹' . number_format($request['amount'], 2);
                                            }
                                            
                                            if (!empty($price_type)) {
                                                switch($price_type) {
                                                    case 'fixed':
                                                        echo '<br><span class="badge badge-custom badge-success">Fixed</span>';
                                                        break;
                                                    case 'flat_percent':
                                                        echo '<br><span class="badge badge-custom badge-primary">Flat %</span>';
                                                        break;
                                                    case 'upto_percent':
                                                        echo '<br><span class="badge badge-custom badge-warning">Upto %</span>';
                                                        break;
                                                    default:
                                                        echo '<br><span class="badge badge-custom badge-secondary">Unknown</span>';
                                                }
                                            } else {
                                                echo '<br><span class="badge badge-custom badge-secondary">N/A</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $isPurchase = strpos($request['upi_id'], 'purchase@') === 0;
                                            error_log("Checking if request ID " . $request['id'] . " is purchase: " . ($isPurchase ? 'Yes' : 'No') . ", UPI ID: " . $request['upi_id']);
                                            
                                            if ($isPurchase): ?>
                                                <span class="badge badge-custom badge-success">Purchase Request</span>
                                            <?php else: ?>
                                                <span class="badge badge-custom badge-primary">Withdrawal</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($request['offer_title'])): ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($request['offer_title']); ?></strong><br>
                                                    <div class="text-truncate-slider">
                                                        <div class="slider-text">
                                                            <?php echo htmlspecialchars($request['offer_description']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="text-truncate-slider">
                                                    <div class="slider-text">
                                                        <?php echo htmlspecialchars($request['upi_id']); ?>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y, h:i A', strtotime($request['created_at'])); ?></td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=approve" 
                                                   class="btn btn-success btn-action">
                                                    <i class="bi bi-check-circle me-1"></i>Approve
                                                </a>
                                                <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=reject" 
                                                   class="btn btn-danger btn-action">
                                                    <i class="bi bi-x-circle me-1"></i>Reject
                                                </a>
                                                <!-- Added Delete button that actually deletes the request -->
                                                <a href="delete_withdraw.php?id=<?php echo $request['id']; ?>" 
                                                   class="btn btn-outline-danger btn-action"
                                                   onclick="return confirm('Are you sure you want to permanently delete this request? This action cannot be undone.')">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                
                <!-- Batch action buttons -->
                <div class="batch-actions">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <select name="batch_action" class="form-select d-inline-block w-auto" form="batchForm">
                            <option value="">Select batch action...</option>
                            <option value="approve">Approve Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="delete">Delete Selected</option>
                        </select>
                        <button type="submit" class="btn btn-primary" form="batchForm" onclick="return confirm('Are you sure you want to perform this action on selected requests?')">
                            <i class="bi bi-lightning me-1"></i>Apply Action
                        </button>
                    </div>
                </div>
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
    
    // Pause animation when mouse leaves
    const sliders = document.querySelectorAll('.text-truncate-slider');
    sliders.forEach(slider => {
        const textElement = slider.querySelector('.slider-text');
        
        slider.addEventListener('mouseenter', function() {
            textElement.style.animationPlayState = 'running';
        });
        
        slider.addEventListener('mouseleave', function() {
            textElement.style.animationPlayState = 'paused';
        });
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>