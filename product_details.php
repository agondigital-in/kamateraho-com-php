<?php
session_start();
include 'config/db.php';
include 'includes/price_helper.php'; // Include price helper functions

// Get ID and type from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'offer'; // 'offer' or 'card'

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
error_log("DEBUG: Session user_id = " . $user_id);

// Additional session validation
if (session_status() !== PHP_SESSION_ACTIVE) {
    error_log("DEBUG: Session is not active");
    session_start();
}

// Fetch details based on type
if ($type === 'card') {
    // Fetch credit card details
    try {
        $stmt = $pdo->prepare("SELECT * FROM credit_cards WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            header('Location: index.php');
            exit;
        }
        
        // Set variables for credit card
        $item['title'] = $item['title'];
        $item['description'] = $item['description'] ?? "Credit Card Offer"; // Use actual description from DB
        $item['price'] = 0;
        $item['image'] = $item['image'];
        $item['redirect_url'] = $item['link'];
        $item['category_name'] = "Credit Cards";
        $item['category_id'] = 0;
        
        // Store amount details for display
        $item['amount'] = $item['amount'] ?? 0;
        $item['percentage'] = $item['percentage'] ?? 0;
        $item['flat_rate'] = $item['flat_rate'] ?? 0;
        
        // No additional images for credit cards
        $additional_images = [];
    } catch(PDOException $e) {
        $error = "Error fetching credit card: " . $e->getMessage();
        $item = null;
        $additional_images = [];
    }
} else {
    // Fetch offer details (default)
    try {
        $stmt = $pdo->prepare("SELECT o.*, c.name as category_name FROM offers o JOIN categories c ON o.category_id = c.id WHERE o.id = ? AND o.is_active = 1");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            header('Location: index.php');
            exit;
        }
        
        // Fetch additional images for this offer
        $stmt = $pdo->prepare("SELECT image_path FROM offer_images WHERE offer_id = ? ORDER BY id");
        $stmt->execute([$id]);
        $additional_images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        $error = "Error fetching offer: " . $e->getMessage();
        $item = null;
        $additional_images = [];
    }
}

$success_message = '';
$error_message = '';

