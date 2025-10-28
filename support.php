<?php
// Start session and include necessary files
session_start();
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contact KamateRaho - Shop & get cashback on every purchase.">
    <title>Contact Us - KamateRaho</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Add animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .hero {
            background: linear-gradient(135deg, #1a2a6c, #2c3e50);
            color: white;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        
        .hero-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
        }
        
        .wave-fill {
            fill: white;
        }
        
        .contact-section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #1a2a6c;
        }
        
        .section-title p {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .contact-container {
            display: flex;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .contact-info {
            flex: 1;
        }
        
        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #f7b733;
        }
        
        .contact-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .contact-card p {
            color: #495057;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        
        .contact-card a {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .contact-card a:hover {
            color: #f7b733;
            text-decoration: underline;
        }
        
        .contact-form {
            flex: 1;
        }
        
        .form-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .form-card h2 {
            font-size: 1.8rem;
            margin-bottom: 30px;
            color: #2c3e50;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #1a2a6c;
            box-shadow: 0 0 0 0.25rem rgba(26, 42, 108, 0.25);
            outline: none;
        }
        
        .contact-btn {
            background: linear-gradient(135deg, #1a2a6c, #2c3e50);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 30px;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .contact-btn:hover {
            background: linear-gradient(135deg, #2c3e50, #1a2a6c);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.4);
        }
        
        .response-time {
            padding: 80px 0;
            background: #f8f9fa;
        }
        
        .steps {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 50px;
            flex-wrap: wrap;
        }
        
        .step {
            text-align: center;
            max-width: 300px;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1a2a6c, #2c3e50);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        
        .step h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .step p {
            color: #495057;
            line-height: 1.6;
        }
        
        /* Firework effect */
        .firework {
            position: absolute;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            pointer-events: none;
            opacity: 0;
        }
        
        /* Thank you message styling */
        #thankYouMessage {
            display: none;
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        #thankYouMessage h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        #thankYouMessage p {
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        /* Form hiding when submitted */
        .form-submitted .contact-form {
            display: none;
        }
        
        @media (max-width: 992px) {
            .contact-container {
                flex-direction: column;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 60px 0;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .form-card {
                padding: 25px;
            }
            
            .steps {
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="hero animate__animated animate__fadeIn">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="animate__animated animate__fadeInDown">Contact Us</h1>
                    <p class="animate__animated animate__fadeInUp">We'd love to hear from you. Get in touch with our team.</p>
                </div>
            </div>
        </div>
        <div class="hero-wave">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="wave-fill"></path>
                <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="wave-fill"></path>
                <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="wave-fill"></path>
            </svg>
        </div>
    </div>

    <section class="contact-section">
        <div class="container">
            <div class="section-title animate__animated animate__fadeIn">
                <h2>Get In Touch</h2>
                <p>Have questions about KamateRaho? Need help with your account or a cashback transaction? Our support team is here to help.</p>
            </div>
            
            <div class="contact-container">
                <div class="contact-info animate__animated animate__fadeInLeft">
                    <div class="contact-card">
                        <div class="contact-icon">📧</div>
                        <h3>Email Support</h3>
                        <p>For general inquiries: <a href="mailto:info@kamateraho.com">info@kamateraho.com</a></p>
                        <p>For support: <a href="mailto:support@kamateraho.com">support@kamateraho.com</a></p>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">💼</div>
                        <h3>Business Inquiries</h3>
                        <p>business development: <a href="mailto:Business@kamateraho.com">Business@kamateraho.com</a></p>
                    </div>
                    
                    <div class="contact-card">
                        <div class="contact-icon">📰</div>
                        <h3>Press & Media</h3>
                        <p>For press inquiries: <a href="mailto:press@kamateraho.com">press@kamateraho.com</a></p>
                    </div>
                </div>
                
                <div class="contact-form animate__animated animate__fadeInRight">
                    <div class="form-card">
                        <h2>Send Us a Message</h2>
                        <form id="contactForm" action="#" method="POST">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="contact-btn">Send Message</button>
                        </form>
                        
                        <!-- Thank you message -->
                        <div id="thankYouMessage" class="animate__animated">
                            <h2>🎉 Thank You! 🎉</h2>
                            <p>Your message has been sent successfully!</p>
                            <p>We'll get back to you as soon as possible.</p>
                            <div class="firework-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="response-time">
        <div class="container">
            <div class="section-title animate__animated animate__fadeIn">
                <h2>Response Time</h2>
                <p>We strive to respond to all inquiries as quickly as possible</p>
            </div>
            <div class="steps">
                <div class="step animate__animated animate__fadeInUp">
                    <div class="step-number">1</div>
                    <h3>General Inquiries</h3>
                    <p>Response within 24 hours</p>
                </div>
                <div class="step animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="step-number">2</div>
                    <h3>Account Issues</h3>
                    <p>Response within 12 hours</p>
                </div>
                <div class="step animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="step-number">3</div>
                    <h3>Urgent Matters</h3>
                    <p>Response within 2 hours</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const contactForm = document.getElementById('contactForm');
            const thankYouMessage = document.getElementById('thankYouMessage');
            const formCard = document.querySelector('.form-card');
            
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent actual form submission for demo
                
                // Show thank you message
                formCard.classList.add('form-submitted');
                thankYouMessage.style.display = 'block';
                thankYouMessage.classList.add('animate__fadeIn');
                
                // Create firework effect
                createFireworks();
                
                // Reset form after submission
                setTimeout(() => {
                    contactForm.reset();
                }, 1000);
            });
            
            function createFireworks() {
                const container = document.querySelector('.firework-container');
                container.innerHTML = '';
                
                // Create multiple fireworks
                for (let i = 0; i < 50; i++) {
                    setTimeout(() => {
                        createFirework(container);
                    }, i * 100);
                }
            }
            
            function createFirework(container) {
                const firework = document.createElement('div');
                firework.className = 'firework';
                
                // Random position
                const posX = Math.random() * 100;
                const posY = Math.random() * 100;
                
                firework.style.left = `${posX}%`;
                firework.style.top = `${posY}%`;
                
                // Random color
                const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ffffff'];
                const color = colors[Math.floor(Math.random() * colors.length)];
                firework.style.backgroundColor = color;
                
                // Random size
                const size = Math.random() * 5 + 2;
                firework.style.width = `${size}px`;
                firework.style.height = `${size}px`;
                
                container.appendChild(firework);
                
                // Animate firework
                firework.animate([
                    { transform: 'scale(1)', opacity: 1 },
                    { transform: 'scale(3)', opacity: 0 }
                ], {
                    duration: 1000,
                    easing: 'ease-out'
                });
                
                // Remove element after animation
                setTimeout(() => {
                    firework.remove();
                }, 1000);
            }
        });
    </script>
</body>
</html>