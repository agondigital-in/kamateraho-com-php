<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user referral source if available
$user_referral_source = null;
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

// Fetch user's replied messages
$messages = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE user_id = ? AND status = 'replied' AND reply IS NOT NULL ORDER BY replied_at DESC");
        $stmt->execute([$user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching messages: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages - cashbacklo</title>
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
    <?php include 'includes/navbar.php'; ?>
    
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
                <li class="breadcrumb-item active" aria-current="page">My Messages</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1>My Messages</h1>
                        <p class="lead">View replies from admin to your messages</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#referralModal">
                            <i class="fas fa-user-friends me-2"></i>Refer Friend & Earn
                        </button>
                    </div>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (empty($messages)): ?>
                    <div class="alert alert-info">
                        <p>You don't have any replies from admin yet.</p>
                        <p><a href="contact.php" class="btn btn-primary">Send a Message</a></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h5><?php echo htmlspecialchars($msg['subject']); ?></h5>
                                    <span class="badge bg-success">Replied</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Sent:</strong> <?php echo $msg['created_at']; ?></p>
                                <p><strong>Your Message:</strong></p>
                                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                
                                <div class="border-top mt-3 pt-3">
                                    <p><strong>Admin Reply:</strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                                    <p><strong>Replied at:</strong> <?php echo $msg['replied_at']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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