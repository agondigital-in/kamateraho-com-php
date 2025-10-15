<?php
session_start();
include 'auth.php';
include 'database.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch referral statistics by source
$referral_stats = [];
try {
    $stmt = $pdo->query("
        SELECT 
            referral_source,
            COUNT(*) as user_count
        FROM users 
        WHERE referral_source IS NOT NULL AND referral_source != ''
        GROUP BY referral_source
        ORDER BY user_count DESC
    ");
    $referral_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching referral statistics: " . $e->getMessage();
}

// Fetch total users
$total_users = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_users = $result['total'];
} catch (PDOException $e) {
    $error = "Error fetching total users: " . $e->getMessage();
}

// Fetch users with referral sources
$users_with_source = 0;
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE referral_source IS NOT NULL AND referral_source != ''");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $users_with_source = $result['total'];
} catch (PDOException $e) {
    $error = "Error fetching users with referral source: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Statistics - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_nav.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/admin_sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Referral Statistics</h1>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2><?php echo number_format($total_users); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Users with Referral Source</h5>
                                <h2><?php echo number_format($users_with_source); ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Referral Sources</h5>
                                <h2><?php echo count($referral_stats); ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Referral Statistics Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Referral Sources Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($referral_stats)): ?>
                            <p class="text-muted">No referral data available yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Referral Source</th>
                                            <th>Number of Users</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($referral_stats as $stat): ?>
                                            <tr>
                                                <td>
                                                    <?php 
                                                    $source = htmlspecialchars($stat['referral_source']);
                                                    switch ($source) {
                                                        case 'youtube':
                                                            echo '<i class="fab fa-youtube text-danger me-2"></i>';
                                                            break;
                                                        case 'facebook':
                                                            echo '<i class="fab fa-facebook-f text-primary me-2"></i>';
                                                            break;
                                                        case 'instagram':
                                                            echo '<i class="fab fa-instagram text-instagram me-2"></i>';
                                                            break;
                                                        case 'twitter':
                                                            echo '<i class="fab fa-twitter text-info me-2"></i>';
                                                            break;
                                                        default:
                                                            echo '<i class="fas fa-link me-2"></i>';
                                                    }
                                                    echo ucfirst($source);
                                                    ?>
                                                </td>
                                                <td><?php echo number_format($stat['user_count']); ?></td>
                                                <td>
                                                    <?php 
                                                    $percentage = ($total_users > 0) ? ($stat['user_count'] / $total_users) * 100 : 0;
                                                    echo number_format($percentage, 2) . '%';
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Instructions -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">How to Use Platform-Specific Referral Links</h5>
                    </div>
                    <div class="card-body">
                        <p>Share these platform-specific referral links to track where your users are coming from:</p>
                        <ul>
                            <li><strong>YouTube:</strong> https://kamateraho.com/register.php?ref=<?php echo $_SESSION['user_id']; ?>&source=youtube</li>
                            <li><strong>Facebook:</strong> https://kamateraho.com/register.php?ref=<?php echo $_SESSION['user_id']; ?>&source=facebook</li>
                            <li><strong>Instagram:</strong> https://kamateraho.com/register.php?ref=<?php echo $_SESSION['user_id']; ?>&source=instagram</li>
                            <li><strong>Twitter:</strong> https://kamateraho.com/register.php?ref=<?php echo $_SESSION['user_id']; ?>&source=twitter</li>
                        </ul>
                        <p>When users register through these links, their referral source will be automatically tracked in the system.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>