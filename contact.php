<?php
session_start();
include 'config/db.php';
include 'includes/navbar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    // Get user_id if user is logged in
    $user_id = null;
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    
    // Save to database
    if ($pdo) {
        try {
            if ($user_id) {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $name, $email, $subject, $message]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $subject, $message]);
            }
            $success = "Thank you for your message! We'll get back to you soon.";
        } catch (PDOException $e) {
            $error = "Sorry, there was an error sending your message. Please try again.";
        }
    } else {
        $error = "Database connection failed. Please try again later.";
    }
} else {
    // Pre-fill form with user data if logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        try {
            $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $prefill_name = $user['name'];
                $prefill_email = $user['email'];
            }
        } catch (PDOException $e) {
            // Silently fail, form will be empty
        }
    }
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
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($prefill_name) ? htmlspecialchars($prefill_name) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($prefill_email) ? htmlspecialchars($prefill_email) : ''; ?>" required>
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