<?php
// Include database connection
require_once __DIR__ . '/../config/db.php';

// Function to get unread notifications count
function getUnreadNotificationsCount($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

// Function to get all notifications
function getAllNotifications($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT n.*, u.name as user_name, u.email as user_email 
            FROM notifications n 
            JOIN users u ON n.user_id = u.id 
            ORDER BY n.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Function to mark notification as read
function markNotificationAsRead($pdo, $notificationId) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        $stmt->execute([$notificationId]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to mark all notifications as read
function markAllNotificationsAsRead($pdo) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1");
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to create a notification
function createNotification($pdo, $userId, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->execute([$userId, $message]);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        return false;
    }
}

// Function to get notifications for a specific user
function getUserNotifications($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>