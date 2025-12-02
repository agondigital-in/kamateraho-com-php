<header style="background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
    <div class="logo">
        <a href="/">
            <img src="https://res.cloudinary.com/dqsxrixfq/image/upload/v1760442084/logo_cpe9n0_1_uhvkri.png" alt="KamateRaho Logo" style="height: 65px; width: 250px;">
        </a>
    </div>
    <nav style="display: flex; align-items: center;">
        <ul class="nav-links" style="display: flex; list-style: none; gap: 25px; margin: 0; padding: 0;">
            <li><a href="/" style="color: #1a2a6c; text-decoration: none; font-weight: 500;">Features</a></li>
            <li><a href="/kamateraho/how-it-works.html" style="color: #1a2a6c; text-decoration: none; font-weight: 500;">How It Works</a></li>
            <li><a href="/kamateraho/blog/index.php" style="color: #1a2a6c; text-decoration: none; font-weight: 500;">Blog</a></li>
            <li><a href="/kamateraho/contact.html" style="color: #1a2a6c; text-decoration: none; font-weight: 500;">Contact Us</a></li>
            <li><a href="/kamateraho/payment-proof.html" style="color: #1a2a6c; text-decoration: none; font-weight: 500;">Payment Proof</a></li>
            <li><a href="/register.php" class="btn" style="background: #1a2a6c; color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: 500;">Sign Up</a></li>
            <li><a href="/login.php" class="btn" style="background: transparent; color: #1a2a6c; border: 2px solid #1a2a6c; padding: 10px 20px; border-radius: 30px; text-decoration: none; font-weight: 500;">Login</a></li>
        </ul>
        <div class="menu-toggle" style="display: none; flex-direction: column; gap: 5px; cursor: pointer;">
            <span style="width: 25px; height: 3px; background: #1a2a6c; display: block;"></span>
            <span style="width: 25px; height: 3px; background: #1a2a6c; display: block;"></span>
            <span style="width: 25px; height: 3px; background: #1a2a6c; display: block;"></span>
        </div>
    </nav>
</header>

<style>
@media (max-width: 768px) {
    header nav .nav-links {
        display: none;
        position: fixed;
        top: 80px;
        right: 0;
        background: white;
        flex-direction: column;
        width: 70%;
        height: 100vh;
        padding: 20px;
        box-shadow: -5px 0 15px rgba(0,0,0,0.1);
    }
    header nav .nav-links.active {
        display: flex !important;
    }
    header nav .menu-toggle {
        display: flex !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
});
</script>
