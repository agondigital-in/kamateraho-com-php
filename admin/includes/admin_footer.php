        </div> <!-- End of content -->
    </div> <!-- End of main-content -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Admin JS -->
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.sidebar-backdrop');
            
            // Function to check if device is mobile
            function isMobile() {
                return window.innerWidth <= 992;
            }
            
            // Function to close sidebar
            function closeSidebar() {
                if (sidebar) {
                    sidebar.classList.remove('active');
                }
                if (backdrop) {
                    backdrop.classList.remove('active');
                }
                document.body.classList.remove('sidebar-open');
            }
            
            // Function to open sidebar
            function openSidebar() {
                if (sidebar) {
                    sidebar.classList.add('active');
                }
                if (backdrop) {
                    backdrop.classList.add('active');
                }
                document.body.classList.add('sidebar-open');
            }
            
            // Function to toggle sidebar
            function toggleSidebar() {
                if (sidebar && sidebar.classList.contains('active')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }
            
            // Close sidebar when clicking on backdrop
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            
            // Close sidebar when clicking outside on mobile/tablet
            document.addEventListener('click', function(event) {
                if (!sidebar || !sidebarToggle) return;
                
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggle = sidebarToggle.contains(event.target);
                const isClickOnBackdrop = backdrop && backdrop.contains(event.target);
                
                if (!isClickInsideSidebar && !isClickOnToggle && !isClickOnBackdrop && isMobile() && sidebar.classList.contains('active')) {
                    closeSidebar();
                }
            });
            
            // Close sidebar when resizing from mobile to desktop
            window.addEventListener('resize', function() {
                if (!isMobile() && sidebar) {
                    closeSidebar();
                }
            });
            
            // Close sidebar when clicking on a nav link (mobile only)
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobile() && sidebar) {
                        setTimeout(closeSidebar, 300); // Small delay to allow navigation
                    }
                });
            });
            
            // Prevent body scroll when sidebar is open on mobile
            document.addEventListener('touchmove', function(e) {
                if (document.body.classList.contains('sidebar-open') && isMobile()) {
                    // Allow scrolling inside sidebar
                    if (!sidebar.contains(e.target)) {
                        e.preventDefault();
                    }
                }
            }, { passive: false });
        });
    </script>
</body>
</html>