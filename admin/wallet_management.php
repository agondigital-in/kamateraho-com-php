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

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Wallet Management</h2>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary-emphasis fs-6">
                <i class="bi bi-wallet2 me-1"></i> ₹<?php echo number_format($total_balance, 2); ?>
            </span>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($selected_user): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 rounded-top-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-person-badge me-2 text-primary"></i>
                                <?php echo htmlspecialchars($selected_user['name']); ?>'s Wallet
                            </h5>
                            <a href="wallet_management.php" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Back to Users
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-4 mb-3 mb-lg-0">
                                <div class="card h-100 border-0 shadow-sm rounded-3">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                                <i class="bi bi-currency-rupee text-primary fs-2"></i>
                                            </div>
                                        </div>
                                        <h3 class="fw-bold text-success mb-1">₹<?php echo number_format($selected_user['wallet_balance'], 2); ?></h3>
                                        <p class="text-muted mb-0">Current Balance</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($selected_user['email']); ?>" readonly>
                                            <label>Email Address</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($selected_user['phone']); ?>" readonly>
                                            <label>Phone Number</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($selected_user['city']); ?>" readonly>
                                            <label>City</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($selected_user['state']); ?>" readonly>
                                            <label>State</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($referral_info)): ?>
                            <div class="card bg-light border-0 rounded-3 mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="bi bi-people me-2 text-primary"></i>Referral Information
                                    </h6>
                                    <?php foreach ($referral_info as $referrer): ?>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-3">
                                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($referrer['name']); ?></strong>
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
                        
                        <div class="card border-0 shadow-sm rounded-3">
                            <div class="card-header bg-white py-3 rounded-top-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="bi bi-clock-history me-2 text-primary"></i>Transaction History
                                    </h5>
                                    <!-- Wallet History Filters -->
                                    <form method="GET" class="d-flex gap-2">
                                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                        <div class="input-group input-group-sm" style="width: 200px;">
                                            <input type="text" class="form-control" name="wallet_filter" placeholder="Search..." value="<?php echo htmlspecialchars($wallet_filter); ?>">
                                            <button class="btn btn-outline-primary" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                        <select name="transaction_type" class="form-select form-select-sm" style="width: 180px;">
                                            <option value="">All Types</option>
                                            <option value="withdrawal" <?php echo $transaction_type_filter === 'withdrawal' ? 'selected' : ''; ?>>Withdrawal</option>
                                            <option value="spin_earn" <?php echo $transaction_type_filter === 'spin_earn' ? 'selected' : ''; ?>>Spin & Earn</option>
                                            <option value="referral" <?php echo $transaction_type_filter === 'referral' ? 'selected' : ''; ?>>Referral Bonus</option>
                                            <option value="welcome" <?php echo $transaction_type_filter === 'welcome' ? 'selected' : ''; ?>>Welcome Bonus</option>
                                        </select>
                                        <?php if (!empty($wallet_filter) || !empty($transaction_type_filter)): ?>
                                            <a href="wallet_management.php?user_id=<?php echo $user_id; ?>" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($user_wallet_history)): ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-wallet text-muted" style="font-size: 3rem;"></i>
                                        <p class="mt-3 mb-0">No transaction history found for this user.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3">Date</th>
                                                    <th scope="col" class="px-4 py-3">Description</th>
                                                    <th scope="col" class="px-4 py-3">Amount</th>
                                                    <th scope="col" class="px-4 py-3">Type</th>
                                                    <th scope="col" class="px-4 py-3">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($user_wallet_history as $history): ?>
                                                    <tr class="align-middle">
                                                        <td class="px-4 py-3">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-calendar me-2 text-muted"></i>
                                                                <?php echo date('d M Y', strtotime($history['created_at'])); ?>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3">
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
                                                        <td class="px-4 py-3">
                                                            <span class="fw-bold <?php echo $history['type'] === 'credit' ? 'text-success' : 'text-danger'; ?>">
                                                                <?php echo $history['type'] === 'credit' ? '+' : '-'; ?>₹<?php echo number_format($history['amount'], 2); ?>
                                                            </span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <?php if ($history['type'] === 'credit'): ?>
                                                                <span class="badge bg-success-subtle text-success-emphasis">
                                                                    <i class="bi bi-arrow-down-circle me-1"></i>Credit
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger-subtle text-danger-emphasis">
                                                                    <i class="bi bi-arrow-up-circle me-1"></i>Debit
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <?php if ($history['status'] === 'approved'): ?>
                                                                <span class="badge bg-success-subtle text-success-emphasis">
                                                                    <i class="bi bi-check-circle me-1"></i>Approved
                                                                </span>
                                                            <?php elseif ($history['status'] === 'rejected'): ?>
                                                                <span class="badge bg-danger-subtle text-danger-emphasis">
                                                                    <i class="bi bi-x-circle me-1"></i>Rejected
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning-subtle text-warning-emphasis">
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
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 rounded-top-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-people me-2 text-primary"></i>User Wallets
                            </h5>
                            <!-- User Filter Form -->
                            <form method="GET" class="d-flex">
                                <div class="input-group input-group-sm" style="width: 250px;">
                                    <input type="number" class="form-control" name="filter_user_id" placeholder="Filter by User ID" value="<?php echo $filter_user_id ? $filter_user_id : ''; ?>">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <?php if ($filter_user_id): ?>
                                        <a href="wallet_management.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($users)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 mb-0">No users found.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="px-4 py-3">User ID</th>
                                            <th scope="col" class="px-4 py-3">Name</th>
                                            <th scope="col" class="px-4 py-3">Email</th>
                                            <th scope="col" class="px-4 py-3">Wallet Balance</th>
                                            <th scope="col" class="px-4 py-3 text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr class="align-middle">
                                                <td class="px-4 py-3 fw-bold"><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                                <span class="text-primary fw-bold"><?php echo strtoupper(substr(htmlspecialchars($user['name']), 0, 1)); ?></span>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium"><?php echo htmlspecialchars($user['name']); ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-muted"><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td class="px-4 py-3">
                                                    <span class="fw-bold text-success">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
                                                </td>
                                                <td class="px-4 py-3 text-end">
                                                    <a href="wallet_management.php?user_id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-outline-primary btn-sm">
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
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Custom Styles -->
<style>
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    background-color: #fff;
}

.table > :not(caption) > * > * {
    padding: 0.75rem 1rem;
    border-bottom-width: 1px;
}

.table-hover > tbody > tr:hover {
    background-color: rgba(67, 97, 238, 0.05);
}

.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
    border-radius: 6px;
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.alert {
    border: none;
    border-radius: 8px;
}

.form-control, .form-select {
    border-radius: 6px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table th, .table td {
        padding: 0.6rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
    
    .card-header {
        padding: 0.75rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .table th, .table td {
        padding: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
    
    h2 {
        font-size: 1.3rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start !important;
    }
}

@media (max-width: 400px) {
    .table-responsive {
        font-size: 0.75rem;
    }
    
    .table th, .table td {
        padding: 0.4rem;
    }
    
    .btn-sm {
        padding: 0.15rem 0.3rem;
        font-size: 0.7rem;
    }
    
    h2 {
        font-size: 1.25rem;
    }
}
</style>