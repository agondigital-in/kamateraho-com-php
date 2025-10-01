<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Offers - KamateRaho.com</title>
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

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            padding: 80px 5% 50px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
        }

        /* Instructions */
        .instructions {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2.5rem;
            margin: 50px auto;
            max-width: 900px;
        }

        .instructions h2 {
            color: #1a2a6c;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
        }

        .instructions ol {
            padding-left: 1.5rem;
            margin: 1.5rem 0;
        }

        .instructions li {
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        /* Withdrawal Information Section */
        .withdrawal-info {
            background: linear-gradient(135deg, #f8f9ff, #ffffff);
            padding: 4rem 5%;
            margin: 3rem 0;
        }

        .withdrawal-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .info-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 2rem;
            width: 30%;
            min-width: 250px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .info-card h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .info-card p {
            color: #555;
            line-height: 1.6;
        }

        /* Offers Container */
        .offers-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 1rem 5% 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .offer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 1rem;
            padding: 2rem;
            width: 30%;
            min-width: 280px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .offer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .offer-card h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .offer-description {
            color: #555;
            margin-bottom: 1.5rem;
            min-height: 80px;
        }

        .reward {
            color: #ff6e7f;
            font-weight: 700;
            font-size: 1.3rem;
            margin: 1rem 0;
        }

        .btn-participate {
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 30px;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-participate:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.2);
        }

        /* Footer */
        footer {
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
            color: white;
            text-align: center;
            padding: 2rem 0;
            margin-top: 2rem;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 2rem 5%;
        }

        .footer-section {
            width: 30%;
            min-width: 200px;
            margin: 1rem;
        }

        .footer-section h3 {
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
            .offer-card {
                width: 45%;
            }
        }

        @media(max-width: 768px) {
            .offer-card {
                width: 100%;
            }
            
            nav ul {
                display: none;
            }
            
            .page-header h1 {
                font-size: 2rem;
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
                <li style="margin-left: 25px;"><a href="#withdrawal-info" style="color: white; text-decoration: none; font-weight: 500; font-size: 1.1rem; transition: all 0.3s ease; padding: 8px 12px; border-radius: 20px;">Withdrawals</a></li>
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
    
    <div class="page-header">
        <h1>Available Offers</h1>
        <p>Complete these offers to earn cash and grow your wallet balance</p>
    </div>
    
    <div class="instructions">
        <h2>How to Participate in Offers</h2>
        <ol>
            <li>Browse the available offers listed below</li>
            <li>Click on "Participate Now" for any offer you want to join</li>
            <li>Complete the required tasks as mentioned in the offer details</li>
            <li>Submit the required information</li>
            <li>Wait for verification (usually takes 12-24 hours)</li>
            <li>Once approved, your reward will be credited to your wallet</li>
        </ol>
    </div>
    
    <!-- How It Works Section -->
    <section class="steps" id="how-it-works">
        <div class="container">
            <h2>How It Works</h2>
            <p>Follow these simple steps to start earning cash with KamateRaho.com</p>
        </div>
        <div class="step-container">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Join KamateRaho.com</h3>
                <p>Create your free account and get Rs 50 instantly as a welcome bonus.</p>
                <div class="step-divider"></div>
            </div>
            
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Participate in Offers</h3>
                <p>Browse and register in offers that match your interests and skills.</p>
                <div class="step-divider"></div>
            </div>
            
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Get Paid</h3>
                <p>Complete tasks, get approved, and receive payments directly to your wallet.</p>
                <div class="step-divider"></div>
            </div>
        </div>
    </section>
    
    <div class="offers-container" id="offers">
        <div class="offer-card">
            <h3>App Installation Offer</h3>
            <p class="offer-description">Install and use our partner app for 7 days to earn this reward.</p>
            <div class="reward">Reward: ₹50</div>
            <button class="btn-participate">Participate Now</button>
        </div>
        
        <div class="offer-card">
            <h3>Survey Completion</h3>
            <p class="offer-description">Complete a 10-minute survey about your preferences and shopping habits.</p>
            <div class="reward">Reward: ₹30</div>
            <button class="btn-participate">Participate Now</button>
        </div>
        
        <div class="offer-card">
            <h3>Social Media Promotion</h3>
            <p class="offer-description">Share our app on social media and get 10 likes on your post.</p>
            <div class="reward">Reward: ₹20</div>
            <button class="btn-participate">Participate Now</button>
        </div>
        
        <div class="offer-card">
            <h3>Referral Challenge</h3>
            <p class="offer-description">Refer 5 friends to join our platform and complete their first offer.</p>
            <div class="reward">Reward: ₹100</div>
            <button class="btn-participate">Participate Now</button>
        </div>
        
        <div class="offer-card">
            <h3>Daily Login Bonus</h3>
            <p class="offer-description">Login to your account daily for 7 consecutive days to earn this bonus.</p>
            <div class="reward">Reward: ₹35</div>
            <button class="btn-participate">Participate Now</button>
        </div>
        
        <div class="offer-card">
            <h3>Video Watching</h3>
            <p class="offer-description">Watch 5 promotional videos on our platform and complete feedback forms.</p>
            <div class="reward">Reward: ₹25</div>
            <button class="btn-participate">Participate Now</button>
        </div>
    </div>
    
    <!-- Withdrawal Information -->
    <section class="withdrawal-info" id="withdrawal-info" style="background: linear-gradient(135deg, #ffffff, #f8f9ff); padding: 5rem 5%; margin: 3rem 0; position: relative; overflow: hidden;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 3rem;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 1rem;">Withdrawal Information</h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto;">
                    Understand our simple and secure withdrawal process
                </p>
            </div>
            
            <div class="info-cards" style="display: flex; flex-wrap: wrap; gap: 2rem; justify-content: center;">
                <!-- How Withdrawals Work Card -->
                <div class="info-card" style="background: white; border-radius: 20px; padding: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.08); flex: 1; min-width: 300px; max-width: 380px; transition: all 0.4s ease; border: 1px solid #eee;">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498db, #8e44ad); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(52, 152, 219, 0.2);">
                            <i class="fas fa-exchange-alt" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <h3 style="color: #2c3e50; margin-bottom: 1rem; font-size: 1.5rem;">How Withdrawals Work</h3>
                    </div>
                    <div style="background: linear-gradient(to right, #3498db, #8e44ad); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
                    <p style="color: #7f8c8d; line-height: 1.8; font-size: 1rem; text-align: center; margin-bottom: 1.5rem;">
                        Minimum withdrawal amount is ₹200.00. After verification (within 12 hours), cash will be transferred directly to your bank account via UPI.
                    </p>
                    <div style="text-align: center;">
                        <div style="display: inline-flex; align-items: center; gap: 0.5rem; color: #3498db; font-weight: 500;">
                            <span>Fast Processing</span>
                            <i class="fas fa-bolt" style="color: #f39c12;"></i>
                        </div>
                    </div>
                </div>

                <!-- Registration Bonus Card -->
                <div class="info-card" style="background: white; border-radius: 20px; padding: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.08); flex: 1; min-width: 300px; max-width: 380px; transition: all 0.4s ease; border: 1px solid #eee;">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e74c3c, #e67e22); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(231, 76, 60, 0.2);">
                            <i class="fas fa-gift" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <h3 style="color: #2c3e50; margin-bottom: 1rem; font-size: 1.5rem;">Registration Bonus</h3>
                    </div>
                    <div style="background: linear-gradient(to right, #e74c3c, #e67e22); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
                    <p style="color: #7f8c8d; line-height: 1.8; font-size: 1rem; text-align: center; margin-bottom: 1.5rem;">
                        Register and get ₹30 instantly in your wallet now! No hidden charges, no minimum withdrawal limit. Start earning immediately after registration.
                    </p>
                    <div style="text-align: center;">
                        <div style="display: inline-block; background: #e74c3c; color: white; padding: 0.5rem 1.5rem; border-radius: 30px; font-weight: 600; font-size: 1.1rem; box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);">
                            ₹30 Bonus
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Card -->
                <div class="info-card" style="background: white; border-radius: 20px; padding: 2.5rem; box-shadow: 0 15px 35px rgba(0,0,0,0.08); flex: 1; min-width: 300px; max-width: 380px; transition: all 0.4s ease; border: 1px solid #eee;">
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #27ae60, #2ecc71); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);">
                            <i class="fas fa-cash-check-alt" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <h3 style="color: #2c3e50; margin-bottom: 1rem; font-size: 1.5rem;">Payment Methods</h3>
                    </div>
                    <div style="background: linear-gradient(to right, #27ae60, #2ecc71); height: 3px; border-radius: 2px; margin: 1.5rem 0;"></div>
                    <p style="color: #7f8c8d; line-height: 1.8; font-size: 1rem; text-align: center; margin-bottom: 1.5rem;">
                        We support instant transfers to your bank account via UPI ID or QR code scanning. Withdrawals are processed within 24 hours 
                    </p>
                    <div style="display: flex; justify-content: center; gap: 1rem; margin-top: 1rem;">
                        <div style="width: 50px; height: 50px; background: #27ae60; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <div style="width: 50px; height: 50px; background: #27ae60; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white;">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 4rem;">
                <a href="register.php" class="btn" style="background: linear-gradient(135deg, #3498db, #8e44ad); padding: 1.2rem 2.5rem; font-size: 1.2rem; border-radius: 50px; color: white; text-decoration: none; font-weight: 600; box-shadow: 0 10px 25px rgba(52, 152, 219, 0.4); transition: all 0.3s ease; border: none; cursor: pointer; display: inline-block; text-transform: uppercase; letter-spacing: 1px;">Register Now</a>
            </div>
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
                    <a href="https://www.facebook.com/share/17JFgQNHrS/?mibextid=wwXIfr" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com/_kamate_raho?igsh=d2hsYmo2NXFvOGRi" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
                <p style="margin-top: 0.8rem;">​karo</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>© 2023 KamateRaho.com. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Enhanced hover effects -->
    <style>
        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(52, 152, 219, 0.5);
        }
    </style>
</body>
</html>