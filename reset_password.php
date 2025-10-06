<?php
session_start();
include 'config/db.php';
include 'password_reset_tokens.php';

// Check if a token is provided
$token = $_GET['token'] ?? '';
$email = '';

if (!empty($token)) {
    $email = PasswordResetTokens::validateToken($token);
}

$valid_request = !empty($email);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Check if we have a valid email from the token
    if (empty($email)) {
        $error = "Invalid or expired reset link. Please request a new password reset.";
    } elseif (empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required!";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "New password and confirm password do not match!";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters long!";
    } elseif ($pdo === null) {
        $error = "Database connection failed. Please contact the administrator.";
    } else {
        try {
            // Update to new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $updateStmt->execute([$hashedPassword, $email]);
            
            // Remove the token so it can't be used again
            PasswordResetTokens::removeToken($token);
            
            $message = "Password updated successfully! You can now <a href='login.php'>login</a> with your new password.";
        } catch(PDOException $e) {
            $error = "Failed to update password. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - KamateRaho</title>
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
            display: flex;
            align-items: center;
            padding: 20px 0;
        }
        
        .reset-password-container {
            max-width: 500px;
            margin: 0 auto;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
            background: white;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .reset-password-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .reset-password-header h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .reset-password-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-text);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
        }
        
        .input-icon input {
            padding-left: 45px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .input-icon input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(26, 42, 108, 0.1);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(26, 42, 108, 0.3);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 25px;
        }
        
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px 0;
            }
            
            .reset-password-body {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-password-container">
            <div class="reset-password-header">
                <h2>Reset Password</h2>
                <p>Change your password to something you'll remember</p>
            </div>
            
            <div class="reset-password-body">
                <?php if (!$valid_request): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>Invalid or expired reset link. Please <a href="forgot_password.php">request a new password reset</a>.
                    </div>
                    <div class="form-footer">
                        <p><a href="login.php">Back to Login</a></p>
                    </div>
                <?php else: ?>
                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($message)): ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-reset">Update Password</button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="form-footer">
                        <p><a href="login.php">Back to Login</a></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>