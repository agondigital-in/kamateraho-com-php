<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - KamateRaho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Database Setup</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_POST['setup_db'])) {
                            // Load environment variables
                            require_once __DIR__ . '/config/env.php';

                            $servername = $_ENV['DB_HOST'] ?? 'localhost';
                            $username = $_ENV['DB_USERNAME'] ?? 'root';
                            $password = $_ENV['DB_PASSWORD'] ?? '';
                            $port = $_ENV['DB_PORT'] ?? '3306';

                            try {
                                // Connect to MySQL server without specifying a database
                                $dsn = "mysql:host=$servername;port=$port";
                                $pdo = new PDO($dsn, $username, $password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Create database
                                $dbname = $_ENV['DB_DATABASE'] ?? 'kamateraho';
                                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
                                echo "<div class='alert alert-success'>Database '$dbname' created or already exists.</div>";
                                
                                // Connect to the specific database
                                $dsn = "mysql:host=$servername;port=$port;dbname=$dbname";
                                $pdo = new PDO($dsn, $username, $password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Create categories table
                                $sql = "CREATE TABLE IF NOT EXISTS categories (
                                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                                    name VARCHAR(255) NOT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                )";
                                $pdo->exec($sql);
                                echo "<div class='alert alert-success'>Categories table created successfully</div>";
                                
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
                                echo "<div class='alert alert-success'>Offers table created successfully</div>";
                                
                                // Create users table
                                $sql = "CREATE TABLE IF NOT EXISTS users (
                                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                                    name VARCHAR(255) NOT NULL,
                                    email VARCHAR(255) UNIQUE NOT NULL,
                                    phone VARCHAR(20),
                                    city VARCHAR(100),
                                    state VARCHAR(100),
                                    password VARCHAR(255) NOT NULL,
                                    wallet_balance DECIMAL(10, 2) DEFAULT 0.00,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                                )";
                                $pdo->exec($sql);
                                echo "<div class='alert alert-success'>Users table created successfully</div>";
                                
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
                                echo "<div class='alert alert-success'>Wallet history table created successfully</div>";
                                
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
                                echo "<div class='alert alert-success'>Withdraw requests table created successfully</div>";
                                
                                // Create offer_images table
                                $sql = "CREATE TABLE IF NOT EXISTS offer_images (
                                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                                    offer_id INT(11) NOT NULL,
                                    image_path VARCHAR(255) NOT NULL,
                                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE,
                                    INDEX idx_offer_images_offer_id (offer_id)
                                )";
                                $pdo->exec($sql);
                                echo "<div class='alert alert-success'>Offer images table created successfully</div>";
                                
                                echo "<div class='alert alert-success'>
                                        <h4>All tables created successfully!</h4>
                                        <p>You can now <a href='register.php' class='alert-link'>register</a> a new account or <a href='login.php' class='alert-link'>login</a> if you already have an account.</p>
                                      </div>";
                                
                            } catch(PDOException $e) {
                                echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
                            }
                        } else {
                        ?>
                            <p>Click the button below to set up the database for KamateRaho:</p>
                            <form method="POST">
                                <button type="submit" name="setup_db" class="btn btn-primary">Create Database and Tables</button>
                            </form>
                        <?php } ?>
                        
                        <div class="mt-4">
                            <h5>Current Environment Configuration:</h5>
                            <?php
                            require_once __DIR__ . '/config/env.php';
                            echo "<ul>";
                            echo "<li>DB_HOST: " . ($_ENV['DB_HOST'] ?? 'localhost') . "</li>";
                            echo "<li>DB_PORT: " . ($_ENV['DB_PORT'] ?? '3306') . "</li>";
                            echo "<li>DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'root') . "</li>";
                            echo "<li>DB_PASSWORD: " . (empty($_ENV['DB_PASSWORD']) ? '(empty)' : '(set)') . "</li>";
                            echo "<li>DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'kamateraho') . "</li>";
                            echo "</ul>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>