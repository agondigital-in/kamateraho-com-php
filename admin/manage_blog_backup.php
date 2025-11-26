<?php
// Handle form submission for creating/updating blog posts
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if this is a delete request
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['post_number'])) {
        $post_number = (int)$_POST['post_number'];
        $blog_dir = '../kamateraho/blog/';
        $filepath = $blog_dir . "post{$post_number}.php";
        
        // Check if file exists
        if (file_exists($filepath)) {
            // Delete the blog post file
            if (unlink($filepath)) {
                // Also remove the post from the blog index
                removePostFromIndex($post_number);
                // Redirect after successful deletion
                header("Location: manage_blog.php?deleted=true");
                exit();
            } else {
                $error_message = "Failed to delete blog post file";
            }
        } else {
            $error_message = "Blog post file not found";
        }
    } else {
        // Handle creation of new blog post
        $title = $_POST['title'] ?? '';
        $date = $_POST['date'] ?? date('M d, Y');
        $author = $_POST['author'] ?? 'Admin';
        $excerpt = $_POST['excerpt'] ?? '';
        $image_url = $_POST['image_url'] ?? '';
        $content = $_POST['content'] ?? '';
        
        // Validate inputs
        if (!empty($title) && !empty($content)) {
            // Get the highest post number to determine the next post filename
            $blog_dir = '../kamateraho/blog/';
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
                // Redirect to the blog index page after successful creation
                header("Location: ../kamateraho/blog/index.php");
                exit();
            } else {
                $error_message = "Failed to create blog post file";
            }
        } else {
            $error_message = "Title and content are required";
        }
    }
}

$page_title = "Manage Blog";
include '../config/db.php';
include 'includes/admin_layout.php';
    // Check if this is a delete request
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['post_number'])) {
        $post_number = (int)$_POST['post_number'];
        $blog_dir = '../kamateraho/blog/';
        $filepath = $blog_dir . "post{$post_number}.php";
        
        // Check if file exists
        if (file_exists($filepath)) {
            // Delete the blog post file
            if (unlink($filepath)) {
                // Also remove the post from the blog index
                removePostFromIndex($post_number);
                $success_message = "Blog post deleted successfully";
            } else {
                $error_message = "Failed to delete blog post file";
            }
        } else {
            $error_message = "Blog post file not found";
        }
    } else {
        // Handle creation of new blog post
        $title = $_POST['title'] ?? '';
        $date = $_POST['date'] ?? date('M d, Y');
        $author = $_POST['author'] ?? 'Admin';
        $excerpt = $_POST['excerpt'] ?? '';
        $image_url = $_POST['image_url'] ?? '';
        $content = $_POST['content'] ?? '';
        
        // Validate inputs
        if (!empty($title) && !empty($content)) {
            // Get the highest post number to determine the next post filename
            $blog_dir = '../kamateraho/blog/';
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
                // Redirect to the blog index page after successful creation
                header("Location: ../kamateraho/blog/index.php");
                exit();
            } else {
                $error_message = "Failed to create blog post file";
            }
        } else {
            $error_message = "Title and content are required";
        }
    }
}

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

function removePostFromIndex($post_number) {
    $index_file = '../kamateraho/blog/index.php';
    $content = file_get_contents($index_file);
    
    // Find and remove the blog post entry
    $pattern = '/\s*<!-- Blog Post ' . $post_number . ' -->\s*<div class="blog-card">.*?<\/div>\s*<\/div>\s*/s';
    $content = preg_replace($pattern, "\n", $content, 1);
    
    // Write the updated content back to the file
    file_put_contents($index_file, $content);
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
            <h5>Create New Blog Post</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="text" class="form-control" id="date" name="date" value="<?php echo date('M d, Y'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author" value="Admin">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="excerpt" class="form-label">Excerpt</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="2"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="url" class="form-control" id="image_url" name="image_url">
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    <div class="form-text">You can use HTML tags for formatting.</div>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Blog Post</button>
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
                                <a href="edit_post.php?post=<?php echo $post['number']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="../kamateraho/blog/post<?php echo $post['number']; ?>.php" target="_blank" class="btn btn-sm btn-info">View</a>
                                <button class="btn btn-sm btn-danger" onclick="deletePost(<?php echo $post['number']; ?>)">Delete</button>
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
function deletePost(postNumber) {
    if (confirm('Are you sure you want to delete this blog post?')) {
        // Create a form to submit the delete request
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Add action field
        var actionField = document.createElement('input');
        actionField.type = 'hidden';
        actionField.name = 'action';
        actionField.value = 'delete';
        form.appendChild(actionField);
        
        // Add post number field
        var postNumberField = document.createElement('input');
        postNumberField.type = 'hidden';
        postNumberField.name = 'post_number';
        postNumberField.value = postNumber;
        form.appendChild(postNumberField);
        
        // Add form to document and submit
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>