<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
        header("Location: register.php?error=" . urlencode($error));
        exit;
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
        header("Location: register.php?error=" . urlencode($error));
        exit;
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
        header("Location: register.php?error=" . urlencode($error));
        exit;
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered!";
                header("Location: register.php?error=" . urlencode($error));
                exit;
            } else {
                // Hash password and insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, wallet_balance) VALUES (?, ?, ?, ?, 50.00)");
                $stmt->execute([$name, $email, $phone, $hashed_password]);
                
                // Get the inserted user ID
                $user_id = $pdo->lastInsertId();
                
                // Add welcome bonus to wallet history (auto-approved)
                $stmt = $pdo->prepare("INSERT INTO wallet_history (user_id, amount, type, status, description) VALUES (?, 50.00, 'credit', 'approved', 'Welcome Bonus')");
                $stmt->execute([$user_id]);
                
                $success = "Registration successful! You've received ₹50 welcome bonus. Redirecting to login...";
                header("Location: register.php?success=" . urlencode($success));
                exit;
            }
        } catch(PDOException $e) {
            $error = "Registration failed: " . $e->getMessage();
            header("Location: register.php?error=" . urlencode($error));
            exit;
        }
    }
} else {
    header("Location: register.php");
    exit;
}
?>