<?php
$page_title = "Contact Messages";
include '../config/db.php';
include 'includes/admin_layout.php'; // Use the proper admin layout instead of auth.php

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply'];
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET reply = ?, status = 'replied', replied_at = NOW() WHERE id = ?");
            $stmt->execute([$reply, $message_id]);
            $success = "Reply sent successfully!";
        } catch (PDOException $e) {
            $error = "Error sending reply: " . $e->getMessage();
        }
    } else {
        $error = "Database connection failed!";
    }
}

// Fetch all contact messages
$messages = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching messages: " . $e->getMessage();
    }
}
?>

<div class="container-fluid">
    <h2>Contact Messages</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (empty($messages)): ?>
        <p>No messages found.</p>
    <?php else: ?>
        <?php foreach ($messages as $msg): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <h5><?php echo htmlspecialchars($msg['subject']); ?></h5>
                        <span class="badge bg-<?php echo $msg['status'] === 'replied' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($msg['status']); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p><strong>From:</strong> <?php echo htmlspecialchars($msg['name']); ?> 
                    &lt;<?php echo htmlspecialchars($msg['email']); ?>&gt;</p>
                    <p><strong>Date:</strong> <?php echo $msg['created_at']; ?></p>
                    <p><strong>Message:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    
                    <?php if ($msg['status'] === 'replied'): ?>
                        <div class="border-top mt-3 pt-3">
                            <p><strong>Admin Reply:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                            <p><strong>Replied at:</strong> <?php echo $msg['replied_at']; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="border-top mt-3 pt-3">
                            <h6>Reply to Message</h6>
                            <form method="POST">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <div class="mb-3">
                                    <textarea name="reply" class="form-control" rows="4" placeholder="Enter your reply..." required></textarea>
                                </div>
                                <button type="submit" name="reply_message" class="btn btn-primary">Send Reply</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>