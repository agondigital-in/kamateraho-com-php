<!-- Footer -->
<style>
    /* Unique Footer Styles */
    .footer-wave {
        position: relative;
        background: linear-gradient(135deg, #1a2a6c, #2c3e50);
        color: #fff;
        padding: 60px 0 30px;
        margin-top: 50px;
    }
    
    .footer-wave::before {
        content: "";
        position: absolute;
        top: -100px;
        left: 0;
        width: 100%;
        height: 100px;
        background: url('data:image/svg+xml;utf8,<svg viewBox="0 0 1200 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" fill="%231a2a6c" opacity=".25"/><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" fill="%231a2a6c" opacity=".5"/><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%231a2a6c"/></svg>') no-repeat;
        background-size: cover;
        transform: rotate(180deg);
    }
    
    .footer-heading {
        position: relative;
        padding-bottom: 15px;
        margin-bottom: 20px;
        font-weight: 700;
        color: #f7b733;
    }
    
    .footer-heading::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: #f7b733;
        border-radius: 3px;
    }
    
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 12px;
        transition: all 0.3s ease;
    }
    
    .footer-links li:hover {
        transform: translateX(5px);
    }
    
    .footer-links a {
        color: #ddd;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }
    
    .footer-links a:hover {
        color: #f7b733;
        text-decoration: none;
    }
    
    .contact-info {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .contact-info li {
        display: flex;
        margin-bottom: 15px;
        align-items: flex-start;
    }
    
    .contact-info i {
        color: #f7b733;
        font-size: 1.2rem;
        margin-right: 15px;
        margin-top: 5px;
    }
    
    .social-icons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    .social-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }
    
    .social-icon:hover {
        background: #f7b733;
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(247, 183, 51, 0.4);
    }
    
    .newsletter .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 30px 0 0 30px;
        padding: 10px 15px;
        min-width: 0;
        flex: 1;
    }
    
    .newsletter .form-control:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #f7b733;
        box-shadow: none;
        color: #fff;
    }
    
    .newsletter .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }
    
    .btn-subscribe {
        background: #f7b733;
        color: #1a2a6c;
        border: none;
        border-radius: 0 30px 30px 0;
        font-weight: 600;
        transition: all 0.3s ease;
        padding: 10px 20px;
        white-space: nowrap;
    }
    
    .btn-subscribe:hover {
        background: #ffcc33;
        color: #1a2a6c;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(247, 183, 51, 0.4);
    }
    
    .footer-divider {
        background: rgba(255, 255, 255, 0.1);
        margin: 30px 0;
        height: 1px;
    }
    
    .copyright {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.9rem;
        padding: 20px 0;
        margin: 0;
    }
    
    .footer-links-inline {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 10px 0 0;
        padding: 0;
        list-style: none;
    }
    
    .footer-links-inline li {
        margin: 0;
    }
    
    .footer-links-inline a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }
    
    .footer-links-inline a:hover {
        color: #f7b733;
        text-decoration: underline;
    }
    
    /* Improved spacing and gaps */
    .footer-content {
        margin-bottom: 30px;
    }
    
    .footer-section {
        margin-bottom: 25px;
    }
    
    .footer-description {
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .footer-wave {
            padding: 40px 0 20px;
        }
        
        .footer-wave::before {
            top: -50px;
            height: 50px;
        }
        
        .footer-links-inline {
            gap: 15px;
        }
        
        .social-icons {
            justify-content: center;
        }
        
        .footer-content {
            margin-bottom: 20px;
        }
        
        .footer-section {
            margin-bottom: 20px;
            text-align: center;
        }
        
        .footer-heading::after {
            left: 50%;
            transform: translateX(-50%);
        }
    }
    
    @media (max-width: 576px) {
        .newsletter {
            flex-direction: column;
            gap: 10px;
        }
        
        .newsletter .form-control {
            border-radius: 30px;
        }
        
        .btn-subscribe {
            border-radius: 30px;
            width: 100%;
        }
    }
</style>
<br>
<footer class="footer-wave">
    <div class="container">
        <div class="row footer-content">
            <div class="col-lg-4 col-md-6 footer-section">
                <h5 class="footer-heading">KamateRaho</h5>
                <p class="footer-description">Earn cash from home by completing simple tasks and get paid instantly. Join thousands of users who are already earning with us.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/share/17JFgQNHrS/?mibextid=wwXIfr" target="_blank" class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/_kamate_raho?igsh=d2hsYmo2NXFvOGRi" target="_blank" class="social-icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-icon">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 footer-section">
                <h5 class="footer-heading">Quick Links</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="how-to-earn.php">How It Works</a></li>
                    <li><a href="all_offers.php">All Offers</a></li>
                    <!-- <li><a href="contact.php">Contact</a></li> -->
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 footer-section">
                <h5 class="footer-heading">Newsletter</h5>
                <p>Subscribe to our newsletter for the latest offers and updates.</p>
                <form class="newsletter d-flex">
                    <input type="email" class="form-control" placeholder="Your Email">
                    <button class="btn btn-subscribe" type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="text-center">
            <ul class="footer-links-inline">
                <li><a href="privacy.php">Privacy Policy</a></li>
                <li><a href="terms.php">Terms of Service</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="contact.php">Support</a></li>
            </ul>
            <p class="copyright">&copy; 2025 KamateRaho. All rights reserved.</p>
        </div>
    </div>
</footer>