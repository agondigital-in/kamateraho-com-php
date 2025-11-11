<?php
session_start();
include 'config/db.php';
include 'config/app.php';
include 'includes/price_helper.php'; // Include price helper functions

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

// Initialize variables
$all_offers = [];
$categories = [];
$error = '';

// Check if database connection is available
if ($pdo) {
    try {
        // Fetch all categories for filter dropdown
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch all offers with optional filtering
        $sql = "SELECT o.*, c.name as category_name FROM offers o LEFT JOIN categories c ON o.category_id = c.id WHERE o.is_active = 1";
        $params = [];
        
        // Add category filter if selected
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $sql .= " AND o.category_id = :category_id";
            $params[':category_id'] = $_GET['category'];
        }
        
        // Add search filter if provided
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $sql .= " AND (o.title LIKE :search OR o.description LIKE :search)";
            $params[':search'] = '%' . $_GET['search'] . '%';
        }
        
        // Add sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY o.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY o.price DESC";
                break;
            case 'oldest':
                $sql .= " ORDER BY o.created_at ASC";
                break;
            case 'newest':
            default:
                $sql .= " ORDER BY o.created_at DESC";
                break;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $all_offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $error = "Error fetching offers: " . $e->getMessage();
    }
} else {
    $error = "Database connection failed.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Offers - cashbacklo</title>
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
        .btn-earn-money {
            border: 2px solid #0d6efd !important;
            background: linear-gradient(135deg, #4361ee, #3a0ca3) !important;
            color: white !important;
        }
        
        .btn-earn-money:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3) !important;
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
            object-fit: contain;
            width: 100%;
            height: 200px;
            padding: 10px;
        }
        
        .offer-card-col .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .search-box {
            max-width: 300px;
        }
        
        /* Spin & Earn Button */
        .spin-btn {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(255, 154, 158, 0.4);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .spin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 154, 158, 0.6);
        }
        
        .spin-btn:active {
            transform: translateY(0);
        }
        
        /* Enhanced Spin Button on Offers Page */
        #spinBtn {
            background: linear-gradient(135deg, #ff6b6b, #ffa502);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.5);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }
        
        #spinBtn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        #spinBtn:hover::before {
            left: 100%;
        }
        
        #spinBtn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.7);
        }
        
        #spinBtn:active {
            transform: translateY(1px);
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
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
        }
        
        @media (max-width: 576px) {
            .offer-card-col {
                flex: 0 0 100%;
                max-width: 100%;
            }
            
            .btn-earn-money, .btn-outline-primary {
                font-size: 0.75rem !important;
                padding: 0.25rem 0.4rem !important;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-primary">All Available Offers</h1>
            <div class="d-flex align-items-center gap-2">
                <button id="spinBtn" class="spin-btn" data-bs-toggle="modal" data-bs-target="#spinModal">
                    <i class="fas fa-sync-alt"></i> Spin & Earn
                </button>
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php else: ?>
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Offers</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" 
                               placeholder="Search by title or description">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="sort" class="form-label">Sort By</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="newest" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'oldest') ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Offers Count -->
            <div class="mb-3">
                <p class="text-muted">
                    Showing <?php echo count($all_offers); ?> offer<?php echo count($all_offers) != 1 ? 's' : ''; ?>
                </p>
            </div>
            
            <!-- Display Offers -->
            <?php if (empty($all_offers)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>No Offers Found</h4>
                    <p>There are currently no offers available matching your criteria. Please try different filters.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($all_offers as $offer): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12 offer-card-col">
                            <div class="card border-0 shadow-sm h-100">
                                <?php 
                                // Determine image source
                                $image_src = '';
                                if (!empty($offer['image'])) {
                                    if (preg_match('/^https?:\/\//i', $offer['image'])) {
                                        $image_src = $offer['image'];
                                    } else {
                                        $image_src = htmlspecialchars(normalize_image($offer['image']));
                                    }
                                }
                                ?>
                                <?php if (!empty($image_src)): ?>
                                    <img src="<?php echo $image_src; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>">
                                <?php else: ?>
                                    <div class="bg-light" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0" style="font-size: 1rem;"><?php echo htmlspecialchars($offer['title']); ?></h5>
                                        <?php if (!empty($offer['category_name'])): ?>
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($offer['category_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="card-text flex-grow-1" style="font-size: 0.85rem;">
                                        <?php echo htmlspecialchars(mb_strimwidth($offer['description'], 0, 80, '...')); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="price-tag">
                                            <span class="text-muted text-decoration-line-through me-1">
                                                <?php echo !empty($offer['original_price']) ? '₹' . number_format($offer['original_price'], 0) : ''; ?>
                                            </span>
                                            <strong class="text-success"><?php echo !empty($offer['price']) ? display_price($offer['price'], $offer['price_type'] ?? 'fixed') : '0'; ?></strong>
                                        </div>
                                    </div>
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
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
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
            
            // Update URL when sort or filter changes without page reload
            const filterForm = document.querySelector('form');
            if (filterForm) {
                const inputs = filterForm.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        filterForm.submit();
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
</body>
</html>