<?php
// Initialize the database and tables
echo "<h1>KamateRaho Database Initialization</h1>";

// Load environment variables
require_once __DIR__ . '/config/env.php';

// Database configuration
$servername = $_ENV['DB_HOST'] ?? 'localhost';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';
$dbname = $_ENV['DB_DATABASE'] ?? 'kamateraho';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "<p>✓ Database '$dbname' created or already exists</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create categories table
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>✓ Categories table created successfully</p>";
    
    // Create offers table
    $sql = "CREATE TABLE IF NOT EXISTS offers (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        category_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "<p>✓ Offers table created successfully</p>";
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "<p>✓ Users table created successfully</p>";
    
    // Create wallet_history table
    $sql = "CREATE TABLE IF NOT EXISTS wallet_history (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        type ENUM('credit', 'debit') NOT NULL,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        description VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "<p>✓ Wallet history table created successfully</p>";
    
    // Create withdraw_requests table
    $sql = "CREATE TABLE IF NOT EXISTS withdraw_requests (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        upi_id VARCHAR(255) NOT NULL,
        screenshot VARCHAR(255),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);
    echo "<p>✓ Withdraw requests table created successfully</p>";
    
    echo "<h3 class='text-success'>Database initialization completed successfully!</h3>";
    echo "<p><a href='index.php' class='btn btn-primary'>Go to Homepage</a></p>";
    
} catch(PDOException $e) {
    echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Visit <a href='admin/login.php'>Admin Panel</a> (Default credentials: admin / admin123)</li>";
echo "<li>Visit <a href='register.php'>Register</a> to create a user account</li>";
echo "</ol>";
?>