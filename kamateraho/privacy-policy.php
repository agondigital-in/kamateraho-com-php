<?php
// No database connection needed for static pages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="kamateraho/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
      <header>
        <div class="logo">
            <img src="kamateraho/img/logo.png" alt="KamateRaho Logo" style="height: 50px; width: auto;">
        </div>
        
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <nav>
            <ul id="navMenu">
                <li><a href="/">Home</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#testimonial-container">Testimonials</a></li>
                <li><a href="#withdrawal-info">Withdrawals</a></li>
                <li><a href="#blog">Blog</a></li>
                <li><a href="../register.php">Register</a></li>
                <li><a href="../login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
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
        });
    </script>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Privacy Policy</li>
            </ol>
        </nav>
        
        <h1>Privacy Policy</h1>
        <p class="text-muted">Last updated: <?php echo date('F d, Y'); ?></p>
        
        <div class="card">
            <div class="card-body">
                <h3>1. Introduction</h3>
                <p>KamateRaho ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website kamateraho.com (the "Site") and use our services (collectively, the "Services").</p>
                
                <h3>2. Information We Collect</h3>
                <p>We collect information that you provide directly to us, including:</p>
                <ul>
                    <li><strong>Personal Information:</strong> Name, email address, phone number, and other contact details</li>
                    <li><strong>Account Information:</strong> Username, password, and wallet balance</li>
                    <li><strong>Transaction Information:</strong> Details of purchases, cashback earnings, and withdrawal requests</li>
                    <li><strong>Communication Information:</strong> Messages, feedback, and customer support interactions</li>
                </ul>
                
                <p>We also automatically collect certain information when you visit our Site:</p>
                <ul>
                    <li><strong>Log Information:</strong> IP address, browser type, operating system, referring URLs, and access times</li>
                    <li><strong>Device Information:</strong> Device type, unique device identifiers, and mobile network information</li>
                    <li><strong>Usage Information:</strong> Pages visited, time spent on pages, and features used</li>
                </ul>
                
                <h3>3. How We Use Your Information</h3>
                <p>We use the information we collect for various purposes, including:</p>
                <ul>
                    <li>To provide, maintain, and improve our Services</li>
                    <li>To process transactions and send related information</li>
                    <li>To verify your identity and prevent fraud</li>
                    <li>To communicate with you about our Services</li>
                    <li>To personalize your experience and recommend relevant offers</li>
                    <li>To comply with legal obligations and resolve disputes</li>
                </ul>
                
                <h3>4. Information Sharing and Disclosure</h3>
                <p>We may share your information in the following circumstances:</p>
                <ul>
                    <li><strong>With Service Providers:</strong> We may share information with third-party vendors who perform services on our behalf</li>
                    <li><strong>For Legal Reasons:</strong> We may disclose information to comply with legal obligations or protect our rights</li>
                    <li><strong>Business Transfers:</strong> We may transfer information in connection with a merger or acquisition</li>
                    <li><strong>With Your Consent:</strong> We may share information with your consent</li>
                </ul>
                
                <h3>5. Data Security</h3>
                <p>We implement appropriate technical and organizational measures to protect your personal information. However, no method of transmission over the Internet or electronic storage is 100% secure, so we cannot guarantee absolute security.</p>
                
                <h3>6. Data Retention</h3>
                <p>We retain your information for as long as necessary to provide our Services and comply with legal obligations. When we no longer need your information, we will securely delete it.</p>
                
                <h3>7. Your Rights</h3>
                <p>You have certain rights regarding your personal information, including:</p>
                <ul>
                    <li>The right to access, update, or delete your information</li>
                    <li>The right to object to or restrict processing</li>
                    <li>The right to data portability</li>
                    <li>The right to withdraw consent</li>
                </ul>
                
                <h3>8. Cookies and Tracking Technologies</h3>
                <p>We use cookies and similar tracking technologies to enhance your experience and analyze site usage. You can control cookies through your browser settings.</p>
                
                <h3>9. Children's Privacy</h3>
                <p>Our Services are not intended for individuals under 18 years of age. We do not knowingly collect personal information from children.</p>
                
                <h3>10. Changes to This Privacy Policy</h3>
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last updated" date.</p>
                
                <h3>11. Contact Us</h3>
                <p>If you have any questions about this Privacy Policy, please contact us at:</p>
                <p>Email: privacy@kamateraho.com<br>
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