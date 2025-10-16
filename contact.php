<?php
session_start();
include 'config/db.php';

// Get user referral source if available
$user_id = null;
$user_referral_source = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT referral_source FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && !empty($user['referral_source'])) {
                $user_referral_source = $user['referral_source'];
            }
        } catch (PDOException $e) {
            // Handle error silently
        }
    }
}

include 'includes/navbar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Get user_id if user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    // Save to database
    if ($pdo) {
        try {
            if ($user_id) {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $email, $subject, $message]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $subject, $message]);
            }
            $success = "Thank you for your message! We'll get back to you soon.";
        } catch (PDOException $e) {
            $error = "Sorry, there was an error sending your message. Please try again.";
        }
    } else {
        $error = "Database connection failed. Please try again later.";
    }
} else {
    // Pre-fill form with user data if logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $prefill_name = $user['name'];
                $prefill_email = $user['email'];
            }
        } catch (PDOException $e) {
            // Silently fail, form will be empty
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - KamateRaho</title>
    <link rel="icon" href="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        /* Referral Modal Styles */
        .referral-modal .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .referral-header {
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .referral-link-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 20px 0;
            border: 1px dashed #1a2a6c;
        }
        
        .referral-link {
            word-break: break-all;
            font-family: monospace;
            color: #1a2a6c;
            font-weight: 500;
        }
        
        .copy-btn {
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .copy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.3);
        }
        
        .copy-btn.copied {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .social-share {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }
        
        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-3px);
        }
        
        .whatsapp { background: #25D366; }
        .facebook { background: #4267B2; }
        .twitter { background: #1DA1F2; }
        .telegram { background: #0088cc; }
        
        /* Instagram button styles */
        .btn-instagram { 
            border: none; 
            border-radius: 999px; 
            font-weight: 800; 
            color: white; 
        }
        .btn-instagram:hover { 
            opacity: 0.9; 
            transform: translateY(-2px); 
        }
    </style>
</head>
<body>
    
    <!-- Referral Modal -->
    <div class="modal fade referral-modal" id="referralModal" tabindex="-1" aria-labelledby="referralModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header referral-header">
                    <h5 class="modal-title" id="referralModalLabel">Refer & Earn</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Share your referral link with friends and earn 3 for each successful referral!</p>
                    
                    <div class="referral-link-box">
                        <?php
                        $base_url = "https://kamateraho.com/";
                        $referral_link = $base_url . "register.php?ref=" . $user_id;
                        
                        // Add referral source parameter based on user's referral source or default to 'other'
                        if ($user_referral_source) {
                            $referral_link .= "&source=" . urlencode($user_referral_source);
                        } else {
                            $referral_link .= "&source=other";
                        }
                        ?>
                        <div class="referral-link" id="referralLink"><?php echo $referral_link; ?></div>
                    </div>
                    
                    <button class="copy-btn" id="copyReferralBtn">
                        <i class="fas fa-copy me-2"></i>Copy Referral Link
                    </button>
                    
                    <h6 class="mt-4 mb-3">Or share directly on:</h6>
                    <div class="social-share">
                        <a href="https://api.whatsapp.com/send?text=Join cashbacklo and earn money from home! Register using my referral link: <?php echo urlencode($referral_link); ?>" target="_blank" class="social-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($referral_link); ?>" target="_blank" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=Join cashbacklo and earn money from home! Register using my referral link: <?php echo urlencode($referral_link); ?>" target="_blank" class="social-btn twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://t.me/share/url?url=<?php echo urlencode($referral_link); ?>&text=Join cashbacklo and earn money from home!" target="_blank" class="social-btn telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Platform-specific referral links:</h6>
                    <div class="platform-links">
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            <a href="<?php echo $base_url . 'register.php?ref=' . $user_id . '&source=youtube'; ?>" class="btn btn-danger btn-sm" target="_blank">
                                <i class="fab fa-youtube me-1"></i>YouTube
                            </a>
                            <a href="<?php echo $base_url . 'register.php?ref=' . $user_id . '&source=facebook'; ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fab fa-facebook-f me-1"></i>Facebook
                            </a>
                            <a href="<?php echo $base_url . 'register.php?ref=' . $user_id . '&source=instagram'; ?>" class="btn btn-instagram btn-sm" target="_blank" style="background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); color: white;">
                                <i class="fab fa-instagram me-1"></i>Instagram
                            </a>
                            <a href="<?php echo $base_url . 'register.php?ref=' . $user_id . '&source=twitter'; ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fab fa-twitter me-1"></i>Twitter
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>Contact Us</h1>
                        <p class="lead">We'd love to hear from you!</p>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#referralModal">
                            <i class="fas fa-user-friends me-2"></i>Refer Friend & Earn
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($prefill_name) ? htmlspecialchars($prefill_name) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($prefill_email) ? htmlspecialchars($prefill_email) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                   
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Follow Us</h5>
                    </div>
                    <div class="card-body">
                        <p>Stay connected with us on social media for the latest updates and offers:</p>
                        <div class="d-flex justify-content-around">
                            <a href="#" class="btn btn-primary">Facebook</a>
                            <a href="#" class="btn btn-info text-white">Twitter</a>
                            <a href="#" class="btn btn-danger">Instagram</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Referral Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Referral Modal functionality
            const copyReferralBtn = document.getElementById('copyReferralBtn');
            const referralLink = document.getElementById('referralLink');
            
            if (copyReferralBtn && referralLink) {
                copyReferralBtn.addEventListener('click', function() {
                    navigator.clipboard.writeText(referralLink.innerText).then(() => {
                        // Show success feedback
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                        this.classList.add('copied');
                        
                        // Show success message
                        alert('Referral link copied to clipboard!');
                        
                        // Reset button after 2 seconds
                        setTimeout(() => {
                            this.innerHTML = originalText;
                            this.classList.remove('copied');
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                        alert('Failed to copy link. Please try again.');
                    });
                });
            }
        });
    </script>
</body>
</html>