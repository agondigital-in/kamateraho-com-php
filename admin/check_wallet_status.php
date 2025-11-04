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
        margin-bottom: 20px;
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
        margin-bottom: 20px;
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
        margin-bottom: 20px;
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
    
    .btn-action {
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: 5px;
    }
    
    .btn-success {
        background: linear-gradient(135deg, #20c997, #1aa179);
        border: none;
    }
    
    .btn-danger {
        background: linear-gradient(135deg, #dc3545, #bd2130);
        border: none;
    }
    
    .back-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        box-shadow: 0 4px 6px rgba(111, 66, 193, 0.2);
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(111, 66, 193, 0.3);
    }
    
    /* Responsive styles */
    @media (max-width: 992px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .stats-box {
            min-width: 200px;
        }
        
        .stats-number {
            font-size: 1.5rem;
        }
    }
    
    @media (max-width: 768px) {
        .wallet-header {
            padding: 15px;
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
        
        .card-header {
            padding: 12px 15px;
        }
        
        .card-title {
            font-size: 1.1rem;
        }
        
        .table th, .table td {
            padding: 10px 8px;
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 576px) {
        .wallet-header {
            padding: 12px;
        }
        
        .page-title {
            font-size: 1.1rem;
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
        
        .btn-action {
            padding: 4px 8px;
            font-size: 0.7rem;
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
        
        .btn-action {
            font-size: 0.65rem;
        }
    }
</style>

<div class="container-fluid">
    <div class="wallet-header">
        <h1 class="page-title">Wallet and Withdrawal Status</h1>
    </div>
    
    <!-- Stats Section -->
    <div class="stats-container">
        <div class="stats-box">
            <div class="stats-number">₹<?php echo number_format($total_balance, 2); ?></div>
            <div class="stats-label">Total Wallet Balance (All Users)</div>
        </div>
        <div class="stats-box">
            <div class="stats-number"><?php echo count($pending_requests); ?></div>
            <div class="stats-label">Pending Withdrawal Requests</div>
        </div>
    </div>
    
    <!-- Pending Withdrawal Requests -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Pending Withdrawal Requests</h5>
            <span class="badge bg-secondary"><?php echo count($pending_requests); ?> requests</span>
        </div>
        <div class="card-body">
            <?php if (!empty($pending_requests)): ?>
                <div class="table-container">
                    <table class="table table-hover">
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
                                        <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=approve" class="btn btn-success btn-action">Approve</a>
                                        <a href="approve_withdraw.php?id=<?php echo $request['id']; ?>&action=reject" class="btn btn-danger btn-action">Reject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="bi bi-cash-stack text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No pending withdrawal requests.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Wallet History -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Recent Wallet History</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($wallet_history)): ?>
                <div class="table-container">
                    <table class="table table-hover">
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
                                        <span class="badge badge-custom <?php echo ($history['type'] === 'credit' ? 'badge-success' : 'badge-danger'); ?>">
                                            <?php echo ucfirst($history['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-custom <?php 
                                            echo ($history['status'] === 'approved' ? 'badge-success' : 
                                            ($history['status'] === 'rejected' ? 'badge-danger' : 'badge-warning')); ?>">
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
                <div class="text-center py-4">
                    <i class="bi bi-wallet2 text-muted" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No wallet history found.</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="index.php" class="back-btn">
                    <i class="bi bi-arrow-left-circle me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>