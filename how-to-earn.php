<?php
include 'config/db.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How To Earn - cashbacklo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .step-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .benefit-card {
            background: linear-gradient(135deg, #e6f4ff, #ffffff);
            border-radius: 10px;
            border: 1px solid #d1e7ff;
            transition: transform 0.3s ease;
        }
        
        .benefit-card:hover {
            transform: translateY(-3px);
        }
        
        .earning-method {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        
        .tip-card {
            background: #fff8e6;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid #ffc107;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">How To Earn</li>
            </ol>
        </nav>
        
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary">How To Earn Money</h1>
            <p class="lead">Simple steps to start earning cashback from home</p>
        </div>
        
        <!-- Steps to Earn -->
        <section class="mb-5">
            <h2 class="section-title">Easy Steps To Start Earning</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex step-card p-4 h-100">
                        <div class="step-number">1</div>
                        <div>
                            <h4>Register For Free</h4>
                            <p>Sign up on our platform with your email and mobile number. It's completely free and takes less than 2 minutes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex step-card p-4 h-100">
                        <div class="step-number">2</div>
                        <div>
                            <h4>Browse Offers</h4>
                            <p>Explore hundreds of cashback offers across various categories like finance, insurance, credit cards, and more.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex step-card p-4 h-100">
                        <div class="step-number">3</div>
                        <div>
                            <h4>Complete Tasks</h4>
                            <p>Follow the simple instructions for each offer. This might include filling a form, making a purchase, or applying for a service.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex step-card p-4 h-100">
                        <div class="step-number">4</div>
                        <div>
                            <h4>Earn & Withdraw</h4>
                            <p>Get cashback credited to your wallet instantly. Withdraw your earnings to your bank account or UPI once you reach the minimum limit.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Earning Methods -->
        <section class="mb-5">
            <h2 class="section-title">Ways To Earn</h2>
            <div class="earning-method">
                <h4><i class="fas fa-credit-card text-primary me-2"></i>Credit Card Referrals</h4>
                <p>Refer friends to apply for credit cards and earn upto ₹500 per successful referral. Different cards offer different rewards.</p>
            </div>
            
            <div class="earning-method">
                <h4><i class="fas fa-piggy-bank text-primary me-2"></i>Savings Account Offers</h4>
                <p>Help people open savings accounts with partner banks and earn attractive commissions for each successful account opening.</p>
            </div>
            
            <div class="earning-method">
                <h4><i class="fas fa-heart text-primary me-2"></i>Insurance Policies</h4>
                <p>Earn commissions by helping people get life, health, or motor insurance policies from our partner companies.</p>
            </div>
            
            <div class="earning-method">
                <h4><i class="fas fa-shopping-cart text-primary me-2"></i>EMI Products</h4>
                <p>Refer customers for EMI products and earn commissions on successful conversions.</p>
            </div>
        </section>
        
        <!-- Benefits -->
        <section class="mb-5">
            <h2 class="section-title">Why Choose Us</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="benefit-card p-4 h-100">
                        <div class="text-center mb-3">
                            <i class="fas fa-wallet fa-2x text-primary"></i>
                        </div>
                        <h5 class="text-center">Instant Credit</h5>
                        <p class="text-center">Get cashback credited to your wallet immediately after task completion.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card p-4 h-100">
                        <div class="text-center mb-3">
                            <i class="fas fa-rupee-sign fa-2x text-primary"></i>
                        </div>
                        <h5 class="text-center">High Earnings</h5>
                        <p class="text-center">Earn up to ₹500 per successful referral with our premium partners.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card p-4 h-100">
                        <div class="text-center mb-3">
                            <i class="fas fa-mobile-alt fa-2x text-primary"></i>
                        </div>
                        <h5 class="text-center">Work From Home</h5>
                        <p class="text-center">Work at your own pace from anywhere, anytime. No office timings.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Tips -->
        <section class="mb-5">
            <h2 class="section-title">Pro Tips To Earn More</h2>
            <div class="tip-card">
                <h5><i class="fas fa-lightbulb me-2 text-warning"></i>Maximize Your Earnings</h5>
                <ul class="mb-0">
                    <li>Share offers on social media regularly to reach more potential customers</li>
                    <li>Focus on high-value offers that give better commissions</li>
                    <li>Build a network of people interested in financial products</li>
                    <li>Follow up with your referrals to ensure successful conversions</li>
                    <li>Check for new offers daily as we keep adding fresh opportunities</li>
                </ul>
            </div>
        </section>
        
        <!-- CTA Section -->
        <section class="text-center py-5 bg-light rounded-3">
            <h3 class="mb-3">Ready To Start Earning?</h3>
            <p class="mb-4">Join thousands of users who are already earning cashback with us</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php" class="btn btn-primary btn-lg px-5 py-3">
                    <i class="fas fa-arrow-right me-2"></i>Start Earning Now
                </a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary btn-lg px-5 py-3">
                    <i class="fas fa-user-plus me-2"></i>Register & Get ₹50 Bonus
                </a>
                <div class="mt-3">
                    <small>Already have an account? <a href="login.php">Login here</a></small>
                </div>
            <?php endif; ?>
        </section>
    </div>
    
    <!-- Footer -->
    <footer class="bg-dark text-white pt-4 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>cashbacklo</h5>
                    <p>Earn cash from home by completing simple tasks and get paid instantly.</p>
                </div>
                <div class="col-md-6">
                    <h5>Connect With Us</h5>
                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/share/17JFgQNHrS/?mibextid=wwXIfr" target="_blank" class="text-white fs-4">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/_kamate_raho?igsh=d2hsYmo2NXFvOGRi" target="_blank" class="text-white fs-4">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 cashbacklo. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>