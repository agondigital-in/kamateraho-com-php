<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Fetch user details
$user = null;
try {
    // Check if PDO connection is working
    if (!$pdo) {
        $error = "Database connection failed.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            header("Location: logout.php");
            exit;
        }
    }
} catch(PDOException $e) {
    $error = "Error fetching user data: " . $e->getMessage();
    $user = null;
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, city = ?, state = ? WHERE id = ?");
        $stmt->execute([$name, $email, $phone, $city, $state, $user_id]);
        
        // Update the user data in the session
        $user['name'] = $name;
        $user['email'] = $email;
        $user['phone'] = $phone;
        $user['city'] = $city;
        $user['state'] = $state;
        
        $success_message = "Profile updated successfully!";
    } catch(PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
    }
}

// Fetch user's wallet history
try {
    $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $wallet_history = [];
}

// Fetch user's withdraw requests
try {
    $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $withdraw_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $withdraw_requests = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>User Profile</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <?php if ($user): ?>
                            <h4><?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                        <?php else: ?>
                            <h4>Unknown User</h4>
                            <p class="text-muted">User data not available</p>
                        <?php endif; ?>
                        <div class="d-grid">
                            <a href="dashboard.php" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-wallet me-2"></i>Wallet Balance</h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if ($user): ?>
                            <h3 class="text-success">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></h3>
                        <?php else: ?>
                            <h3 class="text-success">₹0.00</h3>
                        <?php endif; ?>
                        <a href="withdraw.php" class="btn btn-success mt-2">
                            <i class="fas fa-money-bill-transfer me-2"></i>Withdraw Money
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Account Information</h5>
                        <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#editProfileForm" aria-expanded="false" aria-controls="editProfileForm">
                            <i class="fas fa-edit me-1"></i>Edit Profile
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="collapse" id="editProfileForm">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="user_id" class="form-label">User ID</label>
                                        <input type="text" class="form-control" id="user_id" value="<?php echo htmlspecialchars($user['id'] ?? ''); ?>" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="wallet_balance" class="form-label">Wallet Balance</label>
                                        <input type="text" class="form-control" id="wallet_balance" value="₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?>" disabled>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="created_at" class="form-label">Member Since</label>
                                        <input type="text" class="form-control" id="created_at" value="<?php echo date('F j, Y', strtotime($user['created_at'] ?? 'now')); ?>" disabled>
                                    </div>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-toggle="collapse" data-bs-target="#editProfileForm">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                            </form>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-id-card me-2"></i>User ID:</strong> <?php echo htmlspecialchars($user['id'] ?? 'N/A'); ?></p>
                                <p><strong><i class="fas fa-user me-2"></i>Full Name:</strong> <?php echo htmlspecialchars($user['name'] ?? 'N/A'); ?></p>
                                <p><strong><i class="fas fa-envelope me-2"></i>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                                <p><strong><i class="fas fa-phone me-2"></i>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-city me-2"></i>City:</strong> <?php echo htmlspecialchars($user['city'] ?? 'N/A'); ?></p>
                                <p><strong><i class="fas fa-map-marker-alt me-2"></i>State:</strong> <?php echo htmlspecialchars($user['state'] ?? 'N/A'); ?></p>
                                <p><strong><i class="fas fa-wallet me-2"></i>Wallet Balance:</strong> <span class="text-success">₹<?php echo number_format($user['wallet_balance'] ?? 0, 2); ?></span></p>
                                <p><strong><i class="fas fa-calendar-alt me-2"></i>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Wallet History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($wallet_history)): ?>
                            <p class="text-center">No wallet history found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
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
                                        <?php foreach ($wallet_history as $history): ?>
                                            <tr>
                                                <td><?php echo date('M j, Y', strtotime($history['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($history['description']); ?></td>
                                                <td>₹<?php echo number_format($history['amount'], 2); ?></td>
                                                <td>
                                                    <?php if ($history['type'] === 'credit'): ?>
                                                        <span class="badge bg-success">Credit</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Debit</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($history['status'] === 'approved'): ?>
                                                        <span class="badge bg-success">Approved</span>
                                                    <?php elseif ($history['status'] === 'pending'): ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Rejected</span>
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
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-transfer me-2"></i>Withdrawal Requests</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($withdraw_requests)): ?>
                            <p class="text-center">No withdrawal requests found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>UPI ID</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($withdraw_requests as $request): ?>
                                            <tr>
                                                <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                                                <td>₹<?php echo number_format($request['amount'], 2); ?></td>
                                                <td><?php echo htmlspecialchars($request['upi_id']); ?></td>
                                                <td>
                                                    <?php if ($request['status'] === 'approved'): ?>
                                                        <span class="badge bg-success">Approved</span>
                                                    <?php elseif ($request['status'] === 'pending'): ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Rejected</span>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>