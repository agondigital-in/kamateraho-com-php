// JavaScript for KamateRaho landing page

// Hide preloader when page is fully loaded
window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(function() {
            preloader.style.opacity = '0';
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 300);
        }, 500);
    }
    
    // Add animation to the logo
    const logo = document.querySelector('.logo');
    if (logo) {
        logo.classList.add('animate__animated', 'animate__bounceIn');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 70,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Form submission handling (if we add forms later)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Form handling logic would go here
            alert('Form submitted! (This is a demo)');
        });
    });
    
    // Animation for feature cards on scroll
    const featureCards = document.querySelectorAll('.feature-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    featureCards.forEach(card => {
        card.style.opacity = 0;
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });
    
    // Testimonial display - show all testimonials in grid
    const testimonials = document.querySelectorAll('.testimonial-card');
    if (testimonials.length > 0) {
        // Ensure all testimonials are visible (remove carousel functionality)
        testimonials.forEach((testimonial) => {
            testimonial.style.display = 'block';
        });
    }
    
    // Button hover effects enhancement
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Add scroll animation to sections
    const sections = document.querySelectorAll('section');
    const sectionObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate__animated', 'animate__fadeInUp');
            }
        });
    }, { threshold: 0.1 });
    
    sections.forEach(section => {
        sectionObserver.observe(section);
    });
    
    // Add parallax effect to hero section
    window.addEventListener('scroll', function() {
        const hero = document.querySelector('.hero');
        const scrollPosition = window.scrollY;
        hero.style.backgroundPositionY = -scrollPosition * 0.5 + 'px';
    });
    
    // Add animation to feature icons on hover
    const featureIcons = document.querySelectorAll('.feature-icon');
    featureIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.classList.add('animate__animated', 'animate__pulse');
        });
        
        icon.addEventListener('animationend', function() {
            this.classList.remove('animate__animated', 'animate__pulse');
        });
    });
    
    // Add animation to step numbers on hover
    const stepNumbers = document.querySelectorAll('.step-number');
    stepNumbers.forEach(number => {
        number.addEventListener('mouseenter', function() {
            this.classList.add('animate__animated', 'animate__rubberBand');
        });
        
        number.addEventListener('animationend', function() {
            this.classList.remove('animate__animated', 'animate__rubberBand');
        });
    });
    
    // Mobile menu toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mobileNavLinks = document.querySelector('.nav-links');

    if (menuToggle && mobileNavLinks) {
        menuToggle.addEventListener('click', function() {
            mobileNavLinks.classList.toggle('active');
            // Animate hamburger icon
            this.classList.toggle('active');
            
            // Add animation class to nav items
            const navItems = mobileNavLinks.querySelectorAll('li');
            
            // Prevent body scroll when menu is open
            if (mobileNavLinks.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
                
                // Add staggered animation to nav items
                navItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.style.transform = 'translateX(0)';
                        item.style.opacity = '1';
                    }, 100 * index);
                });
            } else {
                document.body.style.overflow = '';
                
                // Reset nav items animation
                navItems.forEach(item => {
                    item.style.transform = 'translateX(-20px)';
                    item.style.opacity = '0';
                });
            }
        });
        
        // Close mobile menu when clicking on a link
        const navItems = document.querySelectorAll('.nav-links a');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                mobileNavLinks.classList.remove('active');
                menuToggle.classList.remove('active');
                document.body.style.overflow = '';
                
                // Reset nav items animation
                const navListItems = mobileNavLinks.querySelectorAll('li');
                navListItems.forEach(item => {
                    item.style.transform = 'translateX(-20px)';
                    item.style.opacity = '0';
                });
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (mobileNavLinks.classList.contains('active') && 
                !mobileNavLinks.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                mobileNavLinks.classList.remove('active');
                menuToggle.classList.remove('active');
                document.body.style.overflow = '';
                
                // Reset nav items animation
                const navListItems = mobileNavLinks.querySelectorAll('li');
                navListItems.forEach(item => {
                    item.style.transform = 'translateX(-20px)';
                    item.style.opacity = '0';
                });
            }
        });
    }
    
    // Enhanced brand slider functionality
    const sliderTrack = document.querySelector('.slider-track');
    if (sliderTrack) {
        // No additional JS needed as CSS handles the animation
        // The hover pause is handled by CSS animation-play-state
    }
    
    // Image loading optimization for brand logos
    const brandImages = document.querySelectorAll('.brand-img');
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                // Images are already defined in HTML, but we can add lazy loading if needed
                imageObserver.unobserve(img);
            }
        });
    });
    
    brandImages.forEach(img => {
        imageObserver.observe(img);
    });
    
    // Animate offer cards in hero section
    const offerCards = document.querySelectorAll('.offer-card');
    if (offerCards.length > 0) {
        // Add staggered animation to offer cards
        offerCards.forEach((card, index) => {
            // Add delay based on index
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                
                // Trigger animation
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100);
            }, index * 200);
        });
    }
    
    // Add floating animation to hero device
    const heroDevice = document.querySelector('.hero-device');
    if (heroDevice) {
        let floatPosition = 0;
        setInterval(() => {
            floatPosition = (floatPosition + 1) % 360;
            heroDevice.style.transform = `translateY(${Math.sin(floatPosition * Math.PI / 180) * 10}px)`;
        }, 50);
    }
    
    // Create particle effect for hero section
    const heroParticles = document.querySelector('.hero-particles');
    if (heroParticles) {
        // Create 30 particles
        for (let i = 0; i < 30; i++) {
            createParticle(heroParticles);
        }
    }
    
    function createParticle(container) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        // Random size between 2px and 6px
        const size = Math.random() * 4 + 2;
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        
        // Random position
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;
        
        // Random animation duration between 5s and 15s
        const duration = Math.random() * 10 + 5;
        particle.style.animationDuration = `${duration}s`;
        
        // Random delay
        particle.style.animationDelay = `${Math.random() * 5}s`;
        
        container.appendChild(particle);
        
        // Remove particle after animation completes and create a new one
        setTimeout(() => {
            particle.remove();
            createParticle(container);
        }, duration * 1000);
    }
    
    // Add refresh button functionality
    const refreshBtn = document.querySelector('.refresh-btn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            // Add rotation animation
            this.style.transform = 'rotate(360deg)';
            this.style.transition = 'transform 0.5s ease';
            
            // Reset after animation
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 500);
            
            // In a real app, this would fetch new offers
            console.log('Refreshing offers...');
        });
    }
    
    // Add hover effect to offer cards
    offerCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Handle window resize for responsive adjustments
    window.addEventListener('resize', function() {
        // Adjust hero section for different screen sizes
        const hero = document.querySelector('.hero');
        const heroContent = document.querySelector('.hero-content');
        const heroVisual = document.querySelector('.hero-visual');
        
        if (window.innerWidth <= 767) {
            // Mobile adjustments
            if (hero) hero.style.padding = '50px 0 0';
            if (heroContent) heroContent.style.textAlign = 'center';
            if (heroVisual) heroVisual.style.marginTop = '0';
        } else {
            // Desktop adjustments
            if (hero) hero.style.padding = '120px 0 0';
            if (heroContent) heroContent.style.textAlign = 'left';
            if (heroVisual) heroVisual.style.marginTop = '30px';
        }
    });
    
    // Add scroll effect to header
    const header = document.querySelector('header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // FAQ accordion functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            const answer = question.nextElementSibling;
            const icon = question.querySelector('.faq-icon');
            
            // Close all other FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                    const otherAnswer = item.querySelector('.faq-answer');
                    const otherIcon = item.querySelector('.faq-icon');
                    if (otherAnswer) otherAnswer.style.display = 'none';
                    if (otherIcon) otherIcon.textContent = '+';
                }
            });
            
            // Toggle current FAQ item
            faqItem.classList.toggle('active');
            
            // Toggle answer visibility
            if (faqItem.classList.contains('active')) {
                answer.style.display = 'block';
                icon.textContent = '−';
            } else {
                answer.style.display = 'none';
                icon.textContent = '+';
            }
        });
    });
});

