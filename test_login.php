<?php
session_start();
include 'config/db.php';

// Test login with a known user
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        echo "User logged in successfully:<br>";
        echo "User ID: " . $user['id'] . "<br>";
        echo "Name: " . $user['name'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
        echo "<a href='profile.php'>Go to Profile</a>";
    } else {
        echo "User not found.";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>