<?php
include 'config/db.php';
include 'includes/email_verification.php';

// Start session for CAPTCHA and verification
session_start();

// Only generate a new CAPTCHA if it doesn't exist or we're not processing a form submission
if (!isset($_SESSION['captcha']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['captcha'] = rand(1000, 9999);
}

// Check for referral code in URL
$referrer_id = null;
$referral_source = null;

// Check for referrer ID
if (isset($_GET['ref']) && is_numeric($_GET['ref'])) {
    $referrer_id = intval($_GET['ref']);
    
    // Validate that the referrer exists
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$referrer_id]);
        if (!$stmt->fetch()) {
            $referrer_id = null; // Invalid referrer
        }
    } catch (PDOException $e) {
        $referrer_id = null; // Error checking referrer
    }
}

// Check for referral source
if (isset($_GET['source'])) {
    $allowed_sources = ['youtube', 'facebook', 'instagram', 'twitter', 'other'];
    if (in_array(strtolower($_GET['source']), $allowed_sources)) {
        $referral_source = strtolower($_GET['source']);
    }
}

// Handle form submissions
$step = 1; // 1 = email entry, 2 = verification, 3 = registration form
$email = '';
$verification_sent = false;
$verification_error = '';
$registration_error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: Email entry
    if (isset($_POST['enter_email'])) {
        $email = trim($_POST['email']);
        
        // Validate email
        if (empty($email)) {
            $error = "Email is required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {
            // Check if email already exists
            try {
                $stmt = $pdo->prepare("SELECT id, email_verified FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    if ($user['email_verified'] == 1) {
                        $error = "Email already registered!";
                    } else {
                        // User exists but email not verified, allow re-verification
                        $step = 2;
                        $verification_sent = true;
                        
                        // Check if we already have a valid verification code
                        if (!hasVerificationCode($pdo, $email)) {
                            // Generate and send verification code
                            $code = generateVerificationCode();
                            if (storeVerificationCode($pdo, $email, $code)) {
                                // Send verification email
                                sendVerificationEmail($email, $code);
                            }
                        }
                    }
                } else {
                    // New email, proceed to verification
                    $step = 2;
                    $verification_sent = true;
                    
                    // Generate and send verification code
                    $code = generateVerificationCode();
                    if (storeVerificationCode($pdo, $email, $code)) {
                        // Send verification email
                        sendVerificationEmail($email, $code);
                    }
                }
            } catch (PDOException $e) {
                $error = "Database error. Please try again.";
            }
        }
    }
    // Step 2: Verification code submission
    elseif (isset($_POST['verify_code'])) {
        $email = trim($_POST['email']);
        $code = trim($_POST['verification_code']);
        
        if (empty($code)) {
            $verification_error = "Verification code is required!";
        } elseif (strlen($code) != 4 || !is_numeric($code)) {
            $verification_error = "Invalid verification code format!";
        } else {
            // Verify the code
            if (verifyCode($pdo, $email, $code)) {
                // Code is valid, proceed to registration form
                $step = 3;
                $_SESSION['verified_email'] = $email;
            } else {
                $verification_error = "Invalid or expired verification code!";
            }
        }
    }
    // Step 3: Registration form submission
    elseif (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_SESSION['verified_email'] ?? $_POST['email'];
        $phone = $_POST['phone'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Get referrer ID from form if not in URL
        if (!$referrer_id && isset($_POST['referrer_id']) && is_numeric($_POST['referrer_id'])) {
            $temp_referrer_id = intval($_POST['referrer_id']);
            // Validate that the referrer exists
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
                $stmt->execute([$temp_referrer_id]);
                if ($stmt->fetch()) {
                    $referrer_id = $temp_referrer_id;
                }
            } catch (PDOException $e) {
                // Invalid referrer
            }
        }
        
        // Get referral source from form if not in URL
        if (!$referral_source && isset($_POST['referral_source'])) {
            $allowed_sources = ['youtube', 'facebook', 'instagram', 'twitter', 'other'];
            if (in_array(strtolower($_POST['referral_source']), $allowed_sources)) {
                $referral_source = strtolower($_POST['referral_source']);
            }
        }
        
        // Validation
        if (empty($name) || empty($email) || empty($phone) || empty($city) || empty($state) || empty($password) || empty($confirm_password)) {
            $registration_error = "All fields are required!";
        } elseif (isset($_POST['captcha']) && $_POST['captcha'] != $_SESSION['captcha']) {
            $registration_error = "Invalid CAPTCHA code!";
        } elseif ($password !== $confirm_password) {
            $registration_error = "Passwords do not match!";
        } elseif (strlen($password) < 6) {
            $registration_error = "Password must be at least 6 characters long!";
        } elseif ($pdo === null) {
            $registration_error = "Database connection failed. Please contact the administrator.";
            // Add detailed error information for debugging (remove in production)
            if (isset($db_error)) {
                $registration_error .= " Error details: " . $db_error;
            }
        } else {
            try {
                // Begin transaction
                $pdo->beginTransaction();
                
                // Hash password and insert user with referral source
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, city, state, password, wallet_balance, referral_source, email_verified) VALUES (?, ?, ?, ?, ?, ?, 50.00, ?, TRUE)");
                $stmt->execute([$name, $email, $phone, $city, $state, $hashed_password, $referral_source]);
                
                // Get the inserted user ID
                $user_id = $pdo->lastInsertId();
                
                // Add welcome bonus to wallet history (auto-approved)
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, 50.00, 'credit', 'approved', 'Welcome Bonus')");
                $stmt->execute([$user_id]);
                
                // Handle referral bonus if applicable
                if ($referrer_id) {
                    // Credit referrer's wallet with ₹3
                    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + 3.00 WHERE id = ?");
                    $stmt->execute([$referrer_id]);
                    
                    // Add referral bonus to referrer's wallet history
                    $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, 3.00, 'credit', 'approved', 'Referral Bonus for user ID: " . $user_id . "')");
                    $stmt->execute([$referrer_id]);
                }
                
                // Commit transaction
                $pdo->commit();
                
                $success = "Registration successful! You've received ₹50 welcome bonus.";
                // Redirect to login after 3 seconds
                header("refresh:3;url=login.php");
                
                // Clear session data
                unset($_SESSION['verified_email']);
            } catch(PDOException $e) {
                // Rollback transaction on error
                if ($pdo->inTransaction()) {
                    $pdo->rollback();
                }
                $registration_error = "Registration failed: " . $e->getMessage();
            }
        }
    }
    // Resend verification code
    elseif (isset($_POST['resend_code'])) {
        $email = trim($_POST['email']);
        $step = 2;
        $verification_sent = true;
        
        // Generate and send verification code
        $code = generateVerificationCode();
        if (storeVerificationCode($pdo, $email, $code)) {
            // Send verification email
            sendVerificationEmail($email, $code);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KamateRaho</title>
    <link rel="icon" href="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-RMM38DLZLM');
    </script>
    <style>
        :root {
            --primary-dark: #1a1a2e;
            --primary-medium: #16213e;
            --accent-teal: #0f3460;
            --accent-pink: #e94560;
            --light-text: #f1f1f1;
            --card-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-medium));
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
            margin: 0;
            color: var(--light-text);
        }
        
        .register-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            border-radius: 25px;
            overflow: hidden;
            background: var(--card-bg);
            animation: fadeIn 0.8s ease-out;
            box-shadow: var(--shadow);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .register-form-container {
            flex: 1;
            min-width: 300px;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }
        
        .register-banner {
            flex: 1;
            min-width: 300px;
            background: linear-gradient(135deg, var(--accent-teal), var(--accent-pink));
            color: white;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-banner::before {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            top: -50px;
            right: -50px;
        }
        
        .register-banner::after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            bottom: -30px;
            left: -30px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: var(--accent-teal);
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .register-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .banner-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .banner-header h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            font-weight: 700;
        }
        
        .banner-header p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            opacity: 0.9;
        }
        
        .banner-image {
            text-align: center;
            margin: 20px 0;
        }
        
        .banner-image img {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .unique-features {
            position: relative;
            z-index: 2;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.25);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: white;
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 15px;
            color: white;
            font-weight: 600;
        }
        
        .feature-card p {
            font-size: 1rem;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--accent-teal);
        }
        
        .input-icon input {
            padding-left: 45px;
            border: 2px solid #e1e5ee;
            border-radius: 12px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            height: 50px;
            font-size: 1rem;
        }
        
        .input-icon input:focus {
            border-color: var(--accent-teal);
            box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
        }
        
        .btn-register {
            background: linear-gradient(135deg, var(--accent-teal), var(--accent-pink));
            color: white;
            border: none;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 10px;
            box-shadow: 0 5px 15px rgba(233, 69, 96, 0.3);
        }
        
        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(233, 69, 96, 0.4);
        }
        
        .form-footer {
    text-align: center;
    margin-top: 25px;
}

