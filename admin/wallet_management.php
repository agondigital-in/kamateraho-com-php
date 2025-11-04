<?php
$page_title = "Wallet Management";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Get filter parameters
$filter_user_id = isset($_GET['filter_user_id']) ? intval($_GET['filter_user_id']) : null;

// Fetch all users with their wallet balances (with optional filter)
try {
    if ($filter_user_id) {
        $stmt = $pdo->prepare("SELECT id, name, email, phone, city, state, wallet_balance FROM users WHERE id = ? ORDER BY wallet_balance DESC");
        $stmt->execute([$filter_user_id]);
    } else {
        $stmt = $pdo->query("SELECT id, name, email, phone, city, state, wallet_balance FROM users ORDER BY wallet_balance DESC");
    }
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total wallet balance across all users
    $total_balance = 0;
    foreach ($users as $user) {
        $total_balance += $user['wallet_balance'];
    }
} catch(PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
    $users = [];
    $total_balance = 0;
}

// If a specific user is selected, fetch their wallet history
$user_wallet_history = [];
$selected_user = null;
$referral_info = [];
$wallet_filter = '';
$transaction_type_filter = '';
if (isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    // Get wallet history filters
    $wallet_filter = isset($_GET['wallet_filter']) ? $_GET['wallet_filter'] : '';
    $transaction_type_filter = isset($_GET['transaction_type']) ? $_GET['transaction_type'] : '';
    
    // Fetch user details
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, phone, city, state, wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $selected_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($selected_user) {
            // Fetch wallet history for this user with optional filters
            $sql = "SELECT * FROM wallet_history WHERE user_id = ?";
            $params = [$user_id];
            
            // Add transaction type filter if selected
            if (!empty($transaction_type_filter)) {
                switch ($transaction_type_filter) {
                    case 'withdrawal':
                        $sql .= " AND description LIKE 'Withdrawal request submitted%'";
                        break;
                    case 'spin_earn':
                        $sql .= " AND description = 'Spin & Earn Reward'";
                        break;
                    case 'referral':
                        $sql .= " AND description LIKE 'Referral Bonus%'";
                        break;
                    case 'welcome':
                        $sql .= " AND description = 'Welcome Bonus'";
                        break;
                }
            }
            
            // Add text filter if provided
            if (!empty($wallet_filter)) {
                $sql .= " AND (description LIKE ? OR amount = ?)";
                $params[] = '%' . $wallet_filter . '%';
                $params[] = is_numeric($wallet_filter) ? floatval($wallet_filter) : 0;
            }
            
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $user_wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check if this user was referred by someone (look for referral bonus entries)
            $stmt = $pdo->prepare("SELECT description FROM wallet_history WHERE description LIKE 'Referral Bonus for user ID: " . $user_id . "'");
            $stmt->execute();
            $referral_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Extract referrer information
            foreach ($referral_entries as $entry) {
                // Extract referrer ID from description
                if (preg_match('/Referral Bonus for user ID: (\d+)/', $entry['description'], $matches)) {
                    $referrer_id = $matches[1];
                    // Get referrer details
                    $stmt2 = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
                    $stmt2->execute([$referrer_id]);
                    $referrer = $stmt2->fetch(PDO::FETCH_ASSOC);
                    if ($referrer) {
                        $referral_info[] = $referrer;
                    }
                }
            }
        }
    } catch(PDOException $e) {
        $error = "Error fetching user data: " . $e->getMessage();
    }
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
    
    .stats-container {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    
    .stats-box {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        flex: 1;
        min-width: 250px;
        border-left: 4px solid var(--primary-color);
        transition: transform 0.3s ease;
    }
    
    .stats-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 5px;
    }
    
    .stats-label {
        color: var(--light-text);
        font-size: 0.9rem;
        font-weight: 500;
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
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1rem;
    }
    
    .btn-action {
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: 5px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    }
    
    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-color: var(--primary-color);
        color: white;
    }
    
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    
    .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
    }
    
    .form-select {
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 8px 12px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
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
    
    .wallet-balance {
        font-size: 2rem;
        font-weight: 700;
        color: var(--success-color);
    }
    
    /* Responsive styles */
    @media (max-width: 992px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .wallet-header, .filter-container {
            padding: 15px;
        }
        
        .stats-box {
            min-width: 200px;
        }
        
        .stats-number {
            font-size: 1.5rem;
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
        
        .stats-container {
            gap: 10px;
        }
        
        .stats-box {
            padding: 15px;
            min-width: 150px;
        }
        
        .stats-number {
            font-size: 1.25rem;
            margin-bottom: 3px;
        }
        
        .stats-label {
            font-size: 0.8rem;
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
        }
        
        .form-select, .form-control {
            padding: 6px 10px;
            font-size: 0.85rem;
        }
        
        .wallet-balance {
            font-size: 1.5rem;
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
        
        .stats-container {
            flex-direction: column;
            gap: 10px;
        }
        
        .stats-box {
            width: 100%;
            min-width: auto;
        }
        
        .stats-number {
            font-size: 1.5rem;
        }
        
        .table th, .table td {
            padding: 8px 5px;
            font-size: 0.75rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }
        
        .btn-action {
            font-size: 0.65rem;
            padding: 3px 5px;
        }
        
        .form-select, .form-control {
            padding: 5px 8px;
            font-size: 0.8rem;
        }
        
        .wallet-balance {
            font-size: 1.25rem;
        }
    }
    
    @media (max-width: 400px) {
        .table th, .table td {
            padding: 6px 3px;
            font-size: 0.7rem;
        }
        
        .stats-number {
            font-size: 1.25rem;
        }
        
        .form-select, .form-control {
            padding: 4px 6px;
            font-size: 0.75rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="wallet-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="page-title">Wallet Management</h1>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-secondary">Total Balance: ₹<?php echo number_format($total_balance, 2); ?></span>
            </div>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($selected_user): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            <?php echo htmlspecialchars($selected_user['name']); ?>'s Wallet
                        </h5>
                        <a href="wallet_management.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left-circle me-1"></i>Back to Users
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-4 mb-3 mb-lg-0">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <div class="user-avatar mx-auto">
                                                <?php echo strtoupper(substr(htmlspecialchars($selected_user['name']), 0, 1)); ?>
                                            </div>
                                        </div>
                                        <div class="wallet-balance">₹<?php echo number_format($selected_user['wallet_balance'], 2); ?></div>
                                        <p class="text-muted mb-0">Current Balance</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">
                                            <i class="bi bi-info-circle me-2"></i>User Information
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Email Address</label>
                                                <div class="form-control-plaintext"><?php echo htmlspecialchars($selected_user['email']); ?></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">Phone Number</label>
                                                <div class="form-control-plaintext"><?php echo htmlspecialchars($selected_user['phone']); ?></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">City</label>
                                                <div class="form-control-plaintext"><?php echo htmlspecialchars($selected_user['city']); ?></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-medium">State</label>
                                                <div class="form-control-plaintext"><?php echo htmlspecialchars($selected_user['state']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($referral_info)): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-people me-2"></i>Referral Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($referral_info as $referrer): ?>
                                        <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                            <div class="user-avatar me-3">
                                                <?php echo strtoupper(substr(htmlspecialchars($referrer['name']), 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?php echo htmlspecialchars($referrer['name']); ?></div>
                                                <div class="small text-muted">
                                                    ID: <?php echo $referrer['id']; ?> | 
                                                    Email: <?php echo htmlspecialchars($referrer['email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clock-history me-2"></i>Transaction History
                                </h5>
                                <!-- Wallet History Filters -->
                                <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                                    <form method="GET" class="d-flex gap-2">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                        <div class="input-group" style="width: 200px;">
                                            <input type="text" class="form-control" name="wallet_filter" placeholder="Search..." value="<?php echo htmlspecialchars($wallet_filter); ?>">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                        <select name="transaction_type" class="form-select" style="width: 180px;">
                                            <option value="">All Types</option>
                                            <option value="withdrawal" <?php echo $transaction_type_filter === 'withdrawal' ? 'selected' : ''; ?>>Withdrawal</option>
                                            <option value="spin_earn" <?php echo $transaction_type_filter === 'spin_earn' ? 'selected' : ''; ?>>Spin & Earn</option>
                                            <option value="referral" <?php echo $transaction_type_filter === 'referral' ? 'selected' : ''; ?>>Referral Bonus</option>
                                            <option value="welcome" <?php echo $transaction_type_filter === 'welcome' ? 'selected' : ''; ?>>Welcome Bonus</option>
                                        </select>
                                        <?php if (!empty($wallet_filter) || !empty($transaction_type_filter)): ?>
                                            <a href="wallet_management.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($user_wallet_history)): ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-wallet2 text-muted" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">No transaction history found for this user.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-container">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Description</th>
                                                    <th>Amount</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($user_wallet_history as $history): ?>
                                                    <tr>
                                                        <td><?php echo date('d M Y', strtotime($history['created_at'])); ?></td>
                                                        <td>
                                                            <div class="fw-medium">
                                                                <?php echo htmlspecialchars($history['description']); ?>
                                                            </div>
                                                            <?php 
                                                            // If this is a referral bonus entry, show the referred user's email
                                                            if (preg_match('/Referral Bonus for user ID: (\d+)/', $history['description'], $matches)) {
                                                                $referred_user_id = $matches[1];
                                                                // Get referred user's email
                                                                $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
                                                                $stmt->execute([$referred_user_id]);
                                                                $referred_user = $stmt->fetch(PDO::FETCH_ASSOC);
                                                                if ($referred_user) {
                                                                    echo '<div class="small text-muted mt-1">';
                                                                    echo '<i class="bi bi-person me-1"></i>';
                                                                    echo htmlspecialchars($referred_user['name']) . ' (' . htmlspecialchars($referred_user['email']) . ')';
                                                                    echo '</div>';
                                                                }
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <span class="fw-bold <?php echo $history['type'] === 'credit' ? 'text-success' : 'text-danger'; ?>">
                                                                <?php echo $history['type'] === 'credit' ? '+' : '-'; ?>₹<?php echo number_format($history['amount'], 2); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($history['type'] === 'credit'): ?>
                                                                <span class="badge badge-custom badge-success">
                                                                    <i class="bi bi-arrow-down-circle me-1"></i>Credit
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-custom badge-danger">
                                                                    <i class="bi bi-arrow-up-circle me-1"></i>Debit
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($history['status'] === 'approved'): ?>
                                                                <span class="badge badge-custom badge-success">
                                                                    <i class="bi bi-check-circle me-1"></i>Approved
                                                                </span>
                                                            <?php elseif ($history['status'] === 'rejected'): ?>
                                                                <span class="badge badge-custom badge-danger">
                                                                    <i class="bi bi-x-circle me-1"></i>Rejected
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge badge-custom badge-warning">
                                                                    <i class="bi bi-clock me-1"></i>Pending
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="filter-container">
            <h5 class="filter-title">Filter Users</h5>
            <form method="GET" class="d-flex">
                <div class="input-group" style="width: 300px;">
                    <input type="number" class="form-control" name="filter_user_id" placeholder="Filter by User ID" value="<?php echo $filter_user_id ? $filter_user_id : ''; ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <?php if ($filter_user_id): ?>
                        <a href="wallet_management.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>User Wallets
                </h5>
                <span class="badge bg-secondary"><?php echo count($users); ?> users</span>
            </div>
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <p class="mt-3 mb-0">No users found.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Wallet Balance</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-3">
                                                    <?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 1)); ?>
                                                </div>
                                                <div>
                                                    <div class="fw-medium"><?php echo htmlspecialchars($user['name']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="fw-bold text-success">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="wallet_management.php?user_id=<?php echo $user['id']; ?>" 
                                               class="btn btn-primary btn-action">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/admin_footer.php'; ?>