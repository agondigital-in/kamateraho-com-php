<?php
session_start();
include 'config/db.php';
require_once 'admin/notifications.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user notifications
$user_notifications = [];
if ($pdo) {
    $user_notifications = getUserNotifications($pdo, $user_id);
    // Mark all as read when viewing
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        // Silently fail
    }
}

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $user = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notifications - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a2a6c;
            --secondary-color: #f7b733;
            --accent-color: #ff6e7f;
            --light-bg: #f8f9fa;
            --dark-text: #333;
            --light-text: #fff;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .notification-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .notification-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }
        
        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        }
        
        .notification-header {
            background: linear-gradient(135deg, var(--primary-color), #2c3e8f);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        
        .notification-body {
            padding: 1.5rem;
        }
        
        .notification-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="notification-container">
        <div class="card notification-card">
            <div class="card-header notification-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Your Notifications</h4>
                <span class="badge bg-light text-dark"><?php echo count($user_notifications); ?> Notifications</span>
            </div>
            <div class="card-body">
                <?php if (empty($user_notifications)): ?>
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h4>No Notifications</h4>
                        <p class="text-muted">You're all caught up! We'll notify you when something important happens.</p>
                        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($user_notifications as $notification): ?>
                            <div class="list-group-item d-flex align-items-start border-0 border-bottom py-3">
                                <div class="notification-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-danger me-2">New</span>
                                            <?php endif; ?>
                                            Notification
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y g:i A', strtotime($notification['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>