<?php
$page_title = "Wallet Management";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Fetch all users with their wallet balances
try {
    $stmt = $pdo->query("SELECT id, name, email, phone, city, state, wallet_balance FROM users ORDER BY wallet_balance DESC");
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
if (isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    // Fetch user details
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, phone, city, state, wallet_balance FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $selected_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($selected_user) {
            // Fetch wallet history for this user
            $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $user_wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        $error = "Error fetching user data: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <h2>Wallet Management</h2>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="number">₹<?php echo number_format($total_balance, 2); ?></div>
                            <div class="label">Total Wallet Balance (All Users)</div>
                        </div>
                        <i class="bi bi-currency-rupee fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($selected_user): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><?php echo htmlspecialchars($selected_user['name']); ?>'s Wallet Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($selected_user['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($selected_user['phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Current Balance:</strong> <span class="fs-4 fw-bold text-success">₹<?php echo number_format($selected_user['wallet_balance'], 2); ?></span></p>
                                <p><strong>City:</strong> <?php echo htmlspecialchars($selected_user['city']); ?></p>
                                <p><strong>State:</strong> <?php echo htmlspecialchars($selected_user['state']); ?></p>
                            </div>
                        </div>
                        
                        <h6>Wallet History</h6>
                        <?php if (empty($user_wallet_history)): ?>
                            <p>No wallet history found for this user.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
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
                                                <td><?php echo htmlspecialchars($history['description']); ?></td>
                                                <td>₹<?php echo number_format($history['amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $history['type'] === 'credit' ? 'success' : 'danger'; ?>">
                                                        <?php echo ucfirst($history['type']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $history['status'] === 'approved' ? 'success' : 
                                                           ($history['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                                        <?php echo ucfirst($history['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        
                        <a href="wallet_management.php" class="btn btn-secondary">Back to All Users</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!$selected_user): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>All Users Wallet Balances</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <p>No users found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Wallet Balance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td>₹<?php echo number_format($user['wallet_balance'], 2); ?></td>
                                                <td>
                                                    <a href="wallet_management.php?user_id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-primary btn-sm">View Details</a>
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

<?php include 'includes/admin_footer.php'; ?>