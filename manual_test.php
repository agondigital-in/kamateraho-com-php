<!DOCTYPE html>
<html>
<head>
    <title>Manual Wallet Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Manual Wallet Test</h2>
        
        <?php
        include 'config/db.php';
        
        if (isset($_POST['test_type'])) {
            $test_type = $_POST['test_type'];
            
            try {
                if ($test_type === 'check_users') {
                    echo "<div class='alert alert-info'>Checking users with wallet balances...</div>";
                    $stmt = $pdo->query("SELECT id, name, email, wallet_balance FROM users ORDER BY wallet_balance DESC");
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<table class='table'>";
                    echo "<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Wallet Balance</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($users as $user) {
                        echo "<tr>";
                        echo "<td>" . $user['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                        echo "<td>₹" . number_format($user['wallet_balance'], 2) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } elseif ($test_type === 'check_requests') {
                    echo "<div class='alert alert-info'>Checking withdrawal requests...</div>";
                    $stmt = $pdo->query("SELECT wr.*, u.name FROM withdraw_requests wr JOIN users u ON wr.user_id = u.id ORDER BY wr.created_at DESC");
                    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo "<table class='table'>";
                    echo "<thead><tr><th>ID</th><th>User</th><th>Amount</th><th>UPI ID</th><th>Status</th><th>Date</th></tr></thead>";
                    echo "<tbody>";
                    foreach ($requests as $request) {
                        echo "<tr>";
                        echo "<td>" . $request['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($request['name']) . "</td>";
                        echo "<td>₹" . number_format($request['amount'], 2) . "</td>";
                        echo "<td>" . htmlspecialchars($request['upi_id']) . "</td>";
                        echo "<td>" . ucfirst($request['status']) . "</td>";
                        echo "<td>" . date('d M Y', strtotime($request['created_at'])) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                } elseif ($test_type === 'deduct_test' && isset($_POST['user_id']) && isset($_POST['amount'])) {
                    $user_id = (int)$_POST['user_id'];
                    $amount = (float)$_POST['amount'];
                    
                    echo "<div class='alert alert-info'>Testing wallet deduction for user ID: $user_id, Amount: ₹" . number_format($amount, 2) . "</div>";
                    
                    // Get current balance
                    $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                    $stmt->execute([$user_id]);
                    $current_balance = $stmt->fetchColumn();
                    
                    echo "<p>Current balance: ₹" . number_format($current_balance, 2) . "</p>";
                    
                    // Deduct amount
                    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                    $result = $stmt->execute([$amount, $user_id]);
                    
                    if ($result) {
                        // Get new balance
                        $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
                        $stmt->execute([$user_id]);
                        $new_balance = $stmt->fetchColumn();
                        
                        echo "<p>New balance: ₹" . number_format($new_balance, 2) . "</p>";
                        echo "<p>Expected balance: ₹" . number_format($current_balance - $amount, 2) . "</p>";
                        
                        if (abs($new_balance - ($current_balance - $amount)) < 0.01) {
                            echo "<div class='alert alert-success'>Wallet deduction test PASSED!</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Wallet deduction test FAILED!</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Failed to execute deduction query.</div>";
                    }
                }
            } catch(PDOException $e) {
                echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
            }
        }
        ?>
        
        <div class="card mb-3">
            <div class="card-header">
                <h5>Test Options</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <select name="test_type" class="form-select">
                            <option value="check_users">Check All Users' Wallet Balances</option>
                            <option value="check_requests">Check All Withdrawal Requests</option>
                            <option value="deduct_test">Test Wallet Deduction</option>
                        </select>
                    </div>
                    
                    <div id="deduct_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">User ID</label>
                            <input type="number" class="form-control" name="user_id" id="user_id">
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount to Deduct</label>
                            <input type="number" step="0.01" class="form-control" name="amount" id="amount">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Run Test</button>
                </form>
            </div>
        </div>
        
        <a href="index.php" class="btn btn-secondary">Back to Home</a>
    </div>
    
    <script>
        document.querySelector('select[name="test_type"]').addEventListener('change', function() {
            if (this.value === 'deduct_test') {
                document.getElementById('deduct_fields').style.display = 'block';
            } else {
                document.getElementById('deduct_fields').style.display = 'none';
            }
        });
    </script>
</body>
</html>