<link rel="stylesheet" href="../styles.css">

<header>
    <div class="container">
        <nav>
            <div class="logo">
                <a href="/">
                    <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" alt="KamateRaho Logo" style="height: 65px; width: 250px;">
                </a>
            </div>
            <ul class="nav-links" id="navMenu">
                <li><a href="/">Features</a></li>
                <li><a href="/kamateraho/how-it-works.html">How It Works</a></li>
                <li><a href="/kamateraho/blog/index.php">Blog</a></li>
                <li><a href="/kamateraho/contact.html">Contact Us</a></li>
                <li><a href="/kamateraho/payment-proof.html">Payment Proof</a></li>
                <li><a href="/register.php" class="btn">Sign Up</a></li>
                <li><a href="/login.php" class="btn">Login</a></li>
            </ul>
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </div>
</header>

<style>
/* Blog-specific header adjustments */
header {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
}

header .container {
    max-width: 1400px;
}

header nav {
    padding: 15px 0;
}
</style>

<script>
(function() {
    // Prevent multiple initializations
    if (window.blogMenuInitialized) return;
    window.blogMenuInitialized = true;
    
    function initMobileMenu() {
        const menuToggle = document.getElementById('menuToggle');
        const navMenu = document.getElementById('navMenu');
        
        if (!menuToggle || !navMenu) return;
        
        // Toggle menu
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
        
        // Close menu when clicking on a link
        const navLinks = document.querySelectorAll('.nav-links li a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                menuToggle.classList.remove('active');
            }
        });
    }
    
    // Run immediately if DOM is already loaded, otherwise wait
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobileMenu);
    } else {
        initMobileMenu();
    }
})();
</script>
