<?php
$page_title = "Wallet Status";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

try {
    // Get total wallet balance across all users
    $stmt = $pdo->query("SELECT SUM(wallet_balance) as total_balance FROM users");
    $total_balance = $stmt->fetch(PDO::FETCH_ASSOC)['total_balance'] ?? 0;
    
    // Get pending withdrawal requests
    $stmt = $pdo->query("SELECT wr.*, u.name, u.email, u.wallet_balance 
                         FROM withdraw_requests wr 
                         JOIN users u ON wr.user_id = u.id 
                         WHERE wr.status = 'pending' 
                         ORDER BY wr.created_at DESC");
    $pending_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent wallet history
    $stmt = $pdo->query("SELECT wh.*, u.name, u.email 
                         FROM wallet_history wh 
                         JOIN users u ON wh.user_id = u.id 
                         ORDER BY wh.created_at DESC 
                         LIMIT 10");
    $wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
    $total_balance = 0;
    $pending_requests = [];
    $wallet_history = [];
}
?>

<div class="container-fluid">
    <h2>Wallet and Withdrawal Status</h2>
    
    <!-- Pending Withdrawal Requests (Moved to top) -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Pending Withdrawal Requests (<?php echo count($pending_requests); ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($pending_requests)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Wallet Balance</th>
                                <th>Withdrawal Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['email']); ?></td>
                                    <td>₹<?php echo number_format($request['wallet_balance'], 2); ?></td>
                                    <td>₹<?php echo number_format($request['amount'], 2); ?></td>
                                    <td><?php echo date('d M Y', strtotime($request['created_at'])); ?></td>
                                    <td>
                                        <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=approve" class="btn btn-success btn-sm">Approve</a>
                                        <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=reject" class="btn btn-danger btn-sm">Reject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No pending withdrawal requests.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Wallet Summary Card -->
    <div class="card mb-4 stats-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="number">₹<?php echo number_format($total_balance, 2); ?></div>
                    <div class="label">Total Wallet Balance (All Users)</div>
                </div>
                <i class="bi bi-wallet fs-1"></i>
            </div>
        </div>
    </div>
    
    <!-- Recent Wallet History -->
    <div class="card">
        <div class="card-header">
            <h5>Recent Wallet History</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($wallet_history)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wallet_history as $history): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($history['name']); ?></td>
                                    <td><?php echo htmlspecialchars($history['email']); ?></td>
                                    <td><?php echo htmlspecialchars($history['description']); ?></td>
                                    <td>₹<?php echo number_format($history['amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo ($history['type'] === 'credit' ? 'success' : 'danger'); ?>">
                                            <?php echo ucfirst($history['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo ($history['status'] === 'approved' ? 'success' : 
                                            ($history['status'] === 'rejected' ? 'danger' : 'warning')); ?>">
                                            <?php echo ucfirst($history['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($history['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No wallet history found.</p>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>