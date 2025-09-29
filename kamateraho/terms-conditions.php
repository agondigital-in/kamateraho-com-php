<?php
// No database connection needed for static pages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms and Conditions - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <img src="img/logo.png" alt="KamateRaho Logo" style="height: 50px; width: auto;">
            </div>
            <div class="auth-buttons">
                <a href="login.php" class="btn btn-login">Login</a>
                <a href="register.php" class="btn btn-register">Register</a>
            </div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="offers.php">Offers</a></li>
                    <li><a href="testimonials.php">Testimonials</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

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
    
    <!-- Footer -->
    <footer>
        <div class="footer-single-line">
            <div class="footer-single-item">
                <h3>Navigate</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms-conditions.php">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <div class="footer-single-item">
                <h3>Who we are?</h3>
                <p>KamateRaho.com is the latest and exclusive website for earning pocket cash. KamateRaho.com supports Paytm, PhonePay and GooglePay and other online payment methods.</p>
            </div>
            
            <div class="footer-single-item">
                <h3>How it Works?</h3>
                <p>To get free paytm amount user must participate in the offers listed on offer page with genuine information and send the redeem request once we review your request your amount will be transfered in your paytm wallet.</p>
            </div>
            
            <div class="footer-single-item">
                <h3>Stay Connected</h3>
                <p>Connect with us on social media for updates and offers.</p>
                <div class="footer-social">
                    <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2025 KamateRaho.com. All rights reserved.</p>
        </div>
    </footer>
    
    <script>
        /* Responsive Menu */
        function toggleMenu() {
            const nav = document.querySelector('nav ul');
            nav.classList.toggle('active');
        }
        
        // Update the year in footer
        document.querySelector('.footer-bottom p').innerHTML = '© ' + new Date().getFullYear() + ' KamateRaho.com. All rights reserved.';
    </script>
</body>
</html>