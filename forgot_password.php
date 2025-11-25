<?php
session_start();
include 'config/db.php';

$message = '';
$error = '';

// Function to generate a random password
function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    if (empty($email)) {
        $error = "Please enter your email address!";
    } elseif ($pdo === null) {
        $error = "Database connection failed. Please contact the administrator.";
    } else {
        try {
            // Check if user exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // User found, generate a new password
                $newPassword = generateRandomPassword(10);
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                // Update the password in the database
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
                $updateStmt->execute([$hashedPassword, $email]);
                
                // Include the email template
                include_once 'admin/email_template.php';
                
                // Prepare email content
                $emailSubject = 'Your New Password for KamateRaho';
                $emailMessage = "Hello,\n\nYour password has been reset successfully. Here is your new password: $newPassword\n\nPlease login with this password and change it immediately from your profile page.\n\nIf you did not request this password reset, please contact our support team immediately.\n\nThank you for using KamateRaho!";
                
                // Generate HTML email content
                $htmlContent = getEmailTemplate($emailSubject, $emailMessage);
                
                // Prepare data for API call with HTML content
                $api_data = [
                    'email' => $email,
                    'Password' => $newPassword,  // Send the new plain text password
                    'html' => $htmlContent       // Send HTML content
                ];
                
                // API endpoint
                $url = 'https://mail2.kamateraho.com/send-password';
                
                // Authorization token
                $token = 'km_ritik_ritikyW8joeSZUHp6zgPm8Y8';
                
                // Initialize cURL
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token
                ]);
                
                // Execute the request
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($http_code === 200) {
                    $message = "A new password has been sent to your email address. Please check your email and login with the new password. After logging in, you can change it to something you'll remember by visiting the Reset Password page from your profile menu.";
                } else {
                    $error = "Failed to send password. Please try again later. (Error: " . $http_code . ")";
                }
            } else {
                // For security reasons, we'll show the same message whether the user exists or not
                $message = "If your email exists in our system, a new password has been sent to your email address.";
            }
        } catch(PDOException $e) {
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - KamateRaho</title>
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
        
        .forgot-password-container {
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
        
        .forgot-password-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .forgot-password-header h2 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .forgot-password-body {
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
            
            .forgot-password-body {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-password-container">
            <div class="forgot-password-header">
                <h2>Forgot Password</h2>
                <p>Enter your email to receive your password</p>
            </div>
            
            <div class="forgot-password-body">
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
                
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-reset">Send Password</button>
                </form>
                
                <div class="form-footer">
                    <p><a href="login.php">Back to Login</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>