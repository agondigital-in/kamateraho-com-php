<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching user data: " . $e->getMessage();
    $user = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    $upi_id = trim($_POST['upi_id']);
    
    // Validation
    if ($amount < 200) {
        $error = "Minimum withdrawal amount is ₹200!";
    } elseif (empty($upi_id)) {
        $error = "UPI ID is required!";
    } elseif ($amount > $user['wallet_balance']) {
        $error = "Insufficient wallet balance!";
    } else {
        try {
            // Handle screenshot upload (now optional)
            $screenshot = '';
            if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK && $_FILES['screenshot']['size'] > 0) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_name = 'withdraw_' . time() . '_' . basename($_FILES['screenshot']['name']);
                $target_file = $upload_dir . $file_name;
                
                // Allow certain file formats
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($imageFileType, $allowed_types)) {
                    if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $target_file)) {
                        $screenshot = $upload_dir . $file_name;
                    } else {
                        $error = "Sorry, there was an error uploading your screenshot.";
                    }
                } else {
                    $error = "Only JPG, JPEG, PNG & GIF files are allowed for screenshot.";
                }
            }
            
            if (!isset($error)) {
                // Begin transaction
                $pdo->beginTransaction();
                
                // Deduct amount from user's wallet immediately
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance - ? WHERE id = ?");
                $stmt->execute([$amount, $user_id]);
                
                // Insert withdraw request with 'pending' status
                $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $amount, $upi_id, $screenshot]);
                
                // Add entry to wallet history
                $description = "Withdrawal request submitted";
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, ?, 'debit', 'pending', ?)");
                $stmt->execute([$user_id, $amount, $description]);
                
                // Commit transaction
                $pdo->commit();
                
                // Redirect to dashboard with success message
                header("Location: dashboard.php?withdraw_success=1");
                exit;
            }
        } catch(PDOException $e) {
            // Rollback transaction on error
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
            $error = "Error submitting withdraw request: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0"><i class="fas fa-money-bill-transfer me-2"></i>Withdraw Money</h3>
                            <div class="wallet-display d-flex align-items-center bg-white bg-opacity-25 px-3 py-2 rounded-pill">
                                <i class="fas fa-wallet me-2"></i>
                                <span class="balance-amount fw-bold">₹<?php echo number_format($user['wallet_balance'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="card h-100 border-primary border-2">
                                    <div class="card-body text-center">
                                        <i class="fas fa-info-circle fa-2x text-primary mb-3"></i>
                                        <h5>Minimum Withdrawal</h5>
                                        <p class="mb-0">₹200</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-success border-2">
                                    <div class="card-body text-center">
                                        <i class="fas fa-clock fa-2x text-success mb-3"></i>
                                        <h5>Processing Time</h5>
                                        <p class="mb-0">24-48 Hours</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="amount" class="form-label fw-bold">Withdrawal Amount (₹)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">₹</span>
                                    <input type="number" class="form-control form-control-lg" id="amount" name="amount" 
                                           min="200" step="0.01" 
                                           placeholder="Enter amount (minimum ₹200)" required>
                                </div>
                                <div class="form-text">Available balance: ₹<?php echo number_format($user['wallet_balance'], 2); ?></div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="upi_id" class="form-label fw-bold">UPI ID</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-qrcode"></i></span>
                                    <input type="text" class="form-control form-control-lg" id="upi_id" name="upi_id" 
                                           placeholder="yourname@upi" required>
                                </div>
                                <div class="form-text">Enter your valid UPI ID for receiving funds</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="screenshot" class="form-label fw-bold">Upload Qr Code (Optional)</label>
                                <div class="input-group">
                                    <input type="file" class="form-control" id="screenshot" name="screenshot" 
                                           accept="image/*">
                                    <label class="input-group-text bg-primary text-white" for="screenshot">
                                        <i class="fas fa-upload"></i>
                                    </label>
                                </div>
                                <div class="form-text">Upload screenshot of UPI payment request (JPG, PNG, GIF) - Optional</div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg py-3">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Withdraw Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>How to Withdraw</h5>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li>Enter the amount you want to withdraw (minimum ₹200)</li>
                            <li>Provide your valid UPI ID</li>
                            <li>Upload Qr Code (Optional) (optional)</li>
                            <li>Submit your request - <strong>Amount will be deducted immediately from your wallet</strong></li>
                            <li>If approved, your withdrawal will be processed within 24-48 hours</li>
                            <li>If rejected, the amount will be refunded to your wallet</li>
                            <li>Check your wallet history for status updates</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>