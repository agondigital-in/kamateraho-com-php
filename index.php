<?php
session_start();
include 'config/db.php';
include 'config/app.php';

// Normalize image path to an absolute URL using BASE_URL
function normalize_image($path) {
    if (!$path) return '';
    // If already absolute URL, return as-is
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    // Remove leading ../ if present from legacy stored paths
    $path = preg_replace('#^\.\./#', '', $path);
    // Ensure no leading slash issues
    $path = ltrim($path, '/');  
    // Build absolute URL
    return url($path);
}

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if database connection is available
if ($pdo) {
    // Fetch all categories
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error fetching categories: " . $e->getMessage();
        $categories = [];
    }
    
    // Fetch active credit cards
    try {
        $stmt = $pdo->query("SELECT * FROM credit_cards WHERE is_active = 1 ORDER BY created_at DESC LIMIT 4");
        $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $credit_cards = [];
    }
    
    // Fetch specific categories by ID and their offers
    try {
        // Fetch category with ID=8 for kotak811 section and its offers
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = 8");
        $stmt->execute();
        $kotak_category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($kotak_category) {
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = 8 AND is_active = 1 ORDER BY created_at DESC LIMIT 4");
            $stmt->execute();
            $kotak_offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $kotak_offers = [];
        }
        
        // Fetch category with ID=9 for ICICI Life Insurance section and its offers
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = 9");
        $stmt->execute();
        $icici_category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($icici_category) {
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = 9 AND is_active = 1 ORDER BY created_at DESC LIMIT 4");
            $stmt->execute();
            $icici_offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $icici_offers = [];
        }
        
        // Fetch category with ID=10 for Bajaj Insta EMI section and its offers
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = 10");
        $stmt->execute();
        $bajaj_category = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($bajaj_category) {
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = 10 AND is_active = 1 ORDER BY created_at DESC LIMIT 4");
            $stmt->execute();
            $bajaj_offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $bajaj_offers = [];
        }
    } catch(PDOException $e) {
        $kotak_offers = [];
        $icici_offers = [];
        $bajaj_offers = [];
    }
} else {
    $categories = [];
    $credit_cards = [];
    $kotak_offers = [];
    $icici_offers = [];
    $bajaj_offers = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KamateRaho - CashKaro.com Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .btn-earn-money {
            border: 2px solid #0d6efd !important; /* Blue border */
            background: linear-gradient(135deg, #4361ee, #3a0ca3) !important; /* Matching gradient */
            color: white !important;
        }
        
        .btn-earn-money:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3) !important;
        }
        
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
        
        /* Responsive improvements for Trending Promotion Tasks */
        .offer-card-col {
            display: flex;
            flex-direction: column;
        }
        
        .offer-card-col .card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .offer-card-col .card-img-top {
            object-fit: cover;
            width: 100%;
        }
        
        .offer-card-col .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Responsive improvements for mobile */
        @media (max-width: 768px) {
            .category-card-wrapper {
                width: 120px;
                margin-right: 10px;
            }
            
            .category-card .rounded-circle {
                width: 90px !important;
                height: 90px !important;
            }
            
            .offer-card-col {
                flex: 0 0 50%;
                max-width: 50%;
            }
            
            .filter-sort-container {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .filter-sort-container .form-select {
                width: 100%;
                margin-bottom: 10px;
            }
            
            /* Increase image height on tablets */
            .offer-card-col .card-img-top {
                height: 260px !important;
            }
        }
        
        @media (max-width: 576px) {
            .offer-card-col {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .category-card-wrapper {
                width: 100px;
                margin-right: 8px;
            }
            
            .category-card .rounded-circle {
                width: 80px !important;
                height: 80px !important;
            }
            
            .btn-earn-money, .btn-outline-primary {
                font-size: 0.75rem !important;
                padding: 0.25rem 0.4rem !important;
            }
            
            /* Further increase image height on small screens */
            .offer-card-col .card-img-top {
                height: 180px !important;
            }
            
            /* Reduce title font size on small screens */
            .offer-card-col .card-title {
                font-size: 0.8rem !important;
            }
            
            /* Adjust price tag font size */
            .price-tag {
                font-size: 1rem !important;
            }
        }
        
        @media (max-width: 400px) {
            /* Further increase image height on very small screens */
            .offer-card-col .card-img-top {
                height: 239px !important;
            }
            
            /* Further reduce title font size */
            .offer-card-col .card-title {
                font-size: 0.75rem !important;
            }
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
                    <p>Share your referral link with friends and earn ₹3 for each successful referral!</p>
                    
                    <div class="referral-link-box">
                        <?php
                        $base_url = "https://kamateraho.com/";
                        $referral_link = $base_url . "register.php?ref=" . $_SESSION['user_id'];
                        ?>
                        <div class="referral-link" id="referralLink"><?php echo $referral_link; ?></div>
                    </div>
                    
                    <button class="copy-btn" id="copyReferralBtn">
                        <i class="fas fa-copy me-2"></i>Copy Referral Link
                    </button>
                    
                    <div class="social-share">
                        <a href="https://api.whatsapp.com/send?text=Join KamateRaho and earn money from home! Register using my referral link: <?php echo urlencode($referral_link); ?>" target="_blank" class="social-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($referral_link); ?>" target="_blank" class="social-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=Join KamateRaho and earn money from home! Register using my referral link: <?php echo urlencode($referral_link); ?>" target="_blank" class="social-btn twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://t.me/share/url?url=<?php echo urlencode($referral_link); ?>&text=Join KamateRaho and earn money from home!" target="_blank" class="social-btn telegram">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Banner Section -->
    <div class="banner-section py-4 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300494/6_ftxkhz.png" class="card-img-top" alt="Banner 1">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300532/1_v9r0lh.png" class="card-img-top" alt="Banner 2">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300537/2_kgswae.png" class="card-img-top" alt="Banner 3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300727/3_rgraak.png" class="card-img-top" alt="Banner 4">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300742/4_g3f3wr.png" class="card-img-top" alt="Banner 5">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300752/5_zoqfoa.png" class="card-img-top" alt="Banner 6">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (!$pdo): ?>
            <div class="alert alert-warning">
                <h4>Database Not Initialized</h4>
                <p>Please run the database initialization script first:</p>
                <a href="init.php" class="btn btn-primary">Initialize Database</a>
            </div>
        <?php else: ?>
            <!-- Categories Section -->
            <section id="categories" class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0 text-primary">Best Promotion Tasks For You To Start</h2>
                    <a href="#" class="text-decoration-none"></a>
                </div>
                
                <?php if (empty($categories)): ?>
                    <div class="alert alert-info text-center">
                        No categories available yet. Please check back later.
                    </div>
                <?php else: ?>
                    <div class="scrolling-wrapper">
                        <div class="scrolling-content" id="categories-scroll">
                            <?php 
                            // Define category images (using sample images for now)
                           $category_images = [
                                8 => "https://i.ytimg.com/vi/r4u5K-jkdxM/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLBwmmE48h_3VJMLH5dnXHYzO2ESmw",
                                9 => "https://images.moneycontrol.com/static-mcnews/2023/04/ICICI-Prudential-life.png?impolicy=website&width=770&height=431",
                                10 => "https://cardinsider.com/wp-content/uploads/2025/04/Understanding-Kotak-811-Zero-Balance-Savings-Account.webp"
                            ];
                            
                            // Create category items array
                            $category_items = [];
                            foreach (array_slice($categories, 0, 12) as $category): 
                                // Use category photo if available, otherwise use default image
                                $image_url = !empty($category['photo']) ? htmlspecialchars($category['photo']) : (isset($category_images[$category['id']]) ? $category_images[$category['id']] : "https://asset20.ckassets.com/wp-content/uploads/2023/02/Others-1.png");
                                $category_items[] = [
                                    'id' => $category['id'],
                                    'name' => $category['name'],
                                    'price' => $category['price'],
                                    'image_url' => $image_url
                                ];
                            endforeach;
                            
                            // Display items twice for seamless looping
                            for ($i = 0; $i < 2; $i++):
                                foreach ($category_items as $category): ?>
                                    <div class="category-card-wrapper">
                                        <div class="card category-card border-0 shadow-sm overflow-hidden text-center">
                                            <div class="position-relative">
                                                <!-- Circular category image -->
                                                <div class="d-flex justify-content-center align-items-center" style="height: 150px;">
                                                    <img src="<?php echo $category['image_url']; ?>" 
                                                         class="rounded-circle" 
                                                         alt="<?php echo htmlspecialchars($category['name']); ?>" 
                                                         style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #1a2a6c;">
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <!-- Price below image -->
                                                <?php if (!empty($category['price'])): ?>
                                                    <div class="price-tag mt-2" style="font-weight: 700; color: #28a745; font-size: 1.1rem;">
                                                        ₹<?php echo number_format($category['price'], 2); ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Category title below price -->
                                                <h6 class="card-title mt-2 mb-0 text-center" style="font-weight: 600; color: #1a2a6c;">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </h6>
                                                <a href="category.php?id=<?php echo $category['id']; ?>" class="stretched-link"></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;
                            endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Trending Promotion Tasks -->
            <div class="text-start mb-4">
                <h2 class="text-primary">Trending Promotion Tasks</h2>
                <!-- Filter and Sort Options -->
                <div class="d-flex justify-content-between align-items-center mb-3 filter-sort-container">
                    <form method="GET" class="d-flex gap-2 w-100">
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                        </select>
                    </form>
                </div>
            </div>
            
            <!-- Display uploaded offers in Trending Promotion Tasks section -->
            <?php
            // Fetch all offers for display in Trending Promotion Tasks with sorting
            try {
                // Default sort order is price high to low
                $sort_order = "price DESC";
                if (isset($_GET['sort'])) {
                    switch ($_GET['sort']) {
                        case 'price_asc':
                            $sort_order = "price ASC";
                            break;
                        case 'newest':
                            $sort_order = "created_at DESC";
                            break;
                        case 'oldest':
                            $sort_order = "created_at ASC";
                            break;
                        case 'price_desc':
                        default:
                            $sort_order = "price DESC";
                            break;
                    }
                }
                
                $stmt = $pdo->query("SELECT * FROM offers WHERE is_active = 1 ORDER BY " . $sort_order);
                $all_offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                $all_offers = [];
            }
            ?>
            
            <?php if (empty($all_offers)): ?>
                <div class="alert alert-info text-center">
                    No offers available yet. Please check back later.
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach (array_slice($all_offers, 0, 12) as $offer): ?>
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 offer-card-col">
                            <div class="card border-0 shadow-sm h-100">
                                <?php 
                                // Determine image source (using same approach as product_details.php)
                                $image_src = '';
                                if (!empty($offer['image'])) {
                                    // Check if it's an absolute URL
                                    if (preg_match('/^https?:\/\//i', $offer['image'])) {
                                        $image_src = $offer['image'];
                                    } 
                                    // For local files, use the direct path
                                    else {
                                        $image_src = htmlspecialchars($offer['image']);
                                    }
                                }
                                
                                // Display image or fallback
                                if (!empty($image_src)): ?>
                                    <img src="<?php echo $image_src; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <!-- Amount/Price below image -->
                                    <?php if (!empty($offer['price'])): ?>
                                        <div class="price-tag mb-2" style="font-weight: 700; color: #28a745; font-size: 1.1rem;">
                                            ₹<?php echo number_format($offer['price'], 2); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Title below amount -->
                                    <h5 class="card-title text-center mb-3" style="font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($offer['title']); ?>
                                    </h5>
                                    
                                    <!-- Two buttons below title -->
                                    <div class="d-flex gap-2 mt-auto">
                                        <a href="product_details.php?id=<?php echo $offer['id']; ?>" class="btn btn-earn-money flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;">Earn Amount</a>
                                        <button class="btn btn-outline-primary copy-link-btn flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;"
                                                data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($offer['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
                                                <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                            <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Best Life Insurance Free Credit Cards -->
            <section class="mb-5">
               
                
                <?php if (empty($credit_cards)): ?>
                   
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($credit_cards as $card): ?>
                            <div class="col-md-3 col-sm-6 offer-card-col">
                                <div class="card border-0 shadow-sm h-100">
                                    <img src="<?php echo htmlspecialchars(normalize_image($card['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($card['title']); ?>" style="height: 180px; object-fit: cover;">
                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Title -->
                                        <h5 class="card-title text-center mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($card['title']); ?></h5>
                                        <div class="d-flex gap-2 mt-auto">
                                            <a href="product_details.php?id=<?php echo $card['id']; ?>&type=card" class="btn btn-earn-money flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;">Earn Amount</a>
                                            <button class="btn btn-outline-primary copy-link-btn flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;"
                                                    data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($card['link'] . $_SESSION['user_id']) : ''; ?>"
                                                    <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Kotak811 Section -->
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                 
                    <a href="category.php?id=8" class="text-decoration-none"></a>
                </div>
                
                <?php if (empty($kotak_offers)): ?>
                   
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($kotak_offers as $offer): ?>
                            <div class="col-md-3 col-sm-6 offer-card-col">
                                <div class="card border-0 shadow-sm h-100">
                                    <?php if (!empty($offer['image'])): ?>
                                        <img src="<?php echo htmlspecialchars(normalize_image($offer['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Title -->
                                        <h5 class="card-title text-center mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($offer['title']); ?></h5>
                                        <div class="d-flex gap-2 mt-auto">
                                            <a href="product_details.php?id=<?php echo $offer['id']; ?>" class="btn btn-earn-money flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;">Earn Amount</a>
                                            <button class="btn btn-outline-primary copy-link-btn flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;"
                                                    data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($offer['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
                                                    <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- ICICI Life Insurance Section -->
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
              
                    <a href="category.php?id=9" class="text-decoration-none"></a>
                </div>
                
                <?php if (empty($icici_offers)): ?>
                  
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($icici_offers as $offer): ?>
                            <div class="col-md-3 col-sm-6 offer-card-col">
                                <div class="card border-0 shadow-sm h-100">
                                    <?php if (!empty($offer['image'])): ?>
                                        <img src="<?php echo htmlspecialchars(normalize_image($offer['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Title -->
                                        <h5 class="card-title text-center mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($offer['title']); ?></h5>
                                        <div class="d-flex gap-2 mt-auto">
                                            <a href="product_details.php?id=<?php echo $offer['id']; ?>" class="btn btn-earn-money flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;">Earn Amount</a>
                                            <button class="btn btn-outline-primary copy-link-btn flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;"
                                                    data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($offer['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
                                                    <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
            <!-- Bajaj Insta EMI Section -->
            <section class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                 
                    <a href="category.php?id=10" class="text-decoration-none"></a>
                </div>
                
                <?php if (empty($bajaj_offers)): ?>
                   
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($bajaj_offers as $offer): ?>
                            <div class="col-md-3 col-sm-6 offer-card-col">
                                <div class="card border-0 shadow-sm h-100">
                                    <?php if (!empty($offer['image'])): ?>
                                        <img src="<?php echo htmlspecialchars(normalize_image($offer['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <!-- Product Title -->
                                        <h5 class="card-title text-center mb-1" style="font-size: 0.9rem;"><?php echo htmlspecialchars($offer['title']); ?></h5>
                                        <div class="d-flex gap-2 mt-auto">
                                            <a href="product_details.php?id=<?php echo $offer['id']; ?>" class="btn btn-earn-money flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;">Earn Amount</a>
                                            <button class="btn btn-outline-primary copy-link-btn flex-grow-1" style="font-size: 0.85rem; padding: 0.375rem 0.5rem;"
                                                    data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($offer['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
                                                    <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                                <?php echo isset($_SESSION['user_id']) ? 'Refer & Earn' : 'Login to Copy'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
            
           
        <?php endif; ?>
    </div> <!-- End of container -->
    

    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Referral Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Copy link functionality for existing buttons
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
    
    <!-- Footer -->
    <footer class="bg-dark text-white pt-4 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>KamateRaho</h5>
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
                <p>&copy; 2025 KamateRaho. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>