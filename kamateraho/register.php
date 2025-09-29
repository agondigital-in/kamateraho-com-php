<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KamateRaho.com</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffffff, #f6f9ff);
            color: #333;
            line-height: 1.6;
        }

        /* Header Styles */
        header {
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo h1 {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
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
            background-color: white;
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
            margin: 0;
            padding: 0;
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 20px;
        }

        nav ul li a:hover {
            background: rgba(255, 255, 255, 0.2);
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
        }

        .btn-login {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-login:hover {
            background: white;
            color: #1a2a6c;
        }

        .btn-register {
            background: #ff6e7f;
            color: white;
            border: 2px solid #ff6e7f;
        }

        .btn-register:hover {
            background: #ff526a;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 110, 127, 0.3);
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
                background: linear-gradient(90deg, #1a2a6c, #f7b733);
                width: 70%;
                height: 100vh;
                padding: 80px 20px 20px;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                margin: 0;
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
            }

            .auth-buttons {
                position: absolute;
                top: 20px;
                right: 20px;
            }
        }

        /* Registration Form */
        .registration-container {
            max-width: 600px;
            margin: 120px auto 50px;
            padding: 0 20px;
        }

        .registration-form {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .registration-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
        }

        .registration-form h2 {
            color: #1a2a6c;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 14px 14px 14px 40px;
            border: 2px solid #e1e5ee;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            border-color: #1a2a6c;
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 42, 108, 0.1);
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 42px;
            color: #1a2a6c;
        }

        .btn-submit {
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
            color: white;
            border: none;
            padding: 14px 32px;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 30px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 15px rgba(26, 42, 108, 0.3);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .form-footer {
            margin-top: 2rem;
            text-align: center;
        }

        .form-footer a {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-footer a:hover {
            text-decoration: underline;
            transform: translateY(-2px);
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
            font-weight: 500;
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

        /* Benefits Section */
        .benefits {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 3rem 5%;
            background-color: #fff;
        }

        .benefit-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 1rem;
            padding: 2rem;
            text-align: center;
            width: 30%;
            min-width: 250px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .benefit-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
        }

        .benefit-card h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        /* Footer */
        footer {
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 2rem;
        }

        .footer-single-line {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 2rem 5%;
        }

        .footer-single-item {
            width: 30%;
            min-width: 200px;
            margin: 1rem;
        }

        .footer-single-item h3 {
            color: white;
            margin-bottom: 1rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .footer-links a:hover {
            color: white;
            text-decoration: underline;
        }

        /* Responsive */
        @media(max-width: 992px) {
            .benefit-card {
                width: 45%;
            }
        }

        @media(max-width: 768px) {
            .benefit-card {
                width: 100%;
            }
            
            nav ul {
                display: none;
            }
            
            .registration-container {
                margin: 80px auto 30px;
            }
            
            .registration-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <header style="background: linear-gradient(90deg, #1a2a6c, #f7b733); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100;">
        <div class="logo">
            <h1 style="color: white; font-size: 1.8rem; font-weight: 700; margin: 0;">
                Kamate<span style="color: #ff6e7f;">Raho</span>
            </h1>
        </div>
        
        <div class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <nav>
            <ul style="display: flex; list-style: none; margin: 0; padding: 0;" id="navMenu">
                <li style="margin-left: 25px;"><a href="index.php" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Home</a></li>
                <li style="margin-left: 25px;"><a href="index.php#how-it-works" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">How It Works</a></li>
                <li style="margin-left: 25px;"><a href="testimonials.php" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Testimonials</a></li>
                <li style="margin-left: 25px;"><a href="index.php#withdrawal-info" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Withdrawals</a></li>
                <li style="margin-left: 25px;"><a href="offers.php" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Offers</a></li>
                <li style="margin-left: 25px;"><a href="register.php" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Register</a></li>
                <li style="margin-left: 25px;"><a href="login.php" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Login</a></li>
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
    
    <div class="registration-container">
        <div class="registration-form">
            <h2>Create Your Free Account</h2>
            <p style="margin-bottom: 1.5rem; color: #555;">Join & Get Rs. 50 Instantly</p>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <form action="register_process.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" required placeholder="Enter your full name">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <i class="fas fa-phone"></i>
                    <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Create a password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                </div>
                
                <button type="submit" class="btn-submit">Register & Get Rs. 50</button>
            </form>
            
            <div class="form-footer">
                <p>Already have an account? <a href="login.php">Login Now</a></p>
            </div>
        </div>
    </div>
    
    <!-- Benefits Section -->
    <section class="benefits">
        <div class="benefit-card">
            <h3><i class="fas fa-rupee-sign"></i> Earn Daily</h3>
            <p>Participate in offers and earn up to Rs. 500 every day with just your smartphone.</p>
        </div>
        
        <div class="benefit-card">
            <h3><i class="fas fa-users"></i> Invite Friends</h3>
            <p>Get Rs. 3.00 for each friend who joins and completes their first offer.</p>
        </div>
        
        <div class="benefit-card">
            <h3><i class="fas fa-bolt"></i> Instant Withdrawal</h3>
            <p>Withdraw your earnings via Paytm, PhonePe or GooglePay with no hidden charges.</p>
        </div>
    </section>
    
    <footer>
        <div class="footer-single-line">
            <div class="footer-single-item">
                <h3>Navigate</h3>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms & Conditions</a></li>
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
                <p style="margin-top: 0.8rem;">​karo</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2023 KamateRaho.com. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>