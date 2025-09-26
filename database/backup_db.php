<?php
// Database backup script
echo "<h1>KamateRaho Database Backup</h1>";

// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password
$dbname = "kamateraho";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all table names
    $tables = [];
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    // Generate backup SQL
    $backup_content = "-- KamateRaho Database Backup\n";
    $backup_content .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
    $backup_content .= "USE $dbname;\n\n";
    
    foreach ($tables as $table) {
        // Drop table statement
        $backup_content .= "DROP TABLE IF EXISTS `$table`;\n";
        
        // Create table statement
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $backup_content .= $row[1] . ";\n\n";
        
        // Insert data statements
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $columns = $stmt->columnCount();
        
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $backup_content .= "INSERT INTO `$table` VALUES (";
            for ($i = 0; $i < $columns; $i++) {
                $backup_content .= ($i > 0 ? ',' : '') . 
                    ($row[$i] === null ? 'NULL' : "'" . addslashes($row[$i]) . "'");
            }
            $backup_content .= ");\n";
        }
        $backup_content .= "\n";
    }
    
    // Save backup to file
    $backup_filename = "backup_" . date('Y-m-d_H-i-s') . ".sql";
    file_put_contents($backup_filename, $backup_content);
    
    echo "<p>✓ Database backup created successfully: $backup_filename</p>";
    echo "<p><a href='$backup_filename' class='btn btn-primary' download>Download Backup</a></p>";
    echo "<h3 class='text-success'>Database backup completed!</h3>";
    
} catch(PDOException $e) {
    echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='../index.php' class='btn btn-secondary'>Go to Homepage</a></p>";
?>