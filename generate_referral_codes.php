<?php
include 'config/db.php';

echo "<h1>Generate Referral Codes for Existing Users</h1>";

if ($pdo === null) {
    echo "<p style='color: red; font-weight: bold;'>Database connection failed.</p>";
    if (isset($db_error)) {
        echo "<p>Error details: " . $db_error . "</p>";
    }
} else {
    try {
        // Check if referral_code column exists
        $stmt = $pdo->query("DESCRIBE users");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $referral_column_exists = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'referral_code') {
                $referral_column_exists = true;
                break;
            }
        }
        
        if (!$referral_column_exists) {
            echo "<p style='color: red; font-weight: bold;'>✗ Referral code column does not exist in users table.</p>";
            echo "<p>Please run add_referral_column.php first.</p>";
        } else {
            // Find users without referral codes
            $stmt = $pdo->query("SELECT id FROM users WHERE referral_code IS NULL OR referral_code = ''");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($users) > 0) {
                echo "<p>Found " . count($users) . " users without referral codes. Generating now...</p>";
                
                $updated_count = 0;
                foreach ($users as $user) {
                    // Generate a unique referral code
                    $referral_code = "REF" . $user['id'];
                    
                    // Update user with referral code
                    $stmt = $pdo->prepare("UPDATE users SET referral_code = ? WHERE id = ?");
                    $result = $stmt->execute([$referral_code, $user['id']]);
                    
                    if ($result) {
                        $updated_count++;
                    }
                }
                
                echo "<p style='color: green; font-weight: bold;'>✓ Successfully updated " . $updated_count . " users with referral codes.</p>";
            } else {
                echo "<p style='color: green; font-weight: bold;'>✓ All users already have referral codes.</p>";
            }
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>