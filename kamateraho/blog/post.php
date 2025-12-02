<?php
require_once '../../config/db.php';

// Get slug from URL - either from query string or from path
$slug = '';

// Check if slug is in the URL path
if (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
} else {
    // Parse the URL to get the slug from the path
    $request_uri = $_SERVER['REQUEST_URI'];
    $path_parts = explode('/', trim($request_uri, '/'));
    // Get the last part of the URL as the slug
    $slug = end($path_parts);
    // Remove .php extension if present
    $slug = str_replace('.php', '', $slug);
}

if (empty($slug) || $slug === 'post' || $slug === 'index') {
    header("Location: index.php");
    exit();
}

// Fetch blog post from database
try {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        header("Location: ../../404.php");
        exit();
    }
} catch (PDOException $e) {
    die("Error loading blog post: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - KamateRaho.com</title>
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        
        .blog-header {
            text-align: center;
            padding: 3rem 0;
            background: linear-gradient(135deg, #1a2a6c, #f7b733);
            color: white;
            margin-bottom: 2rem;
        }
        
        .blog-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .blog-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px 3rem;
        }
        
        .blog-meta {
            display: flex;
            gap: 1rem;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .blog-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .blog-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            line-height: 1.8;
        }
        
        .blog-content h1, .blog-content h2, .blog-content h3 {
            color: #1a2a6c;
            margin: 1.5rem 0 1rem;
        }
        
        .blog-content p {
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .blog-content ul, .blog-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .blog-content li {
            margin-bottom: 0.5rem;
        }
        
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 1rem 0;
        }
        
        .blog-content blockquote {
            border-left: 4px solid #1a2a6c;
            padding-left: 1rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: #666;
        }
        
        .blog-content code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        
        .blog-content pre {
            background: #f4f4f4;
            padding: 1rem;
            border-radius: 5px;
            overflow-x: auto;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .blog-header h1 {
                font-size: 2rem;
            }
            
            .blog-image {
                height: 250px;
            }
            
            .blog-content {
                padding: 1rem;
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
    <section class="blog-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <?php if ($post['excerpt']): ?>
                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="blog-container">
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Blog</a>
        
        <div class="blog-meta">
            <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
            <span><i class="far fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
        </div>
        
        <?php if ($post['image_url']): ?>
            <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="blog-image">
        <?php endif; ?>
        
        <div class="blog-content">
            <?php echo $post['content']; ?>
        </div>
    </section>
</body>
</html>
