<?php
session_start();
echo "Session data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "User ID from session: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set') . "<br>";

// Test database connection
include 'config/db.php';

if (!$pdo) {
    echo "Database connection failed.<br>";
} else {
    echo "Database connection successful.<br>";
    
    if (isset($_SESSION['user_id'])) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "User data found:<br>";
                echo "<pre>";
                print_r($user);
                echo "</pre>";
            } else {
                echo "No user found with ID: " . $_SESSION['user_id'] . "<br>";
            }
        } catch(PDOException $e) {
            echo "Error fetching user data: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "User ID not set in session.<br>";
    }
}
?>