<?php
// Debug script to check purchase request rejection functionality
include 'config/db.php';
include 'admin/auth.php'; // Admin authentication

// Only allow access to admin users
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin/login.php');
    exit;
}

$message = '';
$error = '';

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'debug') {
        try {
            // Get withdraw request record
            $stmt = $pdo->prepare("SELECT * FROM withdraw_requests WHERE id = ?");
            $stmt->execute([$id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($request) {
                $message .= "<h3>Withdraw Request Details:</h3>";
                $message .= "<pre>" . print_r($request, true) . "</pre>";
                
                // Check if this is a purchase request
                $is_purchase = (strpos($request['upi_id'], 'purchase@') === 0);
                $message .= "<p>Is Purchase Request: " . ($is_purchase ? 'Yes' : 'No') . "</p>";
                
                // Check wallet history entries for this user and amount
                $stmt = $pdo->prepare("SELECT * FROM wallet_history WHERE user_id = ? AND amount = ? ORDER BY id DESC");
                $stmt->execute([$request['user_id'], $request['amount']]);
                $wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $message .= "<h3>Wallet History Entries:</h3>";
                $message .= "<pre>" . print_r($wallet_history, true) . "</pre>";
                
                // Check user's current wallet balance
                $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                $stmt->execute([$request['user_id']]);
                $wallet_balance = $stmt->fetchColumn();
                
                $message .= "<h3>User Wallet Balance:</h3>";
                $message .= "<p>₹" . number_format($wallet_balance, 2) . "</p>";
            } else {
                $error = "Withdraw request not found.";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Purchase Request Rejection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <h1 class="mb-4">Debug Purchase Request Rejection</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="id" class="form-label">Withdraw Request ID:</label>
                            <input type="number" class="form-control" id="id" name="id" required>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" name="action" value="debug" class="btn btn-primary btn-lg">
                                Debug Request
                            </button>
                        </div>
                    </div>
                </form>
                
                <div class="mt-4">
                    <h3>How to Use:</h3>
                    <ol>
                        <li>Find a pending purchase request in the admin panel</li>
                        <li>Copy the request ID from the URL or database</li>
                        <li>Enter the ID above and click "Debug Request"</li>
                        <li>This will show details about the request and related wallet history entries</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</body>
</html>