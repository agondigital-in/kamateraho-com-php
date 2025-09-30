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
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
       * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            color: #333;
            line-height: 1.6;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            }

            nav ul.active {
                right: 0;
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
<body>
    <!-- Header -->
      <header>
        <div class="logo">
            <img src="img/logo.png" alt="KamateRaho Logo" style="height: 50px; width: auto;">
        </div>
        
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <nav>
            <ul id="navMenu">
                <li><a href="/">Home</a></li>
                <li><a href="/">How It Works</a></li>
                <li><a href="/">Testimonials</a></li>
                <li><a href="/">Withdrawals</a></li>
                <li><a href="/">Blog</a></li>
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
                <li class="breadcrumb-item"><a href="/">Home</a></li>
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
                
                
            </div>
        </div>
    </div>
    
    <!-- Footer -->
 <footer>
        <div class="footer-single-line">
            <div class="footer-single-item">
                <h3>Navigate</h3>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms-conditions.php">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <div class="footer-single-item">
                <h3>Who we are?</h3>
                <p>KamateRaho.com is your exclusive site to earn pocket cash online. Instant payouts supported via Paytm, PhonePe, Google Pay, and more.</p>
            </div>
            
            <div class="footer-single-item">
                <h3>How it Works?</h3>
                <p>Participate in offers on our page with genuine details and send a redeem request. Once approved, your Paytm amount will be transferred instantly.</p>
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