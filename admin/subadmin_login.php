<?php
session_start();
include '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM sub_admins WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $sub_admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($sub_admin && password_verify($password, $sub_admin['password'])) {
            $_SESSION['sub_admin_logged_in'] = true;
            $_SESSION['sub_admin_id'] = $sub_admin['id'];
            $_SESSION['sub_admin_name'] = $sub_admin['name'];
            header("Location: subadmin_dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password, or account is inactive.";
        }
    } catch (PDOException $e) {
        $error = "Login error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub-Admin Login - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="card mt-5">
                    <div class="card-header text-center">
                        <h3>Sub-Admin Login</h3>
                        <p class="text-muted">KamateRaho Admin Panel</p>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>