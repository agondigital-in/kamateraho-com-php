<?php
$page_title = "Send Notification";
include 'includes/admin_layout.php';

// Handle form submission
$message = '';
$user_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_notification'])) {
    $user_id = $_POST['user_id'];
    $message = trim($_POST['message']);
    
    if (empty($user_id) || empty($message)) {
        $error = "Both User ID and Message are required!";
    } else {
        // Validate that the user exists
        try {
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Create notification
                if (createNotification($pdo, $user_id, $message)) {
                    $success = "Notification sent successfully to " . $user['name'] . " (User ID: " . $user_id . ")";
                    $user_id = '';
                    $message = '';
                } else {
                    $error = "Failed to send notification. Please try again.";
                }
            } else {
                $error = "User with ID " . $user_id . " not found!";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch all users for the dropdown
$users = [];
try {
    $stmt = $pdo->query("SELECT id, name, email FROM users ORDER BY name");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Send Notification to User</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select User</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="">Choose a user...</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo ($user_id == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['name']); ?> (ID: <?php echo $user['id']; ?>, Email: <?php echo htmlspecialchars($user['email']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" placeholder="Enter your notification message..." required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>
                        
                        <button type="submit" name="send_notification" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Send Notification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Notifications</h5>
                </div>
                <div class="card-body">
                    <?php 
                    $recent_notifications = [];
                    if ($pdo) {
                        $recent_notifications = getAllNotifications($pdo);
                    }
                    ?>
                    
                    <?php if (empty($recent_notifications)): ?>
                        <div class="text-center py-3">
                            <p class="text-muted">No notifications sent yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recent_notifications, 0, 10) as $notification): ?>
                                        <tr>
                                            <td>
                                                <?php echo htmlspecialchars($notification['user_name']); ?> 
                                                (ID: <?php echo $notification['user_id']; ?>)
                                            </td>
                                            <td><?php echo htmlspecialchars($notification['message']); ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?></td>
                                            <td>
                                                <?php if ($notification['is_read']): ?>
                                                    <span class="badge bg-success">Read</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Unread</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/admin_footer.php';
?>