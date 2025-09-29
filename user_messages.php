<?php
include 'config/db.php';
include 'includes/navbar.php';

// In a real application, you would check if the user is logged in
// For now, we'll just show all replied messages

$replied_messages = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM contact_messages WHERE status = 'replied' AND reply IS NOT NULL ORDER BY replied_at DESC");
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
        <p class="lead">View replies from our team</p>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
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
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>