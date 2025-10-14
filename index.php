<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cashbacklo - CashKaro.com Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            margin-top: 30px;
            padding: 30px;
        }
        
        .section-title {
            color: #1a2a6c;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 70px;
            height: 4px;
            background: linear-gradient(90deg, #1a2a6c, #f7b733);
            border-radius: 2px;
        }
        
        .btn-earn-money {
            border: 2px solid #0d6efd !important;
            background: linear-gradient(135deg, #4361ee, #3a0ca3) !important;
            color: white !important;
            border-radius: 30px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }
        
        .btn-earn-money:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4) !important;
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
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        
        .offer-card-col .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
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
            
            .main-container {
                padding: 20px 15px;
                margin-top: 20px;
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
                height: 283px !important;
            }
            
            /* Reduce title font size on small screens */
            .offer-card-col .card-title {
                font-size: 0.8rem !important;
            }
            
            /* Adjust price tag font size */
            .price-tag {
                font-size: 1rem !important;
            }
            
            .section-title {
                font-size: 1.5rem;
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

        /* Retailer-style cards for categories */
        .retailer-card { 
            border-radius: 15px; 
            background: #fff; 
            position: relative; 
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            border: none;
        }
        
        .retailer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .retailer-card .logo-wrap { 
            height: 120px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background:#ffffff; 
        }
        
        .retailer-card .logo-wrap img { 
            max-height: 100%; 
            max-width: 100%; 
            width: 100%; 
            object-fit: contain; 
        }
        
        .retailer-ribbon { 
            position: absolute; 
            top: 12px; 
            left: 12px; 
            background: linear-gradient(135deg, #e31b53, #ff6b6b); 
            color: #fff; 
            font-size: .7rem; 
            font-weight: 800; 
            padding: .25rem .5rem; 
            border-radius: 4px; 
            text-transform: uppercase; 
            letter-spacing: .3px; 
        }
        
        .you-earn-pill { 
            display: inline-block; 
            background: linear-gradient(135deg, #f0f2f5, #e2e8f0); 
            color: #1a2a6c; 
            border-radius: 999px; 
            padding: .2rem .6rem; 
            font-size: .7rem; 
            font-weight: 700; 
        }
        
        .profit-text { 
            font-weight: 800; 
            color: #111827; 
            margin: .35rem 0 0; 
        }
        
        .btn-share { 
            background: linear-gradient(135deg, #22c55e, #16a34a); 
            color: #fff; 
            border: none; 
            border-radius: 999px; 
            font-weight: 800; 
            transition: all 0.3s ease;
        }
        
        .btn-share:hover { 
            background: linear-gradient(135deg, #16a34a, #15803d); 
            color: #fff; 
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);
        }
        
        .btn-copy-outline { 
            background: #fff; 
            border: 2px solid #d1d5db; 
            color: #111827; 
            border-radius: 999px; 
            font-weight: 800; 
            transition: all 0.3s ease;
        }
        
        .btn-copy-outline:hover { 
            background: #f9fafb; 
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .category-card-wrapper { 
            width: 280px; 
            margin-right: 16px; 
        }
        
        .scrolling-wrapper { 
            overflow: hidden; 
        }
        
        .scrolling-content { 
            display: flex; 
        }
        
        @media (max-width: 576px) {
            .category-card-wrapper { 
                width: 240px; 
                margin-right: 12px; 
            }
        }

        /* Flash deals card styles (Trending Promotion Tasks) */
        .flash-card { 
            border-radius: 15px; 
            overflow: hidden; 
            background: #fff; 
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .flash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .flash-banner { 
            background: #ffffff; 
            height: 260px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            position: relative; 
        }
        
        .flash-banner img { 
            max-height: 100%; 
            max-width: 100%; 
            width: auto; 
            height: auto; 
            object-fit: contain; 
            border-radius: 0; 
            box-shadow: none; 
        }
        
        .flash-pill { 
            position: absolute; 
            right: 12px; 
            top: 12px; 
            background: #fff; 
            color: #ef4444; 
            font-weight: 800; 
            font-size: .7rem; 
            padding: .2rem .5rem; 
            border-radius: 999px; 
            letter-spacing: .2px; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .deal-strip { 
            background: #ef4444; 
            color: #fff; 
            font-weight: 800; 
            font-size: .75rem; 
            padding: .35rem .6rem; 
            text-transform: uppercase; 
            letter-spacing: .3px; 
        }
        
        .meta { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: .5rem; 
            padding: .6rem .75rem; 
        }
        
        .meta small { 
            display: block; 
            color: #6b7280; 
            font-weight: 700; 
            font-size: .7rem; 
        }
        
        .meta .val { 
            font-weight: 900; 
            color: #111827; 
        }
        
        .price-old { 
            color: #9ca3af; 
            text-decoration: line-through; 
            font-weight: 700; 
            margin-right: .35rem; 
        }
        
        .actions { 
            padding: .6rem .75rem .9rem; 
        }
        
        .actions .btn { 
            white-space: nowrap; 
        }
        
        /* Desktop: inline 3 buttons */
        @media (min-width: 768px) {
            .actions .btn { 
                width: auto; 
            }
        }
        
        /* Mobile: stack buttons full width */
        @media (max-width: 767.98px) {
            .actions .btn { 
                width: 100%; 
            }
        }
        
        .btn-earn-now { 
            background: linear-gradient(135deg,#38bdf8,#0ea5e9); 
            color:#fff; 
            border:none; 
            border-radius: 10px; 
            font-weight: 800; 
            transition: all 0.3s ease;
        }
        
        .btn-earn-now:hover { 
            filter: brightness(1.03); 
            color:#fff; 
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.4);
        }

        /* Ad-style tiles (2 rows x 4 columns) */
        .tile-card { 
            background:#fff; 
            border:1px solid #e5e7eb; 
            border-radius:15px; 
            padding:15px; 
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .tile-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .tile-grid { 
            display:grid; 
            grid-template-columns: repeat(2, 1fr);   
        }
        
        .tile { 
            display:flex; 
            flex-direction:column; 
            gap:6px; 
        }
        
        .tile-thumb { 
            width:100%; 
            aspect-ratio:1/1; 
            background:#fff; 
            border:1px solid #e5e7eb; 
            border-radius:8px; 
            overflow:hidden; 
            display:flex; 
            align-items:center; 
            justify-content:center; 
        }
        
        .tile-thumb img { 
            width:100%; 
            height:100%; 
            object-fit:contain; 
        }
        
        .tile-caption { 
            font-size:.82rem; 
            color:#374151; 
            line-height:1.15; 
            display:-webkit-box; 
            -webkit-line-clamp:2; 
            -webkit-box-orient:vertical; 
            overflow:hidden; 
        }
        
        .tile-see-all { 
            display:inline-block; 
            margin-top:8px; 
            color:#0a58ca; 
            font-weight:700; 
            font-size:.9rem; 
            text-decoration:none; 
        }
        
        .tile-see-all:hover { 
            text-decoration:underline; 
        }
        
        @media (max-width: 767.98px) { 
            .tile-grid { 
                grid-template-columns: repeat(2, 1fr); 
            } 
        }
        
        /* Vertical separators between columns on md+ */
        @media (min-width: 768px) { 
            .tile-col { 
                position:relative; 
            } 
            .tile-col + .tile-col { 
                border-left:1px solid #e5e7eb; 
            } 
        }
        
        /* Additional styles to ensure images are not cut off */
        .tile-thumb {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        
        .tile-thumb img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }
        
        .tile-thumb .image-placeholder {
            font-size: 2rem;
            color: #ccc;
        }
        
        /* Banner carousel styling */
        .banner-section {
            margin-top: 20px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .carousel-item .card {
            border-radius: 15px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(26, 42, 108, 0.5);
            border-radius: 50%;
            padding: 20px;
        }
        
        .carousel-indicators [data-bs-target] {
            background-color: #1a2a6c;
            opacity: 0.5;
        }
        
        .carousel-indicators .active {
            opacity: 1;
        }
        
        /* Filter and sort container */
        .filter-sort-container {
            background: rgba(255, 255, 255, 0.7);
            padding: 15px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-select {
            border-radius: 30px;
            border: 2px solid #e2e8f0;
            padding: 8px 15px;
        }
        
        .form-select:focus {
            border-color: #1a2a6c;
            box-shadow: 0 0 0 0.25rem rgba(26, 42, 108, 0.15);
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
                        $base_url = "https://cashbacklo.com/";
                        $referral_link = $base_url . "register.php?ref=" . $_SESSION['user_id'];
                        ?>
                        <div class="referral-link" id="referralLink"><?php echo $referral_link; ?></div>
                    </div>
                    
                    <button class="copy-btn" id="copyReferralBtn">
                        <i class="fas fa-copy me-2"></i>Copy Referral Link
                    </button>
                    
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
                </div>
            </div>
        </div>
    </div>
   
    <!-- Banner Section -->
    <div class="banner-section py-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300494/6_ftxkhz.png" class="card-img-top" alt="Banner 1" style="object-fit: contain; height: 200px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300532/1_v9r0lh.png" class="card-img-top" alt="Banner 2" style="object-fit: contain; height: 200px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300537/2_kgswae.png" class="card-img-top" alt="Banner 3" style="object-fit: contain; height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300727/3_rgraak.png" class="card-img-top" alt="Banner 4" style="object-fit: contain; height: 200px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300742/4_g3f3wr.png" class="card-img-top" alt="Banner 5" style="object-fit: contain; height: 200px;">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1759300752/5_zoqfoa.png" class="card-img-top" alt="Banner 6" style="object-fit: contain; height: 200px;">
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
    
    <div class="container main-container">
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
                    <h2 class="mb-0 section-title">Best Promotion Tasks For You To Start</h2>
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
                                        <div class="card retailer-card border-0 shadow-sm text-center p-3 h-100">
                                            <span class="retailer-ribbon">Top</span>
                                            <div class="logo-wrap mb-2">
                                                <img src="<?php echo $category['image_url']; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" style="object-fit: contain; max-height: 120px;">
                                            </div>
                                            <div class="mb-2">
                                                <span class="you-earn-pill">YOU EARN</span>
                                                <div class="profit-text">Upto <?php echo !empty($category['price']) ? ''.number_format($category['price'], 0) : 'Best'; ?> Profit</div>
                                            </div>
                                            <?php 
                                                $share_url = 'category.php?id=' . $category['id'];
                                                $wa_text = 'Check this offer: ' . $share_url;
                                            ?>
                                            <div class="d-grid gap-2 mt-auto">
                                                <a target="_blank" href="https://api.whatsapp.com/send?text=<?php echo urlencode($wa_text); ?>" class="btn btn-share">
                                                    <i class="fab fa-whatsapp me-1"></i> SHARE NOW
                                                </a>
                                                <button class="btn btn-copy-outline copy-link-btn" data-link="<?php echo htmlspecialchars($share_url); ?>">
                                                    COPY LINK
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;
                            endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

<!-- More Picks For You (Relocated just below navbar) -->
    <?php if (isset($pdo)) : ?>
    <?php
    // Ensure $all_offers is loaded when this section is above main queries
    if (!isset($all_offers)) {
        try {
            $stmt = $pdo->query("SELECT * FROM offers WHERE is_active = 1 ORDER BY created_at DESC");
            $all_offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $all_offers = [];
        }
    }
    ?>
    <?php if (!empty($all_offers)): ?>
    <div class="container mt-3">
        <section class="mb-4">
            <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0 section-title">More Picks For You</h2>
            </div> -->
            <div class="row g-3">
                <?php 
                    // Group offers into chunks of 4 for each tile card
                    $tile_groups_top = array_chunk($all_offers, 4);
                    // Show only first 3 groups (3 boxes)
                    foreach (array_slice($tile_groups_top, 0, 3) as $group): ?>
                    <div class="col-lg-4 col-md-6 col-12 tile-col">
                        <div class="tile-card h-100">
                            <div class="tile-grid">
                                <?php foreach ($group as $g): 
                                    // Choose image source similar to earlier logic
                                    $tile_img = '';
                                    if (!empty($g['image'])) {
                                        if (preg_match('/^https?:\/\//i', $g['image'])) {
                                            $tile_img = $g['image'];
                                        } else {
                                            $tile_img = htmlspecialchars($g['image']);
                                        }
                                    }
                                    $caption = !empty($g['title']) ? $g['title'] : 'Offer';
                                ?>
                                <a href="product_details.php?id=<?php echo $g['id']; ?>" class="tile text-decoration-none">
                                    <div class="tile-thumb">
                                        <?php if (!empty($tile_img)): ?>
                                            <img src="<?php echo $tile_img; ?>" alt="<?php echo htmlspecialchars($caption); ?>" loading="lazy">
                                        <?php else: ?>
                                            <div class="image-placeholder">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="tile-caption"><?php echo htmlspecialchars(mb_strimwidth($caption, 0, 28, '…')); ?></div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="all_offers.php" class="tile-see-all">See all offers</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <?php endif; ?>
    <?php endif; ?>

            
            <!-- Trending Promotion Tasks -->
            <div class="text-start mb-4">
                <h2 class="section-title">Trending Promotion Tasks</h2>
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
                        <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-12 offer-card-col">
                            <div class="flash-card h-100">
                                <?php 
                                // Determine image source
                                $image_src = '';
                                if (!empty($offer['image'])) {
                                    if (preg_match('/^https?:\/\//i', $offer['image'])) {
                                        $image_src = $offer['image'];
                                    } else {
                                        $image_src = htmlspecialchars($offer['image']);
                                    }
                                }
                                ?>
                                <div class="flash-banner">
                                    <?php if (!empty($image_src)): ?>
                                        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="object-fit: contain; width: 100%; height: 100%;">
                                    <?php else: ?>
                                        <div class="text-white fw-bold">Deal</div>
                                    <?php endif; ?>
                                    <span class="flash-pill">Flash Sale</span>
                                </div>
                                
                                <div class="p-2">
                                    <div class="text-dark fw-bold text-center mb-1" style="min-height:40px;font-size:.95rem;">
                                        <?php echo htmlspecialchars($offer['title']); ?>
                                    </div>
                                    <div class="meta">
                                        <div>
                                            <small>Starting From</small>
                                            <div class="val"><?php echo !empty($offer['price']) ? number_format($offer['price'], 0) : '—'; ?></div>
                                        </div>
                                        <div>
                                            <small>Per Sale You Earn</small>
                                            <div class="val"><?php echo !empty($offer['price']) ? number_format($offer['price'], 0) : '—'; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="actions">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <?php $share_text = 'Check this deal: ' . url('product_details.php?id=' . $offer['id']); ?>
                                        <a href="product_details.php?id=<?php echo $offer['id']; ?>" class="btn btn-earn-now flex-fill">EARN NOW</a>
                                        <a class="btn btn-share flex-fill" target="_blank" href="https://api.whatsapp.com/send?text=<?php echo urlencode($share_text); ?>">
                                            <i class="fab fa-whatsapp me-1"></i> SHARE NOW
                                        </a>
                                        <button class="btn btn-copy-outline copy-link-btn flex-fill" 
                                                data-link="<?php echo isset($_SESSION['user_id']) ? htmlspecialchars($offer['redirect_url'] . $_SESSION['user_id']) : ''; ?>"
                                                <?php echo !isset($_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                            <?php echo isset($_SESSION['user_id']) ? 'COPY LINK' : 'Login to Copy'; ?>
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
                                    <img src="<?php echo htmlspecialchars(normalize_image($card['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($card['title']); ?>" style="height: 180px; object-fit: contain; padding: 10px;">
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
                                        <img src="<?php echo htmlspecialchars(normalize_image($offer['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: contain; padding: 10px;">
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
                                        <img src="<?php echo htmlspecialchars(normalize_image($offer['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: contain; padding: 10px;">
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
                                        <img src="<?php echo htmlspecialchars(normalize_image($offer['image'])); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($offer['title']); ?>" style="height: 180px; object-fit: contain; padding: 10px;">
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