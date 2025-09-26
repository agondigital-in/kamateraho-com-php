<?php
// Reset database script - FOR DEVELOPMENT PURPOSES ONLY
// This will delete all data and recreate tables

echo "<h1>Reset KamateRaho Database</h1>";
echo "<p class='text-danger'><strong>WARNING: This will delete all data!</strong></p>";

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    include 'config/db.php';
    
    if ($pdo) {
        try {
            // Drop tables in reverse order of dependencies
            $tables = ['withdraw_requests', 'wallet_history', 'offers', 'users', 'categories'];
            
            foreach ($tables as $table) {
                $pdo->exec("DROP TABLE IF EXISTS $table");
                echo "<p>Dropped table: $table</p>";
            }
            
            // Recreate tables
            // Create categories table
            $sql = "CREATE TABLE categories (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $pdo->exec($sql);
            echo "<p>✓ Categories table created</p>";
            
            // Create users table
            $sql = "CREATE TABLE users (
                id INT(11) AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $pdo->exec($sql);
            echo "<p>✓ Users table created</p>";
            
            // Create offers table
            $sql = "CREATE TABLE offers (
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
            echo "<p>✓ Offers table created</p>";
            
            // Create wallet_history table
            $sql = "CREATE TABLE wallet_history (
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
            echo "<p>✓ Wallet history table created</p>";
            
            // Create withdraw_requests table
            $sql = "CREATE TABLE withdraw_requests (
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
            echo "<p>✓ Withdraw requests table created</p>";
            
            echo "<h3 class='text-success'>Database reset completed successfully!</h3>";
            echo "<p><a href='index.php' class='btn btn-primary'>Go to Homepage</a></p>";
            
        } catch(PDOException $e) {
            echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='text-danger'>Database connection failed.</p>";
    }
} else {
    echo "<p>This script will reset the entire database and delete all data.</p>";
    echo "<p><a href='?confirm=yes' class='btn btn-danger'>Confirm Reset Database</a></p>";
    echo "<p><a href='index.php' class='btn btn-secondary'>Cancel</a></p>";
}
?>