// Handle apply now action
error_log("POST data received: " . print_r($_POST, true));
error_log("Session data: user_id=" . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set'));

if (isset($_POST['apply_now'])) {
    error_log("Apply now button was clicked");
    
    // Create a withdraw request for this offer/card
    if ($item) {
        try {
            // For offers, use the price; for cards, we'll need to determine the amount
            // Handle different price types
            if ($type === 'offer' && isset($item['price'])) {
                $price_type = $item['price_type'] ?? 'fixed';
                
                // For percentage-based offers, use a default amount or the stored price
                // The actual amount will be set by admin later
                if ($price_type !== 'fixed') {
                    // For percentage-based offers, store the offer price (percentage) as amount
                    // Admin will adjust this later
                    $amount = $item['price'];
                } else {
                    // For fixed price, use the stored price
                    $amount = $item['price'];
                }
            } else {
                $amount = 0;
                $price_type = 'fixed';
            }
            
            // Special handling for credit cards - we'll use a default amount or get it from somewhere
            if ($type === 'card') {
                // For credit cards, we'll use a default amount or prompt for it
                $amount = 10000; // Default amount for credit card applications
                $price_type = 'fixed';
            }
            
            // Check if user exists in database
            // First, verify that user_id is valid
            if (!isset($user_id) || empty($user_id)) {
                error_log("ERROR: User ID is not set or is empty");
                $error_message = "User session not found. Please log in again.";
            } else {
                $userCheckStmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
                $userCheckStmt->execute([$user_id]);
                $userExists = $userCheckStmt->fetch();
                
                if (!$userExists) {
                    error_log("ERROR: User ID " . $user_id . " does not exist in users table");
                    $error_message = "User account not found. Please contact support.";
                } else {
                    error_log("User ID " . $user_id . " exists in users table");
                
                    // Create a special "purchase" withdraw request that will add money to wallet when approved
                    $timestamp = time();
                    $upi_id = "purchase@" . $timestamp; // Special UPI ID to identify purchases
                    error_log("Generated UPI ID: " . $upi_id . " (timestamp: " . $timestamp . ")");
                    $screenshot = ""; // No screenshot needed for this type of request
                    
                    // Debug: Log the data being inserted
                    error_log("Creating withdraw request - User ID: " . $user_id . ", Amount: " . $amount . ", UPI ID: " . $upi_id . ", Offer Title: " . $item['title']);
                    
                    // Add additional information for percentage-based offers
                    $offer_description = $item['description'];
                    if ($type === 'offer' && !empty($price_type) && $price_type !== 'fixed') {
                        $offer_description .= " | Offer type: " . ucfirst(str_replace('_', ' ', $price_type)) . " | Offer percentage: " . number_format($item['price'], 2) . "% | Admin to determine final amount";
                    }
                    
                    // Insert withdraw request with special UPI ID to identify it as a purchase
                    $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, screenshot, offer_title, offer_description) VALUES (?, ?, ?, ?, ?, ?)");
                    $result = $stmt->execute([
                        $user_id, 
                        $amount, 
                        $upi_id, 
                        $screenshot,
                        $item['title'],
                        $offer_description
                    ]);
                    
                    // Debug: Check if insertion was successful
                    if ($result) {
                        error_log("Withdraw request inserted successfully");
                        // Get the ID of the inserted request
                        $request_id = $pdo->lastInsertId();
                        error_log("Request ID: " . $request_id);
                        $success_message = "Your application request has been submitted successfully! The admin will review your request and determine the final reward amount.";
                    } else {
                        error_log("Failed to insert withdraw request");
                        error_log("Error info: " . print_r($stmt->errorInfo(), true));
                        $error_message = "Failed to submit your application request. Please try again.";
                    }
                }
            }
        } catch(PDOException $e) {
            error_log("Database error creating request: " . $e->getMessage());
            error_log("SQL State: " . $e->getCode());
            $error_message = "Error creating request: " . $e->getMessage();
        } catch(Exception $e) {
            error_log("General error creating request: " . $e->getMessage());
            $error_message = "Error creating request: " . $e->getMessage();
        }
    } else {
        error_log("No item found for apply_now action");
        $error_message = "Unable to process your request. Item not found.";
    }
    
    // Check if there are any errors before processing
    if (!empty($error_message)) {
        error_log("Error message exists, not redirecting: " . $error_message);
    } else {
        error_log("No errors, preparing redirect");
        // Only redirect if there were no errors
        // Redirect to the item's redirect URL with only p_id=user_id parameter
        $redirect_url = $item['redirect_url'];
        if (!empty($redirect_url)) {
            // Add p_id=user_id parameter to the redirect URL
            $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
            // Add click counter parameter
            $click_counter = time(); // Using timestamp as a simple click counter
            $redirect_url .= $separator . 'p_id=' . $user_id . '&click_id=' . $click_counter;
            error_log("Redirecting to: " . $redirect_url);
            header("Location: " . $redirect_url);
            exit;
        } else {
            // Fallback to Bajaj Finserv if no redirect URL is set
            $redirect_url = "https://www.bajajfinserv.in/webform/v1/emicard/login?utm_source=Expartner&utm_medium=79&utm_campaign=6111";
            error_log("Redirecting to fallback URL: " . $redirect_url);
            header("Location: " . $redirect_url);
            exit;
        }
    }
}

