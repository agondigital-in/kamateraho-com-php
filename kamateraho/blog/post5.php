<?php
// Blog post 5
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Features Coming Soon - KamateRaho.com</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .blog-header {
            text-align: center;
            padding: 3rem 0;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            margin-bottom: 2rem;
        }
        
        .blog-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .blog-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .blog-meta {
            display: flex;
            gap: 1rem;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .blog-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .blog-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .blog-content h2 {
            color: #1a2a6c;
            margin: 1.5rem 0;
        }
        
        .blog-content p {
            line-height: 1.8;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .blog-content ul, .blog-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .blog-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }
        
        .feature-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #1a2a6c;
        }
        
        .feature-card h3 {
            color: #1a2a6c;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .feature-icon {
            color: #f7b733;
        }
        
        .coming-soon {
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin: 2rem 0;
        }
        
        .coming-soon h2 {
            margin-top: 0;
            font-size: 2rem;
        }
        
        .countdown {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .countdown-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 1rem;
            border-radius: 10px;
            min-width: 80px;
        }
        
        .countdown-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .countdown-label {
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .blog-header h1 {
                font-size: 2rem;
            }
            
            .blog-image {
                height: 250px;
            }
            
            .countdown {
                flex-wrap: wrap;
            }
            
            .countdown-item {
                min-width: 70px;
            }
        }
    </style>
