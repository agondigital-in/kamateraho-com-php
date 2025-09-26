<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = "All fields are required!";
        header("Location: login.php?error=" . urlencode($error));
        exit;
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect to dashboard
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid email or password!";
                header("Location: login.php?error=" . urlencode($error));
                exit;
            }
        } catch(PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
            header("Location: login.php?error=" . urlencode($error));
            exit;
        }
    }
} else {
    header("Location: login.php");
    exit;
}
?>