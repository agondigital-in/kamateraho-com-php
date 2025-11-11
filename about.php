<?php
include 'config/db.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - KamateRaho</title>
    <link rel="icon" href="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-RMM38DLZLM');
    </script>
</head>
<body>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About Us</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-8">
                <h1>About KamateRaho</h1>
                <p class="lead">India's Best Cashback & Coupons Website</p>
                
                <p>KamateRaho is a revolutionary cashback platform that helps you save money on every purchase. We partner with top brands and retailers to bring you exclusive deals and cashback offers.</p>
                
                <h3>Our Mission</h3>
                <p>Our mission is to make smart shopping accessible to everyone. We believe that everyone should benefit from their purchases, which is why we've created a platform that rewards you for shopping online.</p>
                
                <h3>How It Works</h3>
                <ol>
                    <li><strong>Shop</strong> - Browse through our extensive collection of categories and offers</li>
                    <li><strong>Buy</strong> - Make a purchase through our platform</li>
                    <li><strong>Earn</strong> - Get cashback directly into your wallet</li>
                    <li><strong>Withdraw</strong> - Transfer your earnings to your bank account or UPI</li>
                </ol>
                
                <h3>Why Choose KamateRaho?</h3>
                <ul>
                    <li>High cashback rates on thousands of products</li>
                    <li>Easy withdrawal process with minimum ₹200 limit</li>
                    <li>Wide range of categories and brands</li>
                    <li>Secure and reliable platform</li>
                    <li>₹50 welcome bonus for new users</li>
                </ul>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <span>Categories:</span>
                            <strong>
                                <?php
                                if ($pdo) {
                                    try {
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
                                        echo $stmt->fetchColumn();
                                    } catch (PDOException $e) {
                                        echo "0";
                                    }
                                } else {
                                    echo "0";
                                }
                                ?>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Active Users:</span>
                            <strong>
                                <?php
                                if ($pdo) {
                                    try {
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                                        echo $stmt->fetchColumn();
                                    } catch (PDOException $e) {
                                        echo "0";
                                    }
                                } else {
                                    echo "0";
                                }
                                ?>
                            </strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Total Offers:</span>
                            <strong>
                                <?php
                                if ($pdo) {
                                    try {
                                        $stmt = $pdo->query("SELECT COUNT(*) FROM offers");
                                        echo $stmt->fetchColumn();
                                    } catch (PDOException $e) {
                                        echo "0";
                                    }
                                } else {
                                    echo "0";
                                }
                                ?>
                            </strong>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Start Saving Today</h5>
                    </div>
                    <div class="card-body">
                        <p>Join thousands of happy users who are already saving with KamateRaho.</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="index.php#categories" class="btn btn-primary w-100">Start Shopping</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-primary w-100">Register Now</a>
                            <div class="text-center mt-2">
                                <small>Already have an account? <a href="login.php">Login</a></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>KamateRaho</h5>
                    <p>India's best cashback and coupons website. Save money on every purchase.</p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="index.php#categories" class="text-white">Categories</a></li>
                        <li><a href="about.php" class="text-white">About Us</a></li>
                        <li><a href="contact.php" class="text-white">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="dashboard.php" class="text-white">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="text-white">Login</a></li>
                            <li><a href="register.php" class="text-white">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Legal</h5>
                    <ul class="list-unstyled">
                        <li><a href="terms.php" class="text-white">Terms & Conditions</a></li>
                        <li><a href="privacy.php" class="text-white">Privacy Policy</a></li>
                    </ul>
                    <h5>Contact Us</h5>
                    <p>Email: support@kamateraho.com</p>
                    <p>Phone: +91 9876543210</p>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; 2025 KamateRaho. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>