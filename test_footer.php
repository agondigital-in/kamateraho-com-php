<?php
session_start();
include 'config/db.php';
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer Test - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Footer Test Page</h1>
        <p>This page is for testing the new unique footer design.</p>
        
        <div class="row">
            <div class="col-md-6">
                <h3>Features of the New Footer</h3>
                <ul>
                    <li>Wave-style top border for visual interest</li>
                    <li>Multi-column layout with useful links</li>
                    <li>Newsletter subscription form</li>
                    <li>Enhanced social media presence</li>
                    <li>Contact information section</li>
                    <li>Animated elements for better engagement</li>
                    <li>Responsive design for all devices</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h3>Implementation Details</h3>
                <p>The new footer includes:</p>
                <ul>
                    <li>Modern gradient background</li>
                    <li>SVG wave divider for visual appeal</li>
                    <li>Social media icons with hover effects</li>
                    <li>Quick links section</li>
                    <li>Contact information with icons</li>
                    <li>Newsletter subscription form</li>
                    <li>Copyright and legal links</li>
                </ul>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>