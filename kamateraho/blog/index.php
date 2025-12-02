<?php
require_once '../../config/db.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

// Fetch blog posts from database
try {
    // Get total count
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
    $total_posts = $count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $per_page);
    
    // Get posts for current page
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE status = 'published' ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error loading blog posts: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - KamateRaho.com</title>
    <meta name="google-site-verification" content="L5OFuMQut1wlaXZQjXlLUO6eqfZprVYYsN1ZMj0MOpM" />
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9867776615304259" crossorigin="anonymous"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        .blog-hero {
            text-align: center;
            padding: 4rem 0;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
        }
        
        .blog-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .blog-section {
            padding: 4rem 0;
            background: #f5f7fa;
        }
        
        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
            align-items: start;
        }
        
        .blog-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .blog-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .blog-card-content {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .blog-card h3 {
            color: #1a2a6c;
            margin-bottom: 0.5rem;
            font-size: 1.3rem;
            line-height: 1.6rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 3.2rem;
            max-height: 3.2rem;
        }
        
        .blog-meta {
            display: flex;
            gap: 1rem;
            color: #666;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .blog-excerpt {
            color: #555;
            margin-bottom: 1rem;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 3.2rem;
            max-height: 3.2rem;
        }
        
        .read-more {
            display: inline-block;
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
        }
        
        .read-more:hover {
            text-decoration: underline;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 5px;
            text-decoration: none;
            color: #1a2a6c;
        }
        
        .pagination .active {
            background: #1a2a6c;
            color: white;
        }
        
        .no-posts {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .blog-hero h1 {
                font-size: 2rem;
            }
            
            .blog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-RMM38DLZLM');
    </script>
</head>
<body>
    <section class="blog-hero">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h1>Our Blog</h1>
            <p>Stay updated with the latest tips, tricks, and news</p>
        </div>
    </section>

    <section class="blog-section">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <?php if (count($posts) > 0): ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $post): ?>
                        <a href="<?php echo htmlspecialchars($post['slug']); ?>" style="text-decoration: none; color: inherit;">
                            <div class="blog-card">
                                <?php if ($post['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                <?php endif; ?>
                                <div class="blog-card-content">
                                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <div class="blog-meta">
                                        <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                                        <span><i class="far fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                                    </div>
                                    <?php if ($post['excerpt']): ?>
                                        <p class="blog-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Next <i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-newspaper" style="font-size: 4rem; color: #ccc; margin-bottom: 1rem;"></i>
                    <h3>No blog posts yet</h3>
                    <p>Check back soon for new content!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
