<?php
$page_title = "Manage Blog";
include '../config/db.php';

// Initialize message variables
$success_message = '';
$error_message = '';

// Check for messages in session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the message
}

// Handle delete request
if (isset($_POST['delete_post'])) {
    $post_number = (int)$_POST['post_number'];
    
    // Delete the blog post file
    $blog_file = "../kamateraho/blog/post{$post_number}.php";
    
    if (file_exists($blog_file) && unlink($blog_file)) {
        // Also remove the post from the blog index
        removeFromBlogIndex($post_number);
        $_SESSION['success_message'] = "Blog post deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to delete blog post!";
    }
    
    // Redirect to the same page to refresh the list
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle edit request - load existing post data
$editing_post = null;
if (isset($_GET['edit'])) {
    $post_number = (int)$_GET['edit'];
    $blog_file = "../kamateraho/blog/post{$post_number}.php";
    
    if (file_exists($blog_file)) {
        $file_content = file_get_contents($blog_file);
        
        // Extract data from the existing post
        $editing_post = [
            'number' => $post_number,
            'title' => '',
            'date' => date('M d, Y'),
            'author' => 'Admin',
            'excerpt' => '',
            'image_url' => '',
            'content' => ''
        ];
        
        // Extract title
        if (preg_match('/<title>(.*?) - KamateRaho\.com<\/title>/', $file_content, $matches)) {
            $editing_post['title'] = $matches[1];
        }
        
        // Extract date
        if (preg_match('/<span><i class="far fa-calendar"><\/i>\s*(.*?)\s*<\/span>/', $file_content, $matches)) {
            $editing_post['date'] = trim($matches[1]);
        }
        
        // Extract excerpt
        if (preg_match('/<p class="blog-excerpt">(.*?)<\/p>/s', $file_content, $matches)) {
            $editing_post['excerpt'] = trim($matches[1]);
        }
        
        // Extract image URL
        if (preg_match('/<img[^>]*src="(.*?)"[^>]*alt="' . preg_quote($editing_post['title']) . '"/', $file_content, $matches)) {
            $editing_post['image_url'] = $matches[1];
        }
        
        // Extract content (everything between blog-content div)
        if (preg_match('/<div class="blog-content">\s*(.*)\s*<\/div>/s', $file_content, $matches)) {
            $content = $matches[1];
            // Remove the back link, image, and other elements that aren't the main content
            $content = preg_replace('/<a href="[^"]*" class="back-link">.*?<\/a>\s*/', '', $content);
            $content = preg_replace('/<img[^>]*class="blog-image"[^>]*>\s*/', '', $content);
            $editing_post['content'] = trim($content);
        }
    }
}

// Handle form submission for creating/updating blog posts
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_post'])) {
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? date('M d, Y');
    $author = $_POST['author'] ?? 'Admin';
    $excerpt = $_POST['excerpt'] ?? '';
    $image_url = $_POST['image_url'] ?? '';
    $content = $_POST['content'] ?? '';
    $post_number = isset($_POST['post_number']) ? (int)$_POST['post_number'] : 0;
    
    // Validate inputs
    if (!empty($title) && !empty($content)) {
        $blog_dir = '../kamateraho/blog/';
        
        if ($post_number > 0) {
            // Update existing post
            $filename = "post{$post_number}.php";
            $filepath = $blog_dir . $filename;
            
            // Update the blog post content
            $post_content = "<?php
// Blog post $post_number: $title
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>$title - KamateRaho.com</title>
    <link rel=\"stylesheet\" href=\"../css/style.css\">
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
    <link href=\"https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">
    <style>
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
            padding: 0 20px;
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
        }
        
        .blog-content h2 {
            color: #1a2a6c;
            margin: 1.5rem 0;
        }
        
        .blog-content p {
            line-height: 1.8;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .blog-content ul, .blog-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .blog-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
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
        }
              /* Header Styles */
        header {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            width: auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .logo span {
            color: #ff6e7f;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            cursor: pointer;
            z-index: 1001;
        }

        .menu-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: #1a2a6c;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        /* Ensure menu is visible on desktop */
        @media (min-width: 769px) {
            nav ul {
                display: flex !important;
            }
        }

        /* Hide mobile menu by default */
        @media (max-width: 768px) {
            nav ul {
                display: none;
            }
            
            nav ul.active {
                display: flex;
            }
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        nav ul li a:hover {
            background: rgba(26, 42, 108, 0.1);
            color: #1a2a6c;
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 15px;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-login {
            background: transparent;
            color: #1a2a6c;
            border: 2px solid #1a2a6c;
        }

        .btn-login:hover {
            background: #1a2a6c;
            color: white;
        }

        .btn-register {
            background: #1a2a6c;
            color: white;
            border: 2px solid #1a2a6c;
        }

        .btn-register:hover {
            background: transparent;
            color: #1a2a6c;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            nav ul {
                position: fixed;
                top: 0;
                right: -100%;
                flex-direction: column;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
                width: 70%;
                height: 100vh;
                padding: 80px 20px 20px;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                margin: 0;
                border-left: 1px solid #d1d1d1;
                display: none; /* Hide by default on mobile */
            }

            nav ul.active {
                right: 0;
                display: flex !important; /* Show when active */
            }

            nav ul li {
                margin: 15px 0;
                text-align: center;
            }

            nav ul li a {
                display: block;
                padding: 15px;
                font-size: 1.2rem;
                color: #1a2a6c;
            }

            .auth-buttons {
                position: absolute;
                top: 20px;
                right: 20px;
            }
        }

    </style>
     <!-- Google tag (gtag.js) -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-RMM38DLZLM');
</script>
</head>
<body>
     
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Animate hamburger icon
                    const spans = menuToggle.querySelectorAll('span');
                    if (navMenu.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const navLinks = document.querySelectorAll('nav ul li a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                        const spans = menuToggle.querySelectorAll('span');
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    });
                });
            }
        });
    </script>

    <section class=\"blog-header\">
        <div class=\"container\">
            <h1>$title</h1>
            <p>$excerpt</p>
        </div>
    </section>

    <section class=\"blog-container\">
        <a href=\"./index.php\" class=\"back-link\"><i class=\"fas fa-arrow-left\"></i> Back to Blog</a>
        
        <img src=\"$image_url\" alt=\"$title\" class=\"blog-image\">
        
        <div class=\"blog-content\">
            $content
        </div>
    </section>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Animate hamburger icon
                    const spans = menuToggle.querySelectorAll('span');
                    if (navMenu.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const navLinks = document.querySelectorAll('nav ul li a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                        const spans = menuToggle.querySelectorAll('span');
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>";

            // Write the file
            if (file_put_contents($filepath, $post_content)) {
                // Also update the blog index to reflect changes
                updateBlogIndexForEdit($post_number, $title, $date, $author, $excerpt, $image_url);
                $_SESSION['success_message'] = "Blog post updated successfully!";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $_SESSION['error_message'] = "Failed to update blog post file";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            // Create new post
            // Get the highest post number to determine the next post filename
            $files = glob($blog_dir . 'post*.php');
            $max_number = 0;
            
            foreach ($files as $file) {
                if (preg_match('/post(\d+)\.php/', basename($file), $matches)) {
                    $number = (int)$matches[1];
                    if ($number > $max_number) {
                        $max_number = $number;
                    }
                }
            }
            
            $next_number = $max_number + 1;
            $filename = "post{$next_number}.php";
            $filepath = $blog_dir . $filename;
            
            // Create the blog post content
            $post_content = "<?php
// Blog post $next_number: $title
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>$title - KamateRaho.com</title>
    <link rel=\"stylesheet\" href=\"../css/style.css\">
    <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css\">
    <link href=\"https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap\" rel=\"stylesheet\">
    <style>
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
            padding: 0 20px;
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
        }
        
        .blog-content h2 {
            color: #1a2a6c;
            margin: 1.5rem 0;
        }
        
        .blog-content p {
            line-height: 1.8;
            margin-bottom: 1.5rem;
            color: #333;
        }
        
        .blog-content ul, .blog-content ol {
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .blog-content li {
            margin-bottom: 0.5rem;
            line-height: 1.6;
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
        }
              /* Header Styles */
        header {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideInDown 0.5s ease-out;
        }

        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            width: auto;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .logo span {
            color: #ff6e7f;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 21px;
            cursor: pointer;
            z-index: 1001;
        }

        .menu-toggle span {
            display: block;
            height: 3px;
            width: 100%;
            background-color: #1a2a6c;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            align-items: center;
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        /* Ensure menu is visible on desktop */
        @media (min-width: 769px) {
            nav ul {
                display: flex !important;
            }
        }

        /* Hide mobile menu by default */
        @media (max-width: 768px) {
            nav ul {
                display: none;
            }
            
            nav ul.active {
                display: flex;
            }
        }

        nav ul li {
            margin-left: 25px;
        }

        nav ul li a {
            color: #1a2a6c;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }

        nav ul li a:hover {
            background: rgba(26, 42, 108, 0.1);
            color: #1a2a6c;
            transform: translateY(-2px);
        }

        .auth-buttons {
            display: flex;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin-left: 15px;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-login {
            background: transparent;
            color: #1a2a6c;
            border: 2px solid #1a2a6c;
        }

        .btn-login:hover {
            background: #1a2a6c;
            color: white;
        }

        .btn-register {
            background: #1a2a6c;
            color: white;
            border: 2px solid #1a2a6c;
        }

        .btn-register:hover {
            background: transparent;
            color: #1a2a6c;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            nav ul {
                position: fixed;
                top: 0;
                right: -100%;
                flex-direction: column;
                background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
                width: 70%;
                height: 100vh;
                padding: 80px 20px 20px;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0,0,0,0.1);
                margin: 0;
                border-left: 1px solid #d1d1d1;
                display: none; /* Hide by default on mobile */
            }

            nav ul.active {
                right: 0;
                display: flex !important; /* Show when active */
            }

            nav ul li {
                margin: 15px 0;
                text-align: center;
            }

            nav ul li a {
                display: block;
                padding: 15px;
                font-size: 1.2rem;
                color: #1a2a6c;
            }

            .auth-buttons {
                position: absolute;
                top: 20px;
                right: 20px;
            }
        }

    </style>
     <!-- Google tag (gtag.js) -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-RMM38DLZLM\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-RMM38DLZLM');
</script>
</head>
<body>
     
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Animate hamburger icon
                    const spans = menuToggle.querySelectorAll('span');
                    if (navMenu.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const navLinks = document.querySelectorAll('nav ul li a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                        const spans = menuToggle.querySelectorAll('span');
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    });
                });
            }
        });
    </script>

    <section class=\"blog-header\">
        <div class=\"container\">
            <h1>$title</h1>
            <p>$excerpt</p>
        </div>
    </section>

    <section class=\"blog-container\">
        <a href=\"./index.php\" class=\"back-link\"><i class=\"fas fa-arrow-left\"></i> Back to Blog</a>
        
        <img src=\"$image_url\" alt=\"$title\" class=\"blog-image\">
        
        <div class=\"blog-content\">
            $content
        </div>
    </section>
    
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');
            
            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                    
                    // Animate hamburger icon
                    const spans = menuToggle.querySelectorAll('span');
                    if (navMenu.classList.contains('active')) {
                        spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
                        spans[1].style.opacity = '0';
                        spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
                    } else {
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    }
                });
                
                // Close menu when clicking on a link
                const navLinks = document.querySelectorAll('nav ul li a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                        const spans = menuToggle.querySelectorAll('span');
                        spans[0].style.transform = 'none';
                        spans[1].style.opacity = '1';
                        spans[2].style.transform = 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>";

            // Write the file
            if (file_put_contents($filepath, $post_content)) {
                // Also update the blog index to include this new post
                updateBlogIndex($next_number, $title, $date, $author, $excerpt, $image_url);
                // Set success message in session
                $_SESSION['success_message'] = "Blog post created successfully!";
                // Stay on the same page after successful creation
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                // Set error message in session
                $_SESSION['error_message'] = "Failed to create blog post file";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    } else {
        // Set error message in session
        $_SESSION['error_message'] = "Title and content are required";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Include the admin layout after processing to avoid headers already sent error
include 'includes/admin_layout.php';

function updateBlogIndex($post_number, $title, $date, $author, $excerpt, $image_url) {
    $index_file = '../kamateraho/blog/index.php';
    $content = file_get_contents($index_file);
    
    // Find the position to insert the new blog post (after the opening blog-grid div)
    $blog_grid_pos = strpos($content, '<div class="blog-grid">');
    
    if ($blog_grid_pos !== false) {
        // Find the end of the opening div tag
        $blog_grid_end_pos = strpos($content, '>', $blog_grid_pos) + 1;
        
        $new_post = "
            <!-- Blog Post $post_number -->
            <div class=\"blog-card\">
                <img src=\"$image_url\" alt=\"$title\" class=\"blog-image\">
                <div class=\"blog-content\">
                    <h3>$title</h3>
                    <div class=\"blog-meta\">
                        <span><i class=\"far fa-calendar\"></i> $date</span>
                        <span><i class=\"far fa-user\"></i> $author</span>
                    </div>
                    <p class=\"blog-excerpt\">$excerpt</p>
                    <a href=\"post{$post_number}.php\" class=\"read-more\">Read More</a>
                </div>
            </div>";
        
        // Insert the new post right after the opening blog-grid div
        $content = substr_replace($content, $new_post, $blog_grid_end_pos, 0);
        
        // Write the updated content back to the file
        file_put_contents($index_file, $content);
    }
}

function updateBlogIndexForEdit($post_number, $title, $date, $author, $excerpt, $image_url) {
    $index_file = '../kamateraho/blog/index.php';
    if (file_exists($index_file)) {
        $content = file_get_contents($index_file);
        
        // Pattern to match the entire blog post entry for this post number
        // This pattern matches the comment, the entire blog-card div and all its content
        $pattern = '/<!-- Blog Post ' . preg_quote($post_number, '/') . ' -->\s*<div class="blog-card">[\s\S]*?<\/div>\s*(?=<!--|<div|\s*$)/';
        
        // Create the updated post entry
        $updated_post = "<!-- Blog Post $post_number -->
            <div class=\"blog-card\">
                <img src=\"$image_url\" alt=\"$title\" class=\"blog-image\">
                <div class=\"blog-content\">
                    <h3>$title</h3>
                    <div class=\"blog-meta\">
                        <span><i class=\"far fa-calendar\"></i> $date</span>
                        <span><i class=\"far fa-user\"></i> $author</span>
                    </div>
                    <p class=\"blog-excerpt\">$excerpt</p>
                    <a href=\"post{$post_number}.php\" class=\"read-more\">Read More</a>
                </div>
            </div>";
        
        // Replace the existing post with the updated one
        $content = preg_replace($pattern, $updated_post, $content);
        
        // Write the updated content back to the file
        file_put_contents($index_file, $content);
    }
}

function removeFromBlogIndex($post_number) {
    $index_file = '../kamateraho/blog/index.php';
    if (file_exists($index_file)) {
        $content = file_get_contents($index_file);
        
        // Pattern to match the entire blog post entry for this post number
        // This pattern matches the comment, the entire blog-card div and all its content
        $pattern = '/<!-- Blog Post ' . preg_quote($post_number, '/') . ' -->\s*<div class="blog-card">[\s\S]*?<\/div>\s*(?=<!--|<div|\s*$)/';
        
        // Debug: Check if pattern matches
        if (preg_match($pattern, $content)) {
            error_log("Match found for post number: " . $post_number);
        } else {
            error_log("No match found for post number: " . $post_number);
        }
        
        // Remove the blog post entry
        $content = preg_replace($pattern, '', $content);
        
        // Write the updated content back to the file
        file_put_contents($index_file, $content);
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Manage Blog Posts</h2>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header">
            <h5><?php echo $editing_post ? 'Edit Blog Post' : 'Create New Blog Post'; ?></h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <?php if ($editing_post): ?>
                    <input type="hidden" name="post_number" value="<?php echo $editing_post['number']; ?>">
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo $editing_post ? htmlspecialchars($editing_post['title']) : ''; ?>" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control" id="date" name="date" value="<?php echo $editing_post ? htmlspecialchars($editing_post['date']) : date('M d, Y'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author" value="<?php echo $editing_post ? htmlspecialchars($editing_post['author']) : 'Admin'; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="excerpt" class="form-label">Excerpt</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="2"><?php echo $editing_post ? htmlspecialchars($editing_post['excerpt']) : ''; ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="url" class="form-control" id="image_url" name="image_url" value="<?php echo $editing_post ? htmlspecialchars($editing_post['image_url']) : ''; ?>">
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required><?php echo $editing_post ? htmlspecialchars($editing_post['content']) : ''; ?></textarea>
                    <div class="form-text">You can use HTML tags for formatting.</div>
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo $editing_post ? 'Update Blog Post' : 'Create Blog Post'; ?></button>
                <?php if ($editing_post): ?>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Existing Blog Posts</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Author</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get all blog posts
                        $blog_dir = '../kamateraho/blog/';
                        $files = glob($blog_dir . 'post*.php');
                        $posts = [];
                        
                        foreach ($files as $file) {
                            if (preg_match('/post(\d+)\.php/', basename($file), $matches)) {
                                $number = (int)$matches[1];
                                // Get title from the file
                                $file_content = file_get_contents($file);
                                if (preg_match('/<title>(.*?) - KamateRaho\.com<\/title>/', $file_content, $title_matches)) {
                                    $title = $title_matches[1];
                                } else {
                                    $title = "Post #$number";
                                }
                                
                                // Get date from the file
                                if (preg_match('/<span><i class=\"far fa-calendar\"><\/i>(.*?)<\/span>/', $file_content, $date_matches)) {
                                    $date = trim($date_matches[1]);
                                } else {
                                    $date = "Unknown";
                                }
                                
                                $posts[] = [
                                    'number' => $number,
                                    'title' => $title,
                                    'date' => $date,
                                    'author' => 'Admin'
                                ];
                            }
                        }
                        
                        // Sort by number descending
                        usort($posts, function($a, $b) {
                            return $b['number'] - $a['number'];
                        });
                        
                        foreach ($posts as $post):
                        ?>
                        <tr>
                            <td><?php echo $post['number']; ?></td>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><?php echo htmlspecialchars($post['date']); ?></td>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td>
                                <a href="../kamateraho/blog/post<?php echo $post['number']; ?>.php" target="_blank" class="btn btn-sm btn-info">View</a>
                                <a href="?edit=<?php echo $post['number']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="post_number" value="<?php echo $post['number']; ?>">
                                    <input type="hidden" name="delete_post" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this blog post?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Delete functionality is now handled via form submission
</script>

<?php include 'includes/admin_footer.php'; ?>