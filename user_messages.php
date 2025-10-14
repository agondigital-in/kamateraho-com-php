<?php
session_start();
include 'config/db.php';
include 'includes/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's replied messages
$messages = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE user_id = ? AND status = 'replied' AND reply IS NOT NULL ORDER BY replied_at DESC");
        $stmt->execute([$user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching messages: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages - cashbacklo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Messages</li>
            </ol>
        </nav>
        
        <div class="row">
            <div class="col-lg-12">
                <h1>My Messages</h1>
                <p class="lead">View replies from admin to your messages</p>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (empty($messages)): ?>
                    <div class="alert alert-info">
                        <p>You don't have any replies from admin yet.</p>
                        <p><a href="contact.php" class="btn btn-primary">Send a Message</a></p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between">
                                    <h5><?php echo htmlspecialchars($msg['subject']); ?></h5>
                                    <span class="badge bg-success">Replied</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Sent:</strong> <?php echo $msg['created_at']; ?></p>
                                <p><strong>Your Message:</strong></p>
                                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                
                                <div class="border-top mt-3 pt-3">
                                    <p><strong>Admin Reply:</strong></p>
                                    <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                                    <p><strong>Replied at:</strong> <?php echo $msg['replied_at']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white pt-4 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>cashbacklo</h5>
                    <p>Earn cash from home by completing simple tasks and get paid instantly.</p>
                </div>
                <div class="col-md-6">
                    <h5>Connect With Us</h5>
                    <div class="d-flex gap-3">
                        <a href="https://www.facebook.com/share/17JFgQNHrS/?mibextid=wwXIfr" target="_blank" class="text-white fs-4">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/_kamate_raho?igsh=d2hsYmo2NXFvOGRi" target="_blank" class="text-white fs-4">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 cashbacklo. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>