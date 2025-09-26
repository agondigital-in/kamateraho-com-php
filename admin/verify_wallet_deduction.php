<?php
$page_title = "Verify Wallet Deduction";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Get a user with a pending withdrawal request
try {
    $stmt = $pdo->query("SELECT wr.*, u.name, u.email, u.wallet_balance 
                         FROM withdraw_requests wr 
                         JOIN users u ON wr.user_id = u.id 
                         WHERE wr.status = 'pending' 
                         LIMIT 1");
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if there are any users with wallet balances
    $stmt = $pdo->query("SELECT id, name, email, wallet_balance FROM users WHERE wallet_balance > 0 LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
    $request = null;
    $users = [];
}
?>

<div class="container-fluid">
    <h2>Wallet Deduction Verification</h2>
    
    <div class="card">
        <div class="card-header">
            <h5>Test Wallet Deduction</h5>
        </div>
        <div class="card-body">
            <?php if ($request): ?>
                <div class="alert alert-info">
                    <h5>Found pending withdrawal request:</h5>
                    <ul class="mb-0">
                        <li><strong>User:</strong> <?php echo htmlspecialchars($request['name']); ?> (<?php echo htmlspecialchars($request['email']); ?>)</li>
                        <li><strong>Current Wallet Balance:</strong> ₹<?php echo number_format($request['wallet_balance'], 2); ?></li>
                        <li><strong>Withdrawal Amount:</strong> ₹<?php echo number_format($request['amount'], 2); ?></li>
                        <li><strong>Request ID:</strong> <?php echo $request['id']; ?></li>
                    </ul>
                </div>
                
                <div class="d-grid gap-2 d-md-block">
                    <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=approve" class="btn btn-success">Approve Withdrawal (Original)</a>
                    <a href="approve_withdraw_simple.php?id=<?php echo $request['id']; ?>&action=approve" class="btn btn-primary">Approve Withdrawal (Simplified)</a>
                </div>
                
                <div class="mt-3">
                    <p class="text-muted">After approval, check if the user's wallet balance is reduced by ₹<?php echo number_format($request['amount'], 2); ?></p>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <p>No pending withdrawal requests found.</p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($users)): ?>
                <h5 class="mt-4">Users with wallet balances:</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Wallet Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>₹<?php echo number_format($user['wallet_balance'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info mt-3">
                    <p>No users with wallet balances found.</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-3">
                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>