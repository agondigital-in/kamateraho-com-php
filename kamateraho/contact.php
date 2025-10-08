<?php
include '../config/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Save to database
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = "Thank you for your message! We'll get back to you soon.";
        } catch (PDOException $e) {
            $error = "Sorry, there was an error sending your message. Please try again.";
        }
    } else {
        $error = "Database connection failed. Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact KamateRaho - Online Earning Platform</title>
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
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

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
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            width: auto;
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

        .contact-section {
            padding: 4rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .section-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
        }

        .contact-form {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #1a2a6c;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1a2a6c;
            outline: none;
            box-shadow: 0 0 0 3px rgba(26, 42, 108, 0.1);
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(26, 42, 108, 0.3);
        }

        .contact-info {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .info-item {
            display: flex;
            margin-bottom: 2rem;
            align-items: flex-start;
        }

        .info-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .info-icon i {
            color: white;
            font-size: 1.2rem;
        }

        .info-content h3 {
            color: #1a2a6c;
            margin-bottom: 0.5rem;
        }

        .info-content p {
            color: #333;
            line-height: 1.6;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-link {
            display: inline-flex;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            transform: translateY(-3px);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        footer {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            color: #333;
            padding: 2rem 0 1rem;
            margin-top: 2rem;
            font-family: 'Poppins', sans-serif;
            border-top: 1px solid #d1d1d1;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 0 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-section {
            width: 22%;
            min-width: 200px;
            margin: 1rem;
            text-align: left;
        }

        .footer-section h3 {
            color: #1a2a6c;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 0.3rem;
            font-weight: 600;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 2px;
            background: #ff6e7f;
            border-radius: 2px;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.6rem;
        }

        .footer-links a {
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: inline-block;
            position: relative;
            padding-left: 0;
        }

        .footer-links a:hover {
            color: #1a2a6c;
            padding-left: 8px;
        }

        .footer-links a::before {
            content: "→";
            position: absolute;
            left: -15px;
            opacity: 0;
            transition: all 0.3s ease;
            color: #ff6e7f;
            font-size: 0.8rem;
        }

        .footer-links a:hover::before {
            opacity: 1;
            left: -20px;
        }

        .footer-section p {
            color: #333;
            line-height: 1.6;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }

        .footer-contact p {
            position: relative;
            padding-left: 25px;
            margin-bottom: 0.6rem;
        }

        .footer-contact p i {
            position: absolute;
            left: 0;
            top: 3px;
            color: #ff6e7f;
        }

        .footer-social {
            display: flex;
            gap: 12px;
            margin-top: 0.8rem;
        }

        .footer-social a {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: rgba(26, 42, 108, 0.1);
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            color: #1a2a6c;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .footer-social a:hover {
            background: #1a2a6c;
            color: white;
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.85rem;
            color: #333;
        }

        @media (max-width: 768px) {
            .contact-container {
                flex-direction: column;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .section-subtitle {
                font-size: 1rem;
            }
            
            .footer-section {
                width: 100%;
                text-align: center;
            }
            
            .footer-section h3::after {
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759811997/logo_cpe9n0.png" alt="KamateRaho Logo" style="height: 65px; width: 250px;">
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
                <li><a href="blog/index.php">Blog</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="../register.php">Register</a></li>
                <li><a href="../login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    
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

    <section class="contact-section">
        <h2 class="section-title">Contact Us</h2>
        <p class="section-subtitle">Have questions about our platform or services? We're here to help!</p>
        
        <div class="contact-container">
            <div class="contact-form">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea class="form-control" id="message" name="message" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Send Message</button>
                </form>
            </div>
            
            <div class="contact-info">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Our Location</h3>
                        <p>jaipur, Rajasthan, India<br>City, State 302017</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Phone Number</h3>
                        <p>+91 98765 43210</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email Address</h3>
                        <p>support@kamateraho.com</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Working Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-content">
                        <h3>Follow Us</h3>
                        <div class="social-links">
                            <a href="https://www.facebook.com/share/17JFgQNHrS/?mibextid=wwXIfr" target="_blank" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.instagram.com/_kamate_raho?igsh=d2hsYmo2NXFvOGRi" target="_blank" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="/">Home</a></li>
                    <li><a href="index.php#how-it-works">How It Works</a></li>
                    <li><a href="index.php#testimonial-container">Testimonials</a></li>
                    <li><a href="index.php#withdrawal-info">Withdrawals</a></li>
                    <li><a href="blog/index.php">Blog</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Legal</h3>
                <ul class="footer-links">
                    <li><a href="privacy-policy.php">Privacy Policy</a></li>
                    <li><a href="terms-conditions.php">Terms & Conditions</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>About Us</h3>
                <p>KamateRaho.com connects users with various online earning opportunities. We facilitate partnerships between offer providers and users, supporting instant payouts via Paytm, PhonePe, Google Pay, and more.</p>
            </div>
            
            <div class="footer-section">
                <h3>Contact Info</h3>
                <div class="footer-contact">
                    <p><i class="fas fa-envelope"></i> support@kamateraho.com</p>
                    <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                </div>
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
</body>
</html>