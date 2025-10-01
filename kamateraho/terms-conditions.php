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
                    <a href="https://www.facebook.com/share/17JFgQNHrS/?mibextid=wwXIfr" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/_kamate_raho?igsh=d2hsYmo2NXFvOGRi" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
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