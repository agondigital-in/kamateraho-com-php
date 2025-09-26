<?php
include '../config/db.php';

// Database name from environment
$dbname = $_ENV['DB_DATABASE'] ?? 'kamateraho';
include 'auth.php'; // Admin authentication check

// Handle actions
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'backup') {
        // In a real application, this would generate a backup
        $message = "Database backup initiated successfully!";
    } elseif ($action === 'reset') {
        // In a real application, this would reset the database
        $message = "Database reset functionality would be implemented here.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management - KamateRaho Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/admin_nav.php'; ?>
    
    <div class="container mt-5">
        <h2>Database Management</h2>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Backup Database</h5>
                    </div>
                    <div class="card-body">
                        <p>Create a backup of the current database.</p>
                        <a href="database.php?action=backup" class="btn btn-primary">Create Backup</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Reset Database</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-danger"><strong>Warning:</strong> This will delete all data!</p>
                        <a href="database.php?action=reset" class="btn btn-danger" 
                           onclick="return confirm('Are you sure you want to reset the database? This will delete all data!')">Reset Database</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5>Database Information</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>Database Name</th>
                        <td><?php echo htmlspecialchars($dbname); ?></td>
                    </tr>
                    <tr>
                        <th>Tables</th>
                        <td>
                            <?php
                            if ($pdo) {
                                try {
                                    $stmt = $pdo->query("SHOW TABLES");
                                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                    echo implode(', ', $tables);
                                } catch (PDOException $e) {
                                    echo "Unable to fetch tables";
                                }
                            } else {
                                echo "Database not connected";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Database Size</th>
                        <td>
                            <?php
                            if ($pdo) {
                                try {
                                    $stmt = $pdo->prepare("SELECT table_schema AS 'database', 
                                                         SUM(data_length + index_length) AS 'size' 
                                                         FROM information_schema.tables 
                                                         WHERE table_schema = :dbname 
                                                         GROUP BY table_schema");
                                    $stmt->bindParam(':dbname', $dbname);
                                    $stmt->execute();
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    echo isset($row['size']) ? round($row['size'] / 1024, 2) . ' KB' : 'Unknown';
                                } catch (PDOException $e) {
                                    echo "Unable to fetch size";
                                }
                            } else {
                                echo "Database not connected";
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>