// Utility function to handle window resize events
window.addEventListener('resize', function() {
    // Responsive adjustments can be made here if needed
    // Close mobile menu on resize if open
    const mobileNavLinks = document.querySelector('.nav-links');
    const menuToggle = document.querySelector('.menu-toggle');
    if (mobileNavLinks && menuToggle) {
        mobileNavLinks.classList.remove('active');
        menuToggle.classList.remove('active');
    }
});

// Popup Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Create popup functionality
    const popup = document.getElementById('imagePopup');
    const popupImage = document.getElementById('popupImage');
    const closeBtn = document.querySelector('.popup-close');
    
    // Function to show popup with image
    function showPopup(imageSrc) {
        if (popup && popupImage) {
            popupImage.src = imageSrc;
            popup.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            // Create confetti effect when popup appears
            createConfetti();
        }
    }
    
    // Function to create confetti effect
    function createConfetti() {
        // Create confetti from top of screen
        confetti({
            particleCount: 150,
            spread: 180,
            origin: { y: 0 },
            gravity: 0.8,
            ticks: 300,
            colors: ['#ff6b00', '#ff8c00', '#ffeb3b', '#4caf50', '#2196f3'],
            shapes: ['circle', 'square'],
            scalar: 1.2
        });
        
        // Create additional confetti bursts
        setTimeout(() => {
            confetti({
                particleCount: 100,
                angle: 60,
                spread: 55,
                origin: { x: 0 },
                gravity: 0.8,
                ticks: 200,
                colors: ['#ff6b00', '#ff8c00', '#ffeb3b'],
                scalar: 0.8
            });
        }, 300);
        
        setTimeout(() => {
            confetti({
                particleCount: 100,
                angle: 120,
                spread: 55,
                origin: { x: 1 },
                gravity: 0.8,
                ticks: 200,
                colors: ['#ff6b00', '#ff8c00', '#ffeb3b'],
                scalar: 0.8
            });
        }, 600);
    }
    
    // Function to hide popup
    function hidePopup() {
        if (popup) {
            popup.style.display = 'none';
            document.body.style.overflow = ''; // Re-enable scrolling
            
            // Create closing confetti effect
            createClosingConfetti();
        }
    }
    
    // Function to create closing confetti effect
    function createClosingConfetti() {
        // Create confetti burst when closing
        confetti({
            particleCount: 100,
            spread: 120,
            origin: { y: 0.5 },
            gravity: 1.2,
            ticks: 200,
            colors: ['#ff6b00', '#ff8c00', '#ffeb3b', '#4caf50'],
            shapes: ['circle'],
            scalar: 1.0
        });
    }
    
    // Close popup when close button is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', hidePopup);
    }
    
    // Close popup when clicking outside the image
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                hidePopup();
            }
        });
    }
    
    // Close popup when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && popup && popup.style.display === 'block') {
            hidePopup();
        }
    });
    
    // Function to get responsive image URL based on screen size
    function getResponsiveImageUrl() {
        // Use the actual image file with correct path
        return '/kamateraho/img/Get ₹200 Instant in Bank Account — Diwali Offer by KamateRaho (1).png';
    }
    
    // Show popup automatically when page loads after a delay
    setTimeout(function() {
        showPopup(getResponsiveImageUrl());
    }, 5000); // Show after 5 seconds
});