.form-footer p {
    color: #000; /* text black */
}

        
        .form-footer a {
            color: var(--accent-teal);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }
        
        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #777;
            cursor: pointer;
        }
        
        .captcha-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e1e5ee;
        }
        
        .captcha-text {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 8px;
            color: var(--accent-pink);
            text-align: center;
            flex-grow: 1;
        }
        
        .verification-code-input {
            font-size: 1.8rem;
            letter-spacing: 10px;
            text-align: center;
            height: 60px;
        }
        
        .resend-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .resend-link a {
            color: var(--accent-teal);
            text-decoration: none;
            font-weight: 500;
        }
        
        .resend-link a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 992px) {
            .register-banner {
                padding: 40px 30px;
            }
            
            .register-form-container {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px 0;
            }
            
            .register-container {
                flex-direction: column-reverse;
                border-radius: 20px;
            }
            
            .register-banner {
                padding: 40px 25px;
            }
            
            .register-form-container {
                padding: 30px 25px;
            }
            
            .register-header h1 {
                font-size: 1.8rem;
            }
            
            .banner-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="register-container">
            <div class="register-form-container">
                <div class="register-header">
                    <h1>Create Account</h1>
                    <p>Join our community and start earning today</p>
                </div>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Step 1: Email Entry -->
                <?php if ($step == 1): ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="enter_email" class="btn-register">Continue</button>
                </form>
                <?php endif; ?>
                
                <!-- Step 2: Email Verification -->
                <?php if ($step == 2): ?>
                <form method="POST">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    
                    <div class="form-group">
                        <label>Verification Code</label>
                        <p>We've sent a 4-digit verification code to <strong><?php echo htmlspecialchars($email); ?></strong></p>
                        <div class="input-icon">
                            <i class="fas fa-key"></i>
                            <input type="text" class="form-control verification-code-input" name="verification_code" placeholder="0 0 0 0" maxlength="4" required>
                        </div>
                        <?php if (!empty($verification_error)): ?>
                            <div class="alert alert-danger mt-2"><?php echo $verification_error; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" name="verify_code" class="btn-register">Verify Email</button>
                    
                    <div class="resend-link">
                        <p>Didn't receive the code? <button type="submit" name="resend_code" class="btn btn-link p-0">Resend Code</button></p>
                    </div>
                </form>
                <?php if ($verification_sent): ?>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>Verification code sent to your email. Please check your inbox (and spam folder).
                    </div>
                <?php endif; ?>
                <?php endif; ?>
                
                <!-- Step 3: Registration Form -->
                <?php if ($step == 3): ?>
                <form method="POST" class="needs-validation" novalidate>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['verified_email'] ?? $email); ?>">
                    
                    <?php if (!empty($registration_error)): ?>
                        <div class="alert alert-danger"><?php echo $registration_error; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email_display">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email_display" value="<?php echo htmlspecialchars($_SESSION['verified_email'] ?? $email); ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City</label>
                        <div class="input-icon">
                            <i class="fas fa-city"></i>
                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter your city" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="state">State</label>
                        <div class="input-icon">
                            <i class="fas fa-map-marker-alt"></i>
                            <input type="text" class="form-control" id="state" name="state" placeholder="Enter your state" required>
                        </div>
                    </div>
                    
                    <?php if ($referrer_id): ?>
                        <div class="form-group">
                            <label>Referred By</label>
                            <div class="input-icon">
                                <i class="fas fa-users"></i>
                                <input type="text" class="form-control" value="User ID: <?php echo $referrer_id; ?>" disabled>
                                <input type="hidden" name="referrer_id" value="<?php echo $referrer_id; ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($referral_source): ?>
                        <input type="hidden" name="referral_source" value="<?php echo htmlspecialchars($referral_source); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Create a strong password" required>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Password must be at least 6 characters long.</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="input-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            <button type="button" class="password-toggle" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="captcha">CAPTCHA</label>
                        <div class="input-icon">
                            <i class="fas fa-shield-alt"></i>
                            <input type="text" class="form-control" id="captcha" name="captcha" placeholder="Enter the code shown below" required>
                        </div>
                        <div class="captcha-container mt-2">
                            <div class="captcha-code">
                                <span class="captcha-text"><?php echo $_SESSION['captcha']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" name="register" class="btn-register">Create Account</button>
                </form>
                <?php endif; ?>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
            
            <div class="register-banner">
                <div class="banner-image">
                    <img src="https://res.cloudinary.com/dep67o63b/image/upload/v1763466833/WhatsApp_Image_2025-11-17_at_17.03.54_d2782913_zsszfi.jpg" alt="Registration Banner">
                </div>
                
             
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
            const password = document.querySelector('#password');
            const confirm_password = document.querySelector('#confirm_password');
            
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
            
            if (toggleConfirmPassword) {
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirm_password.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirm_password.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }
            
            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
    
</body>
</html>