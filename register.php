<?php
include 'config/db.php';

// Start session for CAPTCHA
session_start();
if (!isset($_SESSION['captcha'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
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
        $error = "All fields are required!";
    } elseif (isset($_POST['captcha']) && $_POST['captcha'] != $_SESSION['captcha']) {
        $error = "Invalid CAPTCHA code!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } elseif ($pdo === null) {
        $error = "Database connection failed. Please contact the administrator.";
        // Add detailed error information for debugging (remove in production)
        if (isset($db_error)) {
            $error .= " Error details: " . $db_error;
        }
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered!";
            } else {
                // Begin transaction
                $pdo->beginTransaction();
                
                // Hash password and insert user with referral source
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, city, state, password, wallet_balance, referral_source) VALUES (?, ?, ?, ?, ?, ?, 50.00, ?)");
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
                    
                    // Add referral bonus to new user's wallet (optional)
                    // Uncomment the following lines if you want to give a bonus to the new user too
                    /*
                    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + 3.00 WHERE id = ?");
                    $stmt->execute([$user_id]);
                    
                    $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, 3.00, 'credit', 'approved', 'Referral Bonus Received')");
                    $stmt->execute([$user_id]);
                    */
                }
                
                // Commit transaction
                $pdo->commit();
                
                $success = "Registration successful! You've received ₹50 welcome bonus.";
                // Redirect to login after 3 seconds
                header("refresh:3;url=login.php");
            }
        } catch(PDOException $e) {
            // Rollback transaction on error
            if ($pdo->inTransaction()) {
                $pdo->rollback();
            }
            $error = "Registration failed: " . $e->getMessage();
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
        
        .register-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
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
        
        .register-form-container {
            flex: 1;
            min-width: 300px;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-banner {
            flex: 1;
            min-width: 300px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .register-banner::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            top: -100px;
            right: -100px;
        }
        
        .register-banner::after {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            bottom: -80px;
            left: -80px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: #666;
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
        }
        
        .banner-header p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
        }
        
        .benefits {
            position: relative;
            z-index: 2;
        }
        
        .benefit-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }
        
        .benefit-card:hover {
            transform: translateY(-5px);
        }
        
        .benefit-card h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .benefit-card h3 i {
            margin-right: 10px;
            color: var(--accent-color);
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
        
        .btn-register {
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
        
        .btn-register:hover {
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
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #e1e5ee;
        }
        
        .captcha-text {
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 5px;
            color: var(--primary-color);
            text-align: center;
            flex-grow: 1;
        }
        
        .welcome-bonus {
            background: linear-gradient(135deg, var(--accent-color), #ff8e9e);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            color: white;
            box-shadow: 0 5px 15px rgba(255, 110, 127, 0.3);
        }
        
        .welcome-bonus h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        .bonus-amount {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        @media (max-width: 992px) {
            .register-banner {
                padding: 30px 20px;
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
            }
            
            .register-banner {
                padding: 30px;
            }
            
            .register-form-container {
                padding: 30px;
            }
        }
           /* Header Styles */
        header {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            width: auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .logo span {
            color: #ff6e7f;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            cursor: pointer;
            z-index: 1001;
        }

        .menu-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: #1a2a6c;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        /* Ensure menu is visible on desktop */
        @media (min-width: 769px) {
            nav ul {
                display: flex !important;
            }
        }

        /* Hide mobile menu by default */
        @media (max-width: 768px) {
            nav ul {
                display: none;
            }
            
            nav ul.active {
                display: flex;
            }
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        nav ul li a:hover {
            background: rgba(26, 42, 108, 0.1);
            color: #1a2a6c;
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 15px;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-login {
            background: transparent;
            color: #1a2a6c;
            border: 2px solid #1a2a6c;
        }

        .btn-login:hover {
            background: #1a2a6c;
            color: white;
        }

        .btn-register {
            background: #1a2a6c;
            color: white;
            border: 2px solid #1a2a6c;
        }

        .btn-register:hover {
            background: transparent;
            color: #1a2a6c;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            nav ul {
                position: fixed;
                top: 0;
                right: -100%;
                flex-direction: column;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
                width: 70%;
                height: 100vh;
                padding: 80px 20px 20px;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                margin: 0;
                border-left: 1px solid #d1d1d1;
                display: none; /* Hide by default on mobile */
            }

            nav ul.active {
                right: 0;
                display: flex !important; /* Show when active */
            }

            nav ul li {
                margin: 15px 0;
                text-align: center;
            }

            nav ul li a {
                display: block;
                padding: 15px;
                font-size: 1.2rem;
                color: #1a2a6c;
            }

            .auth-buttons {
                position: absolute;
                top: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
       <!-- <header>
          <div class="logo">
            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" alt="KamateRaho Logo" style="height: 65px; width: 250px;">
        </div>
        
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <nav>
          <ul id="navMenu">
                <li><a href="/">Home</a></li>
                <li><a href="/kamateraho/index.php">How It Works</a></li>
                <li><a href="/kamateraho/index.php">Testimonials</a></li>
                <li><a href="/kamateraho/index.php">Withdrawals</a></li>
                <li><a href="/kamateraho/contact.php">Contact</a></li>
                <li><a href="/kamateraho/blog/index.php">Blog</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header> -->
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Animate hamburger icon
                    const spans = menuToggle.querySelectorAll('span');
                    if (navMenu.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const navLinks = document.querySelectorAll('nav ul li a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                        const spans = menuToggle.querySelectorAll('span');
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    });
                });
            }
        });
    </script>
    <div class="container-fluid">
        <div class="register-container">
            <div class="register-form-container">
                <div class="register-header">
                    <h1>Create Account</h1>
                    <p>Join our community and start earning today</p>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email address" required>
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
                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="refreshCaptcha">Refresh</button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-register">Create Account</button>
                </form>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
            
            <div class="register-banner">
                <div class="banner-header">
                    <h2>Join KamateRaho Today!</h2>
                    <p>Create your account and start earning from the comfort of your home</p>
                </div>
                
                <div class="welcome-bonus">
                    <h3>Welcome Bonus</h3>
                    <div class="bonus-amount">₹50</div>
                    <p>Get ₹50 instantly after registration</p>
                </div>
                
                <div class="benefits">
                    <div class="benefit-card">
                        <h3><i class="fas fa-rupee-sign"></i> Earn Daily</h3>
                        <p>Participate in offers and earn up to ₹500 every day with just your smartphone.</p>
                    </div>
                    
                    <div class="benefit-card">
                        <h3><i class="fas fa-users"></i> Invite Friends</h3>
                        <p>Get ₹3.00 for each friend who joins and completes their first offer.</p>
                    </div>
                    
                    <div class="benefit-card">
                        <h3><i class="fas fa-bolt"></i> Instant Withdrawal</h3>
                        <p>Withdraw your earnings via Paytm, PhonePe or GooglePay with no hidden charges.</p>
                    </div>
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
            
            // CAPTCHA refresh
            const refreshCaptchaBtn = document.getElementById('refreshCaptcha');
            if (refreshCaptchaBtn) {
                refreshCaptchaBtn.addEventListener('click', function() {
                    // In a real implementation, this would make an AJAX call to refresh the CAPTCHA
                    // For this simple implementation, we'll just reload the page
                    location.reload();
                });
            }
        });
    </script>
    
</body>
</html>