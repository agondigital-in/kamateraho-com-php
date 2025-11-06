<?php
session_start();
$page_title = "Contact Messages";
include '../config/db.php';

// Check if main admin is logged in
$isAdmin = false;
$isSubAdmin = false;
$subAdminId = null;

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    $isAdmin = true;
} elseif (isset($_SESSION['sub_admin_logged_in']) && $_SESSION['sub_admin_logged_in']) {
    $isSubAdmin = true;
    $subAdminId = $_SESSION['sub_admin_id'];
    
    // Check if sub-admin has permission for contact messages
    try {
        $stmt = $pdo->prepare("SELECT allowed FROM sub_admin_permissions WHERE sub_admin_id = ? AND permission = 'contact_messages'");
        $stmt->execute([$subAdminId]);
        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$permission || !$permission['allowed']) {
            // Redirect to sub-admin dashboard if no permission
            header("Location: subadmin_dashboard.php");
            exit;
        }
    } catch (PDOException $e) {
        // Redirect on error
        header("Location: subadmin_dashboard.php");
        exit;
    }
} else {
    // Redirect to login if not logged in
    header("Location: login.php");
    exit;
}

// Include admin layout only for main admin
if ($isAdmin) {
    include 'includes/admin_layout.php';
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply'];
    
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET reply = ?, status = 'replied', replied_at = NOW() WHERE id = ?");
            $stmt->execute([$reply, $message_id]);
            
            // Log activity for sub-admin
            if ($isSubAdmin) {
                try {
                    $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                    $activityStmt->execute([$subAdminId, 'contact_reply', 'Replied to contact message ID: ' . $message_id]);
                } catch (PDOException $e) {
                    // Silently fail on activity logging
                }
            }
            
            $success = "Reply sent successfully!";
        } catch (PDOException $e) {
            $error = "Error sending reply: " . $e->getMessage();
        }
    } else {
        $error = "Database connection failed!";
    }
}

// Handle delete submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $message_id = $_POST['message_id'];
    
    if ($pdo) {
        try {
            // First get the message details for logging
            $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
            $stmt->execute([$message_id]);
            $message = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($message) {
                // Delete the message
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                $stmt->execute([$message_id]);
                
                // Log activity for sub-admin
                if ($isSubAdmin) {
                    try {
                        $activityStmt = $pdo->prepare("INSERT INTO sub_admin_activities (sub_admin_id, activity_type, description) VALUES (?, ?, ?)");
                        $activityStmt->execute([$subAdminId, 'contact_delete', 'Deleted contact message from: ' . $message['name'] . ' (' . $message['email'] . ')']);
                    } catch (PDOException $e) {
                        // Silently fail on activity logging
                    }
                }
                
                $success = "Message deleted successfully!";
            } else {
                $error = "Message not found!";
            }
        } catch (PDOException $e) {
            $error = "Error deleting message: " . $e->getMessage();
        }
    } else {
        $error = "Database connection failed!";
    }
}

// Fetch all contact messages with user information
$messages = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT cm.*, u.name as user_name, u.email as user_email FROM contact_messages cm LEFT JOIN users u ON cm.user_id = u.id ORDER BY cm.created_at DESC");
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error fetching messages: " . $e->getMessage();
    }
}

// For sub-admin, use the new sidebar layout
if ($isSubAdmin) {
    include 'subadmin_header.php';
}
?>

<?php if ($isAdmin): ?>
<div class="container-fluid">
<?php else: ?>
<!-- Content is already started in subadmin_header.php -->
<?php endif; ?>
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
                    
                    <?php if ($msg['user_id']): ?>
                        <p><strong>User Account:</strong> <?php echo htmlspecialchars($msg['user_name']); ?> 
                        &lt;<?php echo htmlspecialchars($msg['user_email']); ?>&gt; (ID: <?php echo $msg['user_id']; ?>)</p>
                    <?php else: ?>
                        <p><strong>User:</strong> Guest (No account)</p>
                    <?php endif; ?>
                    
                    <p><strong>Date:</strong> <?php echo $msg['created_at']; ?></p>
                    <p><strong>Message:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                    
                    <?php if (!empty($msg['screenshot'])): ?>
                        <div class="mt-3">
                            <p><strong>Screenshot:</strong></p>
                            <a href="../<?php echo htmlspecialchars($msg['screenshot']); ?>" target="_blank">
                                <img src="../<?php echo htmlspecialchars($msg['screenshot']); ?>" alt="Screenshot" class="img-fluid" style="max-height: 200px;">
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($msg['status'] === 'replied'): ?>
                        <div class="border-top mt-3 pt-3">
                            <p><strong>Admin Reply:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                            <p><strong>Replied at:</strong> <?php echo $msg['replied_at']; ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-3 d-flex justify-content-between">
                        <?php if ($msg['status'] !== 'replied'): ?>
                            <div>
                                <h6>Reply to Message</h6>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <div class="mb-3">
                                        <textarea name="reply" class="form-control" rows="4" placeholder="Enter your reply..." required></textarea>
                                    </div>
                                    <button type="submit" name="reply_message" class="btn btn-primary">Send Reply</button>
                                </form>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-end">
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this message? This action cannot be undone.')">
                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                <button type="submit" name="delete_message" class="btn btn-danger">Delete Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php if ($isAdmin): ?>
</div>
<?php else: ?>
<?php include 'subadmin_footer.php'; ?>
<?php endif; ?>