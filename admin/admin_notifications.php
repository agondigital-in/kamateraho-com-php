<?php
$page_title = "Notifications";
include 'includes/admin_layout.php';

// Get all notifications
$notifications = [];
if ($pdo) {
    $notifications = getAllNotifications($pdo);
    // Mark all as read when viewing
    markAllNotificationsAsRead($pdo);
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Admin Notifications</h5>
                    <span class="badge bg-primary"><?php echo count($notifications); ?> Notifications</span>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-bell-slash" style="font-size: 3rem; color: #ccc;"></i>
                            <h4 class="mt-3">No notifications</h4>
                            <p class="text-muted">You're all caught up!</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-danger">New</span>
                                            <?php endif; ?>
                                            Notification for User: <?php echo htmlspecialchars($notification['user_name']); ?>
                                        </h6>
                                        <small><?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <small class="text-muted">
                                        User ID: <?php echo $notification['user_id']; ?> | 
                                        Email: <?php echo htmlspecialchars($notification['user_email']); ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
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