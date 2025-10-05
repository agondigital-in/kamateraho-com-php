<?php
session_start();
include 'config/db.php';

// Get category ID from URL
$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id <= 0) {
    header('Location: index.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle buy now action
if (isset($_GET['buy_offer'])) {
    $offer_id = (int)$_GET['buy_offer'];
    
    try {
        // Fetch offer details
        $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
        $stmt->execute([$offer_id]);
        $offer = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($offer) {
            // Create a withdraw request for this purchase
            $stmt = $pdo->prepare("INSERT INTO withdraw_requests (user_id, amount, upi_id, status, offer_title, offer_description) VALUES (?, ?, ?, 'pending', ?, ?)");
            $upi_id = "purchase@" . $user_id; // Placeholder UPI ID for purchase
            $stmt->execute([$user_id, $offer['price'], $upi_id, $offer['title'], $offer['description']]);
            
            // Redirect to the offer's redirect URL with user ID parameter
            $redirect_url = $offer['redirect_url'];
            if (!empty($redirect_url)) {
                // Add user ID parameter to the redirect URL
                $separator = (strpos($redirect_url, '?') !== false) ? '&' : '?';
                $redirect_url .= $separator . 'user_id=' . $user_id;
                header("Location: " . $redirect_url);
            } else {
                // Fallback to Bajaj Finserv if no redirect URL is set
                $redirect_url = "https://www.bajajfinserv.in/webform/v1/emicard/login?utm_source=Expartner&utm_medium=79&utm_campaign=6111_" . $user_id;
                header("Location: " . $redirect_url);
            }
            exit;
        }
    } catch(PDOException $e) {
        $error = "Error processing purchase: " . $e->getMessage();
    }
}

// Fetch category details
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        header('Location: index.php');
        exit;
    }
} catch(PDOException $e) {
    $error = "Error fetching category: " . $e->getMessage();
    $category = null;
}

// Fetch offers for this category
try {
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = ? AND is_active = 1 ORDER BY created_at DESC");
    $stmt->execute([$category_id]);
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Error fetching offers: " . $e->getMessage();
    $offers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .category-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        
        .offer-card {
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .offer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }
        
        .offer-img {
            height: 200px;
            object-fit: cover;
        }
        
        .price-tag {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: bold;
            display: inline-block;
        }
        
        .breadcrumb {
            background-color: #f8f9fa;
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
        }
        
        /* Enhanced "Earn Money" button styles */
        .btn-earn-money {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            padding: 12px 25px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-earn-money:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
        }
        
        .btn-earn-money:active {
            transform: translateY(1px);
        }
        
        .btn-earn-money::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -60%;
            width: 20px;
            height: 200%;
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(30deg);
            transition: all 0.6s;
        }
        
        .btn-earn-money:hover::after {
            left: 120%;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="category-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bg-transparent p-0">
                            <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page"><?php echo htmlspecialchars($category['name']); ?></li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($category['name']); ?></h1>
                    <p class="lead mb-0">Discover the best offers and deals in this category</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (empty($offers)): ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-box-open fa-3x mb-3"></i>
                <h4>No Offers Available</h4>
                <p class="mb-0">No offers available in this category yet. Please check back later.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($offers as $offer): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card offer-card h-100">
                            <?php if (!empty($offer['image'])): ?>
                                <img src="<?php echo htmlspecialchars($offer['image']); ?>" 
                                     class="card-img-top offer-img" 
                                     alt="<?php echo htmlspecialchars($offer['title']); ?>">
                            <?php else: ?>
                                <div class="bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($offer['title']); ?></h5>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="price-tag">₹<?php echo number_format($offer['price'], 2); ?></div>
                                        <?php if (!empty($offer['redirect_url'])): ?>
                                            <small class="text-muted">
                                                <i class="fas fa-external-link-alt"></i> External Offer
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-grid">
                                        <a href="product_details.php?id=<?php echo $offer['id']; ?>" class="btn btn-earn-money btn-lg">
                                            <i class="fas fa-coins me-2"></i>Earn Money
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>