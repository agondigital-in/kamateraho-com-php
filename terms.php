<?php
include 'config/db.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Terms and Conditions</li>
            </ol>
        </nav>
        
        <h1>Terms and Conditions</h1>
        <p class="text-muted">Last updated: <?php echo date('F d, Y'); ?></p>
        
        <div class="card">
            <div class="card-body">
                <h3>1. Introduction</h3>
                <p>Welcome to KamateRaho ("we," "our," or "us"). These Terms and Conditions govern your use of our website located at kamateraho.com (the "Site") and the services we provide (collectively, the "Services"). By accessing or using our Site and Services, you agree to be bound by these Terms and Conditions and our Privacy Policy.</p>
                
                <h3>2. Eligibility</h3>
                <p>To be eligible to use our Services, you must be at least 18 years old and capable of forming a binding contract. By using our Services, you represent and warrant that you meet these requirements.</p>
                
                <h3>3. Account Registration</h3>
                <p>To access certain features of our Services, you may be required to register for an account. You agree to provide accurate, current, and complete information during registration and to update such information as necessary. You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account.</p>
                
                <h3>4. Cashback Offers</h3>
                <p>Our cashback offers are subject to change without notice. We strive to provide accurate information, but we do not guarantee the accuracy, completeness, or reliability of any offer details. Cashback amounts are subject to verification, and we reserve the right to modify or cancel any cashback offer at any time.</p>
                
                <h3>5. Wallet and Withdrawals</h3>
                <p>Users can accumulate cashback in their wallet. The minimum withdrawal amount is ₹200. Withdrawal requests are processed within 24-48 business hours. We reserve the right to request additional verification before processing withdrawals.</p>
                
                <h3>6. Prohibited Activities</h3>
                <p>You agree not to engage in any of the following prohibited activities:</p>
                <ul>
                    <li>Using our Services for any illegal purpose</li>
                    <li>Attempting to gain unauthorized access to our systems</li>
                    <li>Using automated systems to access our Site</li>
                    <li>Interfering with the proper functioning of our Services</li>
                    <li>Creating multiple accounts to abuse our cashback system</li>
                </ul>
                
                <h3>7. Intellectual Property</h3>
                <p>All content on our Site, including text, graphics, logos, and software, is the property of KamateRaho and is protected by intellectual property laws. You may not use our content without our prior written consent.</p>
                
                <h3>8. Limitation of Liability</h3>
                <p>To the fullest extent permitted by law, KamateRaho shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, revenue, data, or use, incurred by you or any third party.</p>
                
                <h3>9. Changes to Terms</h3>
                <p>We reserve the right to modify these Terms and Conditions at any time. We will notify users of any material changes by posting the updated terms on our Site. Your continued use of our Services after such changes constitutes your acceptance of the modified terms.</p>
                
                <h3>10. Governing Law</h3>
                <p>These Terms and Conditions are governed by and construed in accordance with the laws of India, without regard to its conflict of law principles.</p>
                
                <h3>11. Contact Information</h3>
                <p>If you have any questions about these Terms and Conditions, please contact us at:</p>
                <p>Email: support@kamateraho.com<br>
                Address: KamateRaho Pvt. Ltd., 123 Business Street, Mumbai, Maharashtra 400001, India</p>
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