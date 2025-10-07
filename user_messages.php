<?php
include 'config/db.php';
include 'includes/navbar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's sent messages and replied messages
$sent_messages = [];
$replied_messages = [];

if ($pdo) {
    try {
        // Fetch user's pending messages
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE user_id = ? AND status = 'pending' ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $sent_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch user's replied messages
        $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE user_id = ? AND status = 'replied' AND reply IS NOT NULL ORDER BY replied_at DESC");
        $stmt->execute([$user_id]);
        $replied_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>My Messages - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        
        <h1>My Messages</h1>
        <p class="lead">View your sent messages and replies from our team</p>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Sent Messages Section -->
        <h3 class="mt-4">Sent Messages</h3>
        <?php if (empty($sent_messages)): ?>
            <div class="alert alert-info">You haven't sent any messages yet.</div>
        <?php else: ?>
            <?php foreach ($sent_messages as $msg): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><?php echo htmlspecialchars($msg['subject']); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>To:</strong> Admin Team</p>
                        <p><strong>Message:</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        <p class="text-muted"><small>Sent on: <?php echo $msg['created_at']; ?></small></p>
                        <span class="badge bg-warning">Waiting for reply</span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Replied Messages Section -->
        <h3 class="mt-4">Replies from Admin</h3>
        <?php if (empty($replied_messages)): ?>
            <div class="alert alert-info">You don't have any replies from our team yet.</div>
        <?php else: ?>
            <?php foreach ($replied_messages as $msg): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5><?php echo htmlspecialchars($msg['subject']); ?></h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Your Message:</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                        
                        <div class="border-top mt-3 pt-3">
                            <p><strong>Admin Reply:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                            <p class="text-muted"><small>Replied on: <?php echo $msg['replied_at']; ?></small></p>
                            <span class="badge bg-success">Replied</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>