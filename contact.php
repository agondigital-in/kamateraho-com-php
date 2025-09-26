<?php
include 'config/db.php';
include 'includes/navbar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // In a real application, you would send an email or save to database
    // For now, we'll just show a success message
    $success = "Thank you for your message! We'll get back to you soon.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-8">
                <h1>Contact Us</h1>
                <p class="lead">We'd love to hear from you!</p>
                
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Address:</strong><br>
                        KamateRaho Pvt. Ltd.<br>
                        123 Business Street<br>
                        Mumbai, Maharashtra 400001<br>
                        India</p>
                        
                        <p><strong>Email:</strong><br>
                        support@kamateraho.com<br>
                        admin@kamateraho.com</p>
                        
                        <p><strong>Phone:</strong><br>
                        +91 98765 43210<br>
                        +91 98765 43211</p>
                        
                        <p><strong>Business Hours:</strong><br>
                        Monday - Friday: 9:00 AM - 6:00 PM<br>
                        Saturday: 10:00 AM - 4:00 PM<br>
                        Sunday: Closed</p>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Follow Us</h5>
                    </div>
                    <div class="card-body">
                        <p>Stay connected with us on social media for the latest updates and offers:</p>
                        <div class="d-flex justify-content-around">
                            <a href="#" class="btn btn-primary">Facebook</a>
                            <a href="#" class="btn btn-info text-white">Twitter</a>
                            <a href="#" class="btn btn-danger">Instagram</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>