</head>
<body>
   
    <section class="blog-header">
        <div class="container">
            <h1>New Features Coming Soon</h1>
            <p>Exciting updates to enhance your experience</p>
        </div>
    </section>

    <section class="blog-container">
        <a href="./index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Blog</a>
        
        <div class="blog-meta">
            <span><i class="far fa-calendar"></i> Oct 15, 2025</span>
            <span><i class="far fa-user"></i> Admin</span>
            <span><i class="far fa-clock"></i> 4 min read</span>
        </div>
        
        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="New Features Coming Soon" class="blog-image">
        
        <div class="blog-content">
            <p>We're constantly working to improve your experience on KamateRaho.com. Our team is excited to announce several new features that will be rolled out in the coming weeks. These updates are designed to make earning money online even easier, more enjoyable, and more rewarding for our users.</p>
            
            <h2>What's Coming Your Way</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <h3><i class="fas fa-mobile-alt feature-icon"></i> Mobile App</h3>
                    <p>A dedicated mobile app for Android and iOS devices to access all features on the go.</p>
                </div>
                
                <div class="feature-card">
                    <h3><i class="fas fa-trophy feature-icon"></i> Achievement System</h3>
                    <p>Earn badges and rewards for completing milestones and achieving specific goals.</p>
                </div>
                
                <div class="feature-card">
                    <h3><i class="fas fa-chart-line feature-icon"></i> Earnings Analytics</h3>
                    <p>Detailed insights and reports on your earning patterns and performance.</p>
                </div>
                
                <div class="feature-card">
                    <h3><i class="fas fa-users feature-icon"></i> Community Forum</h3>
                    <p>Connect with other users, share tips, and participate in discussions.</p>
                </div>
                
                <div class="feature-card">
                    <h3><i class="fas fa-gift feature-icon"></i> Daily Bonuses</h3>
                    <p>Special daily login bonuses and rewards for consistent participation.</p>
                </div>
                
                <div class="feature-card">
                    <h3><i class="fas fa-crown feature-icon"></i> VIP Program</h3>
                    <p>Exclusive benefits and higher payouts for our most dedicated users.</p>
                </div>
            </div>
            
            <h2>Mobile App - Our Biggest Update Yet</h2>
            <p>The most anticipated feature is our dedicated mobile app, which will be available for both Android and iOS devices. The app will include all the functionality of our web platform plus additional mobile-specific features:</p>
            
            <ul>
                <li><strong>Push Notifications:</strong> Get real-time updates on new offers and tasks</li>
                <li><strong>Offline Mode:</strong> Save tasks to complete later when you're offline</li>
                <li><strong>Biometric Login:</strong> Secure and convenient fingerprint or face recognition login</li>
                <li><strong>Quick Withdrawal:</strong> One-tap withdrawal requests</li>
                <li><strong>Dark Mode:</strong> Eye-friendly dark theme for nighttime use</li>
            </ul>
            
            <h2>Achievement System</h2>
            <p>We're introducing a gamified achievement system to make earning more fun and rewarding. Users will be able to earn badges for:</p>
            
            <ul>
                <li>Completing their first task</li>
                <li>Earning their first ₹1,000</li>
                <li>Referring 10 friends</li>
                <li>Maintaining a streak of 30 consecutive days</li>
                <li>Completing 100 tasks</li>
            </ul>
            
            <p>Each achievement will come with special rewards and recognition within our community.</p>
            
            <h2>Earnings Analytics Dashboard</h2>
            <p>Our new analytics dashboard will provide detailed insights into your earning patterns:</p>
            
            <ul>
                <li>Weekly and monthly earning trends</li>
                <li>Most profitable task categories</li>
                <li>Peak earning hours</li>
                <li>Comparison with community averages</li>
                <li>Projection tools for future earnings</li>
            </ul>
            
            <div class="coming-soon">
                <h2>Launching Soon!</h2>
                <p>Mark your calendars for our biggest update yet</p>
                
                <div class="countdown">
                    <div class="countdown-item">
                        <div class="countdown-number" id="days">15</div>
                        <div class="countdown-label">Days</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-number" id="hours">08</div>
                        <div class="countdown-label">Hours</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-number" id="minutes">42</div>
                        <div class="countdown-label">Minutes</div>
                    </div>
                    <div class="countdown-item">
                        <div class="countdown-number" id="seconds">19</div>
                        <div class="countdown-label">Seconds</div>
                    </div>
                </div>
                
                <p>Stay tuned for more updates!</p>
            </div>
            
            <h2>How These Features Will Benefit You</h2>
            <p>These new features are designed with you, our valued user, in mind:</p>
            
            <ol>
                <li><strong>Increased Convenience:</strong> Access everything from your mobile device</li>
                <li><strong>Enhanced Motivation:</strong> Gamification elements make earning more engaging</li>
                <li><strong>Better Insights:</strong> Understand your earning patterns to maximize income</li>
                <li><strong>Stronger Community:</strong> Connect with like-minded individuals</li>
                <li><strong>More Rewards:</strong> Additional ways to earn through daily bonuses</li>
            </ol>
            
            <h2>Stay Updated</h2>
            <p>To ensure you don't miss any of these exciting updates:</p>
            
            <ul>
                <li>Follow us on social media for real-time updates</li>
                <li>Check the blog regularly for feature announcements</li>
                <li>Enable notifications on our website</li>
                <li>Join our WhatsApp community for insider information</li>
            </ul>
            
            <h2>Your Feedback Matters</h2>
            <p>We're always looking for ways to improve your experience. If you have suggestions for new features or improvements to existing ones, we'd love to hear from you:</p>
            
            <ul>
                <li><strong>Email:</strong> feedback@kamateraho.com</li>
                <li><strong>WhatsApp:</strong> +91-9876543210</li>
                <li><strong>Social Media:</strong> Tag us in your suggestions</li>
            </ul>
            
            <p>We're committed to making KamateRaho.com the best platform for earning money online. These new features are just the beginning of our journey to provide you with the ultimate earning experience. Stay tuned for more updates, and thank you for being a part of our community!</p>
        </div>
    </section>

    
    <script>
        // Mobile menu toggle
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
            
            // Simple countdown timer (for demonstration)
            function updateCountdown() {
                // This is just a static example - in a real implementation, this would count down to a specific date
                document.getElementById('days').textContent = '15';
                document.getElementById('hours').textContent = '08';
                document.getElementById('minutes').textContent = '42';
                document.getElementById('seconds').textContent = '19';
            }
            
            // Update countdown every second
            setInterval(updateCountdown, 1000);
            updateCountdown(); // Initial call
        });
    </script>
</body>
</html>