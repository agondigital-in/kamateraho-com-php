<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT name, wallet_balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: logout.php');
    exit();
}

// Fetch referral history (users referred by this user)
$stmt = $pdo->prepare("SELECT u.id, u.name, u.email, u.created_at FROM users u WHERE u.referral_code IN (SELECT CONCAT('REF', id) FROM users WHERE id = ?)");
$stmt->execute([$_SESSION['user_id']]);
$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch wallet history related to referrals
$stmt = $pdo->prepare("SELECT amount, description, created_at FROM wallet_history WHERE user_id = ? AND description LIKE '%Referral%' ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$wallet_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral History - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #f7b733;
            --accent-color: #ff6e7f;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --light-text: #fff;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .wallet-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .section-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .referral-card {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--accent-color);
        }
        
        .transaction-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        
        .transaction-item:last-child {
            border-bottom: none;
        }
        
        .amount-positive {
            color: #28a745;
            font-weight: bold;
        }
        
        .amount-negative {
            color: #dc3545;
            font-weight: bold;
        }
        
        .referral-link-box {
            background: rgba(26, 42, 108, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .referral-link {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            font-family: monospace;
            word-break: break-all;
            margin-bottom: 15px;
        }
        
        .btn-copy {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-copy:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.3);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container dashboard-container">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Referral Dashboard</h1>
            </div>
        </div>
        
        <!-- Wallet Summary -->
        <div class="row">
            <div class="col-12">
                <div class="wallet-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                            <p class="mb-0">Share your referral link and earn ₹3 for each friend who joins</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <h3>Current Balance</h3>
                            <h1 class="mb-0">₹<?php echo number_format($user['wallet_balance'], 2); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Referral Link Section -->
        <div class="row">
            <div class="col-12">
                <div class="section-card">
                    <h3 class="section-title">Your Referral Link</h3>
                    <div class="referral-link-box">
                        <?php
                        $base_url = "https://kamateraho.com/";
                        $referral_link = $base_url . "register.php?ref=" . $_SESSION['user_id'];
                        ?>
                        <div class="referral-link" id="referralLink"><?php echo $referral_link; ?></div>
                        <button class="btn-copy" onclick="copyReferralLink()">
                            <i class="fas fa-copy me-2"></i>Copy Referral Link
                        </button>
                    </div>
                    <p class="mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Share this link with your friends. When they register using your link, you'll earn ₹3 instantly!
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Referral History -->
        <div class="row">
            <div class="col-lg-6">
                <div class="section-card">
                    <h3 class="section-title">Friends You've Referred</h3>
                    <?php if (count($referrals) > 0): ?>
                        <?php foreach ($referrals as $referral): ?>
                            <div class="referral-card">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($referral['name']); ?></h5>
                                        <p class="mb-0 text-muted"><?php echo htmlspecialchars($referral['email']); ?></p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0 text-muted">Joined</p>
                                        <p class="mb-0"><?php echo date('M d, Y', strtotime($referral['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">You haven't referred any friends yet. Share your referral link to start earning!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="section-card">
                    <h3 class="section-title">Referral Earnings</h3>
                    <?php if (count($wallet_history) > 0): ?>
                        <?php foreach ($wallet_history as $transaction): ?>
                            <div class="transaction-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($transaction['description']); ?></p>
                                        <p class="mb-0 text-muted small"><?php echo date('M d, Y g:i A', strtotime($transaction['created_at'])); ?></p>
                                    </div>
                                    <div>
                                        <p class="mb-0 amount-positive">+₹<?php echo number_format($transaction['amount'], 2); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No referral earnings yet. Refer friends to start earning!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyReferralLink() {
            var referralLink = document.getElementById("referralLink");
            navigator.clipboard.writeText(referralLink.innerText).then(function() {
                // Show a temporary message
                var originalText = document.querySelector('.btn-copy').innerHTML;
                document.querySelector('.btn-copy').innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                setTimeout(function() {
                    document.querySelector('.btn-copy').innerHTML = originalText;
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
                // Fallback: show alert with link
                prompt("Copy this referral link:", referralLink.innerText);
            });
        }
    </script>
</body>
</html>