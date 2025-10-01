<?php
$page_title = "Pending Withdraw Requests";
include '../config/db.php';
include 'includes/admin_layout.php'; // This includes auth check

// Fetch pending withdraw requests
try {
    $stmt = $pdo->query("SELECT wr.*, u.name, u.email, u.id as user_id FROM withdraw_requests wr 
                         JOIN users u ON wr.user_id = u.id 
                         WHERE wr.status = 'pending' 
                         ORDER BY wr.created_at DESC");
    $pending_withdraw_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching pending withdraw requests: " . $e->getMessage();
    $pending_withdraw_requests = [];
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Pending Withdraw Requests</h2>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5>All Pending Withdraw Requests</h5>
        </div>
        <div class="card-body">
            <?php if (empty($pending_withdraw_requests)): ?>
                <p>No pending withdraw requests.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
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