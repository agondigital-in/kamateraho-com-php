<?php
$page_title = "Pending Wallet Approvals";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Fetch pending wallet history
try {
    $stmt = $pdo->query("SELECT wh.*, u.name, u.email FROM wallet_history wh 
                         JOIN users u ON wh.user_id = u.id 
                         WHERE wh.status = 'pending' 
                         ORDER BY wh.created_at DESC");
    $pending_wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching pending wallet history: " . $e->getMessage();
    $pending_wallet_history = [];
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Pending Wallet Approvals</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5>All Pending Wallet Approvals</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pending_wallet_history)): ?>
                <p>No pending wallet approvals.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
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
                                        </div>
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

<?php include 'includes/admin_footer.php'; ?>