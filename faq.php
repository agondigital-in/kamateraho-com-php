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
    <meta name="description" content="Frequently Asked Questions about KamateRaho - Shop & get cashback on every purchase.">
    <title>FAQ - KamateRaho</title>
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
        
        .faq-section {
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
            max-width: 600px;
            margin: 0 auto;
        }
        
        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .category-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 40px 0 20px;
            color: #2c3e50;
            padding-bottom: 10px;
            border-bottom: 2px solid #f7b733;
        }
        
        .faq-item {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            background: white;
            transition: all 0.3s ease;
        }
        
        .faq-item:hover {
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }
        
        .faq-question {
            width: 100%;
            text-align: left;
            padding: 20px;
            background: #f8f9fa;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .faq-question:hover {
            background: #e9ecef;
        }
        
        .faq-icon {
            font-size: 1.5rem;
            font-weight: bold;
            color: #f7b733;
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }
        
        .faq-answer {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
            background: white;
        }
        
        .faq-item.active .faq-answer {
            padding: 20px;
            max-height: 500px;
        }
        
        .faq-answer p {
            margin: 0;
            line-height: 1.6;
            color: #495057;
        }
        
        .faq-cta {
            text-align: center;
            margin-top: 50px;
            padding: 40px;
            background: linear-gradient(135deg, #1a2a6c, #2c3e50);
            color: white;
            border-radius: 15px;
        }
        
        .faq-cta h3 {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .faq-cta p {
            font-size: 1.1rem;
            margin-bottom: 25px;
            opacity: 0.9;
        }
        
        .btn-contact {
            background: #f7b733;
            color: #1a2a6c;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        
        .btn-contact:hover {
            background: #ffcc33;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(247, 183, 51, 0.4);
            color: #1a2a6c;
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 60px 0;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .category-title {
                font-size: 1.5rem;
            }
            
            .faq-question {
                padding: 15px;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="hero animate__animated animate__fadeIn">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="animate__animated animate__fadeInDown">Frequently Asked Questions</h1>
                    <p class="animate__animated animate__fadeInUp">Find answers to common questions about KamateRaho</p>
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

    <section class="faq-section">
        <div class="container">
            <div class="section-title animate__animated animate__fadeIn">
                <h2>FAQs</h2>
                <p>Everything you need to know about KamateRaho</p>
            </div>
            
            <div class="faq-container">
                <div class="faq-category">
                    <h2 class="category-title animate__animated animate__fadeIn">About KamateRaho</h2>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>What is KamateRaho?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>KamateRaho is a cashback rewards platform that allows you to earn cashback points on your everyday online purchases. When you shop through our partnered retailers, you earn points that can be redeemed for discounts on future purchases.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>How does KamateRaho work?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>It's simple! First, you sign up for a free account. Then, when you want to make a purchase, you visit our website and click through to the retailer from our site. After making your purchase, the retailer pays us a commission, and we pass a portion of that commission to you as cashback points.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>Is KamateRaho free to use?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Yes, KamateRaho is completely free to use. There are no membership fees or hidden costs. You earn cashback points on purchases you were already planning to make.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-category">
                    <h2 class="category-title animate__animated animate__fadeIn">Earning Cashback</h2>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>How do I earn cashback?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>To earn cashback, you must make your purchase by clicking through from the KamateRaho website to the retailer's site. This ensures that the retailer knows you came through our platform and can properly credit your account.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>How much cashback can I earn?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Cashback rates vary by retailer, typically ranging from 1% to 10% of your purchase amount. Some special promotions may offer even higher rates. You can see the current cashback rate for each retailer on our website.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>When will my cashback be credited?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Cashback is typically credited to your account within 24-48 hours after your purchase. However, some retailers may take longer to confirm the transaction, especially for items with longer return periods.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-category">
                    <h2 class="category-title animate__animated animate__fadeIn">Using Your Cashback</h2>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>How do I use my cashback points?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Your cashback points can be redeemed as discounts on future purchases through KamateRaho. When you have sufficient points in your account, they will automatically be applied to your next eligible purchase.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>Is there a minimum amount to redeem?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Yes, the minimum redemption amount is ₹100 worth of cashback points. Once you reach this threshold, your points will be automatically converted to a discount on your next purchase.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>Do cashback points expire?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Cashback points remain valid for 12 months from the date they are credited to your account. After this period, unused points will expire and be removed from your account.</p>
                        </div>
                    </div>
                </div>
                
                <div class="faq-category">
                    <h2 class="category-title animate__animated animate__fadeIn">Account & Security</h2>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>Is my personal information safe?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>Yes, we take the security of your personal information very seriously. We use industry-standard encryption and security measures to protect your data. For more information, please review our Privacy Policy.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>Can I have multiple accounts?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>No, each person is allowed to have only one KamateRaho account. Creating multiple accounts is against our terms of service and may result in all accounts being suspended.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item animate__animated animate__fadeInUp">
                        <button class="faq-question">
                            <span>What should I do if I forget my password?</span>
                            <i class="faq-icon">+</i>
                        </button>
                        <div class="faq-answer">
                            <p>If you forget your password, click on the "Forgot Password" link on the login page. We'll send you an email with instructions to reset your password.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="faq-cta animate__animated animate__fadeInUp">
                <h3>Still have questions?</h3>
                <p>Contact our support team for personalized assistance</p>
                <a href="contact.php" class="btn btn-contact">Contact Support</a>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // FAQ accordion functionality
        document.addEventListener('DOMContentLoaded', function() {
            const faqQuestions = document.querySelectorAll('.faq-question');
            
            faqQuestions.forEach(question => {
                question.addEventListener('click', () => {
                    const faqItem = question.parentElement;
                    const answer = question.nextElementSibling;
                    const icon = question.querySelector('.faq-icon');
                    
                    // Close all other FAQ items
                    document.querySelectorAll('.faq-item').forEach(item => {
                        if (item !== faqItem) {
                            item.classList.remove('active');
                            const otherAnswer = item.querySelector('.faq-answer');
                            const otherIcon = item.querySelector('.faq-icon');
                            if (otherAnswer) otherAnswer.style.display = 'none';
                            if (otherIcon) otherIcon.textContent = '+';
                        }
                    });
                    
                    // Toggle current FAQ item
                    faqItem.classList.toggle('active');
                    
                    // Toggle answer visibility
                    if (faqItem.classList.contains('active')) {
                        answer.style.display = 'block';
                        icon.textContent = '−';
                    } else {
                        answer.style.display = 'none';
                        icon.textContent = '+';
                    }
                });
            });
        });
    </script>
</body>
</html>