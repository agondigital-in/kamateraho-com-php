<?php
// Database import script
echo "<h1>KamateRaho Database Import</h1>";

// Database configuration
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = ""; // Default XAMPP password

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read SQL files
    $schema_sql = file_get_contents('kamateraho.sql');
    $data_sql = file_get_contents('sample_data.sql');
    
    // Execute schema SQL
    $pdo->exec($schema_sql);
    echo "<p>✓ Database schema imported successfully</p>";
    
    // Execute sample data SQL
    $pdo->exec($data_sql);
    echo "<p>✓ Sample data imported successfully</p>";
    
    echo "<h3 class='text-success'>Database import completed successfully!</h3>";
    echo "<p><a href='../index.php' class='btn btn-primary'>Go to Homepage</a></p>";
    
} catch(PDOException $e) {
    echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Visit <a href='../admin/login.php'>Admin Panel</a> (Default credentials: admin / admin123)</li>";
echo "<li>Visit <a href='../register.php'>Register</a> to create a user account</li>";
echo "</ol>";
?>