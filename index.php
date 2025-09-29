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
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = 8 ORDER BY created_at DESC LIMIT 4");
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
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = 9 ORDER BY created_at DESC LIMIT 4");
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
            $stmt = $pdo->prepare("SELECT * FROM offers WHERE category_id = 10 ORDER BY created_at DESC LIMIT 4");
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
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
   
    
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
                                            <img src="https://asset22.ckassets.com/resources/image/staticpage_images/Desktop Banner-1758558959.png" class="card-img-top" alt="Banner 1">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://asset22.ckassets.com/resources/image/staticpage_images/Desktop Banner-1758559004.png" class="card-img-top" alt="Banner 2">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://asset22.ckassets.com/resources/image/staticpage_images/myntradesktopbanner-1758612029.png" class="card-img-top" alt="Banner 3">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://asset22.ckassets.com/resources/image/staticpage_images/Desktop Banner-1758545418.png" class="card-img-top" alt="Banner 4">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://asset22.ckassets.com/resources/image/staticpage_images/amazon_desktopbanner-1758698242.png" class="card-img-top" alt="Banner 5">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://asset22.ckassets.com/resources/image/staticpage_images/Desktop Banner-1756785978.png" class="card-img-top" alt="Banner 6">
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
                                $image_url = isset($category_images[$category['id']]) ? $category_images[$category['id']] : "https://asset20.ckassets.com/wp-content/uploads/2023/02/Others-1.png";
                                $category_items[] = [
                                    'id' => $category['id'],
                                    'name' => $category['name'],
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
                                                <!-- Category title below image -->
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
            </div>
            
            <!-- Best Life Insurance Free Credit Cards -->
            <section class="mb-5">
               
                
                <?php if (empty($credit_cards)): ?>
                    <div class="alert alert-info">
                        <p>No credit cards available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($credit_cards as $card): ?>
                            <div class="col-md-3">
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
                    <div class="alert alert-info">
                       
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($kotak_offers as $offer): ?>
                            <div class="col-md-3">
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
                    <div class="alert alert-info">
                 
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($icici_offers as $offer): ?>
                            <div class="col-md-3">
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
                    <div class="alert alert-info">
                        <p>No offers available in this category at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($bajaj_offers as $offer): ?>
                            <div class="col-md-3">
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
        // Typing effect for hero section
        document.addEventListener('DOMContentLoaded', function() {
            const texts = ["Earn from Home", "Get Instant Payments", "Join Thousands of Users"];
            let count = 0;
            let index = 0;
            let currentText = '';
            let letter = '';
            
            (function type() {
                if (count === texts.length) {
                    count = 0;
                }
                currentText = texts[count];
                letter = currentText.slice(0, ++index);
                
                document.getElementById('typing').textContent = letter;
                if (letter.length === currentText.length) {
                    count++;
                    index = 0;
                    setTimeout(type, 2000); // Wait 2 seconds before next text
                } else {
                    setTimeout(type, 100); // Typing speed
                }
            }());
            
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
        });
    </script>
</body>
</html>