// Assuming you have variables $user_id and $p_id available
$p_id = $id; // Assuming $id is the product ID
$apply_link = "apply_offer.php?user_id={$user_id}&p_id={$p_id}";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($item['title']); ?> - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-RMM38DLZLM');
    </script>
    <style>
        :root {
            --primary-color: #8e44ad;
            --secondary-color: #9b59b6;
            --accent-color: #e74c3c;
            --light-color: #f3e5f5;
            --dark-color: #4a235a;
            --success-color: #27ae60;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .breadcrumb {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1rem;
            border-radius: 4px;
        }
        
        .breadcrumb a {
            color: #d6b3e7;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            color: white;
            text-decoration: underline;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }
        
        .product-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
        }
        
        .thumbnail-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
            justify-content: center;
        }
        
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 4px;
            transition: border-color 0.3s;
        }
        
        .thumbnail:hover,
        .thumbnail.active {
            border-color: var(--secondary-color);
        }
        
        .price-tag {
            background: var(--secondary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-weight: 600;
            display: inline-block;
            font-size: 1.2rem;
        }
        
        /* Price type styling */
        .price-fixed {
            color: #28a745;
            font-weight: bold;
        }
        
        .price-flat-percent {
            color: #007bff;
            font-weight: bold;
        }
        
        .price-upto-percent {
            color: #ffc107;
            font-weight: bold;
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
        }
        
        .feature-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .feature-list li:last-child {
            border-bottom: none;
        }
        
        .feature-list i {
            color: var(--success-color);
            margin-right: 0.5rem;
        }
        
        .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #8e44ad;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-earn-money {
            border: 2px solid #0d6efd !important;
            background: linear-gradient(135deg, #4361ee, #3a0ca3) !important;
            color: white !important;
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            min-height: 50px;
        }
        
        .btn-earn-money:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3) !important;
        }
        
        .btn-outline-primary {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            min-height: 50px;
        }
        
        .info-box {
            background: var(--light-color);
            border-left: 4px solid var(--secondary-color);
            padding: 1.5rem;
            border-radius: 0 4px 4px 0;
            margin: 1.5rem 0;
        }
        
        .how-to-get-box {
            background: linear-gradient(135deg, #fdfefe 0%, #f8f9fa 100%);
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        /* New styles for application process */
        .process-steps {
            display: flex;
            flex-wrap: wrap;
            margin: 2rem 0;
            gap: 1rem;
        }
        
        .step {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 1.5rem;
            position: relative;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #eee;
        }
        
        .step:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        
        .step-number {
            background: var(--secondary-color);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 1rem;
            font-size: 1.2rem;
        }
        
        .step h5 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .step p {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin: 1rem 0;
        }
        
        .rating {
            color: #f39c12;
        }
       
        
        .faq-item {
            margin-bottom: 1rem;
        }
        
        .faq-question {
            background: var(--light-color);
            color: var(--dark-color);
            padding: 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .faq-answer {
            background: white;
            padding: 1rem;
            border-left: 4px solid var(--secondary-color);
            border-radius: 0 4px 4px 0;
            margin-top: 0.5rem;
            display: none;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .header {
                padding: 1.5rem 0;
                margin-bottom: 1.5rem;
            }
            
            .product-image {
                max-height: 350px;
            }
            
            .thumbnail {
                width: 60px;
                height: 60px;
            }
            
            .step {
                min-width: 100%;
                margin-bottom: 1rem;
            }
            
            .process-steps {
                gap: 0.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .product-image {
                max-height: 300px;
            }
            
            .thumbnail {
                width: 50px;
                height: 50px;
            }
            
            .price-tag {
                font-size: 1rem;
                padding: 0.4rem 0.8rem;
            }
            
            .section-title {
                font-size: 1.25rem;
            }
            
            .btn-earn-money, .btn-outline-primary {
                font-size: 0.9rem !important;
                padding: 0.75rem 0.6rem !important;
                min-height: 44px !important;
            }
        }
        
        @media (max-width: 400px) {
            .product-image {
                max-height: 250px;
            }
            
            .thumbnail {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                            <?php if ($type === 'offer' && !empty($item['category_id'])): ?>
                                <li class="breadcrumb-item"><a href="category.php?id=<?php echo $item['category_id']; ?>" class="text-white"><?php echo htmlspecialchars($item['category_name']); ?></a></li>
                            <?php endif; ?>
                            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($item['title']); ?></li>
                        </ol>
                    </nav>
                    <h1 class="display-6 fw-bold mb-2"><?php echo htmlspecialchars($item['title']); ?></h1>
                    <p class="lead mb-0">Professional financial solution tailored for your needs</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="text-center mb-4">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Redirecting...</span>
                </div>
                <p class="mt-2">Redirecting to offer page in 3 seconds...</p>
            </div>
            <!-- Celebratory animation -->
            <div id="celebration-animation" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>
            <script>
                // Create celebratory animation
                function createCelebration() {
                    const container = document.getElementById('celebration-animation');
                    container.innerHTML = '';
                    
                    // Create multiple celebratory elements
                    for (let i = 0; i < 50; i++) {
                        const elem = document.createElement('div');
                        elem.style.position = 'absolute';
                        elem.style.width = '10px';
                        elem.style.height = '10px';
                        elem.style.backgroundColor = getRandomColor();
                        elem.style.borderRadius = '50%';
                        elem.style.left = Math.random() * 100 + '%';
                        elem.style.top = '-10px';
                        elem.style.opacity = '0';
                        elem.style.animation = `fall-${i} 2s ease-in forwards`;
                        
                        // Add CSS for animation
                        const style = document.createElement('style');
                        style.innerHTML = `
                            @keyframes fall-${i} {
                                0% { transform: translateY(0) rotate(0deg); opacity: 1; }
                                100% { transform: translateY(${Math.random() * 100 + 200}px) rotate(${Math.random() * 360}deg); opacity: 0; }
                            }
                        `;
                        document.head.appendChild(style);
                        
                        container.appendChild(elem);
                    }
                }
                
                function getRandomColor() {
                    const colors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff', '#00ffff', '#ff9900'];
                    return colors[Math.floor(Math.random() * colors.length)];
                }
                
                // Trigger celebration animation
                createCelebration();
                
                // Redirect after 3 seconds if the request was successful
                setTimeout(function() {
                    <?php if ($item && !empty($item['redirect_url'])): ?>
                        var redirectUrl = "<?php echo htmlspecialchars($item['redirect_url']); ?>";
                        var separator = redirectUrl.indexOf('?') !== -1 ? '&' : '?';
                        var userId = "<?php echo $user_id; ?>";
                        var clickCounter = "<?php echo time(); ?>";
                        window.location.href = redirectUrl + separator + 'p_id=' + userId + '&click_id=' + clickCounter;
                    <?php else: ?>
                        // Fallback to Bajaj Finserv if no redirect URL is set
                        window.location.href = "https://www.bajajfinserv.in/webform/v1/emicard/login?utm_source=Expartner&utm_medium=79&utm_campaign=6111";
                    <?php endif; ?>
                }, 3000);
            </script>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <div class="text-center mb-4">
                <p class="mt-2">Please correct the error and try again.</p>
            </div>
        <?php endif; ?>
        
        <?php if ($item): ?>
            <div class="product-card">
                <div class="row g-0">
                    <div class="col-md-6">
                        <?php if (!empty($item['image'])): ?>
                            <img id="mainImage" src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 class="product-image" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                                <i class="fas fa-image fa-5x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($additional_images)): ?>
                            <div class="thumbnail-container">
                                <!-- Additional images thumbnails (excluding main image) -->
                                <?php foreach ($additional_images as $image_path): ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                         class="thumbnail" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                                         onclick="changeImage('<?php echo htmlspecialchars($image_path); ?>')">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="p-4">
                            <?php if ($type === 'offer' && isset($item['price']) && $item['price'] > 0): ?>
                                <div class="mb-3">
                                    <span class="price-tag"><?php echo display_price($item['price'], $item['price_type'] ?? 'fixed'); ?></span>
                                    <?php if (!empty($item['redirect_url'])): ?>
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-external-link-alt me-1"></i> External Offer
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php elseif ($type === 'card'): ?>
                                <!-- Display credit card amount details in the same style as all_offers -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if ($item['amount'] > 0): ?>
                                            <div>
                                                <span class="text-muted text-decoration-line-through me-1"></span>
                                                <strong class="text-success">Amount: ₹<?php echo number_format($item['amount'], 2); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($item['category_name'])): ?>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($item['category_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($item['percentage'] > 0): ?>
                                        <div class="mt-2">
                                            <span class="badge bg-primary">Percentage: <?php echo number_format($item['percentage'], 2); ?>%</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($item['flat_rate'] > 0): ?>
                                        <div class="mt-2">
                                            <span class="badge bg-warning text-dark">Flat Rate: ₹<?php echo number_format($item['flat_rate'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($item['amount'] == 0 && $item['percentage'] == 0 && $item['flat_rate'] == 0): ?>
                                        <div>
                                            <span class="text-muted text-decoration-line-through me-1"></span>
                                            <strong class="text-success">Amount: Variable</strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="how-to-get-box">
                                <h3 class="section-title">How to Get This Offer</h3>
                                <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                            </div>
                            
                            <div class="info-box mt-3">
                                <p class="mb-0"><strong>Note:</strong> Once verified with Advertiser, you will receive the payment in 12-24 hours.</p>
                            </div>
                            
                            <div class="mt-4">
                                <div class="d-flex gap-2">
                                    <form method="POST" class="flex-grow-1">
                                        <?php if ($type === 'offer' && !empty($item['price_type']) && $item['price_type'] !== 'fixed'): ?>
                                            <!-- For percentage-based offers, show information but don't allow user to enter amount -->
                                            <div class="mb-3">
                                                <div class="alert alert-info">
                                                    <strong>Offer Details:</strong> This offer provides <?php 
                                                    switch($item['price_type']) {
                                                        case 'flat_percent':
                                                            echo number_format($item['price'], 2) . '% reward';
                                                            break;
                                                        case 'upto_percent':
                                                            echo 'up to ' . number_format($item['price'], 2) . '% reward';
                                                            break;
                                                    }
                                                    ?>. The exact reward amount will be determined by the admin after you apply.
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-earn-money w-100" data-bs-toggle="modal" data-bs-target="#applyModal">
                                            <i class="fas fa-paper-plane me-2"></i>Apply And Earn
                                        </button>
                                    </form>
                                    <button class="btn btn-outline-primary copy-link-btn"
                                            data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($item['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
                                            <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                        <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
                                    </button>
                                </div>
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-lock me-1"></i> Your information is secure and encrypted
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="product-card p-4">
                <h4 class="mb-3">Application Process</h4>
                <div class="process-steps">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h5>Apply Online</h5>
                        <p>Submit your application with basic details</p>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <h5>Documentation</h5>
                        <p>Submit KYC Documents</p>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <h5>Verify Your Details</h5>
                        <p>Our team will review and confirm your application</p>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <h5>Confirmation</h5>
                        <p>You will receive a confirmation email/SMS after successful registration.</p>
                    </div>
                </div>
            </div>
            
            <div class="product-card p-4 mt-4">
                <h3 class="section-title">Frequently Asked Questions</h3>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-chevron-down me-2"></i> What documents are required for the application?
                    </div>
                    <div class="faq-answer">
                        <p>You'll need to provide identification documents (Aadhaar/PAN), proof of income (salary slips or bank statements), and address verification. Specific requirements will be mentioned during the application process.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <i class="fas fa-chevron-down me-2"></i> How long does the approval process take?
                    </div>
                    <div class="faq-answer">
                        <p>The approval process typically takes 24-48 hours. You will receive a notification once your application is reviewed. For pre-approved customers, the process can be instant.</p>
                    </div>
                </div>
            </div>
            
         
        <?php endif; ?>
    </div>
       <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // FAQ accordion functionality
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                const icon = question.querySelector('i');
                
                if (answer.style.display === 'block') {
                    answer.style.display = 'none';
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                } else {
                    answer.style.display = 'block';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
            });
        });
        
        function changeImage(imagePath) {
            // Change the main image
            document.getElementById('mainImage').src = imagePath;
            
            // Update active thumbnail
            const thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumb => {
                thumb.classList.remove('active');
            });
            
            // Add active class to clicked thumbnail
            event.target.classList.add('active');
        }
        
        // Copy link functionality (same as all_offers)
        document.addEventListener('DOMContentLoaded', function() {
            // Copy link functionality
            document.querySelectorAll('.copy-link-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const link = this.getAttribute('data-link');
                    if (link) {
                        navigator.clipboard.writeText(link).then(() => {
                            // Show feedback to user
                            const originalText = this.innerHTML;
                            this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                            this.classList.remove('btn-outline-primary');
                            this.classList.add('btn-success');
                            
                            // Reset button after 2 seconds
                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.classList.remove('btn-success');
                                this.classList.add('btn-outline-primary');
                            }, 2000);
                        }).catch(err => {
                            console.error('Failed to copy: ', err);
                            alert('Failed to copy link. Please try again.');
                        });
                    }
                });
            });
            
            // Handle Next button click in modal
            const nextButton = document.getElementById('nextButton');
            if (nextButton) {
                nextButton.addEventListener('click', function() {
                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                    modal.hide();
                    
                    // Submit the form
                    const form = document.querySelector('form[method="POST"]');
                    if (form) {
                        // Create hidden input for apply_now
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'apply_now';
                        input.value = '1';
                        form.appendChild(input);
                        
                        // Submit the form
                        form.requestSubmit();
                    }
                });
            }
        });
    </script>
    
    <!-- Apply and Earn Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">Support Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>If you face any issue while using an offer, please contact our 24/7 Support Team.</p>
                    <p>Just send us a message with a screenshot, and you'll get a reply within 10 minutes.</p>
                    <p>We're always here to help you! 💬⚡</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="nextButton">Next</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>