<?php
require_once __DIR__ . '/../config/db.php';

if ($pdo) {
    try {
        // Create email verification table
        $sql = "CREATE TABLE IF NOT EXISTS email_verification (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL,
            verification_code VARCHAR(10) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            expires_at TIMESTAMP NOT NULL,
            is_verified BOOLEAN DEFAULT FALSE,
            INDEX idx_email (email),
            INDEX idx_code (verification_code)
        )";
        
        $pdo->exec($sql);
        echo "Email verification table created successfully!\n";
        
        // Add a verified column to users table if it doesn't exist
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'email_verified'");
        $column_exists = $stmt->fetch();
        
        if (!$column_exists) {
            $alter_sql = "ALTER TABLE users ADD COLUMN email_verified BOOLEAN DEFAULT FALSE";
            $pdo->exec($alter_sql);
            echo "Added email_verified column to users table!\n";
        } else {
            echo "email_verified column already exists in users table.\n";
        }
        
    } catch(PDOException $e) {
        echo "Error creating email verification table: " . $e->getMessage() . "\n";
    }
} else {
    echo "Database connection failed!\n";
}
?>