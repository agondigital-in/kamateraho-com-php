<?php
// Fix for "Data too long for column 'redirect_url'" error
// This script increases the redirect_url column length from VARCHAR(500) to VARCHAR(2000)

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Fix Redirect URL Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f8f8f8; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Fix Redirect URL Error</h1>
        <p>This script fixes the 'Data too long for column redirect_url' error by increasing the column length.</p>";

// Include database configuration
include 'config/db.php';

try {
    echo "<h2>Current Database Status</h2>";
    
    // Check current column definition for offers table
    $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'redirect_url'");
    $stmt->execute();
    $columnInfo = $stmt->fetch();
    
    if ($columnInfo) {
        echo "<p><strong>Current redirect_url column definition:</strong></p>";
        echo "<pre>";
        print_r($columnInfo);
        echo "</pre>";
        
        // Check if we need to update the column
        if (strpos($columnInfo['Type'], 'varchar(500)') !== false || strpos($columnInfo['Type'], 'VARCHAR(500)') !== false) {
            echo "<p class='info'>Updating redirect_url column from VARCHAR(500) to VARCHAR(2000)...</p>";
            
            // Modify the column to increase its length
            $sql = "ALTER TABLE offers MODIFY redirect_url VARCHAR(2000)";
            $pdo->exec($sql);
            
            echo "<p class='success'>✓ Column 'redirect_url' successfully updated to VARCHAR(2000)</p>";
            
            // Verify the change
            $stmt = $pdo->prepare("SHOW COLUMNS FROM offers LIKE 'redirect_url'");
            $stmt->execute();
            $updatedColumnInfo = $stmt->fetch();
            
            echo "<p><strong>Updated redirect_url column definition:</strong></p>";
            echo "<pre>";
            print_r($updatedColumnInfo);
            echo "</pre>";
        } else {
            echo "<p class='info'>Column 'redirect_url' is already updated or has a different type</p>";
        }
    } else {
        echo "<p class='error'>Column 'redirect_url' not found in offers table</p>";
    }
    
    // Also update the credit_cards table link column if needed
    echo "<h2>Credit Cards Table</h2>";
    $stmt = $pdo->prepare("SHOW COLUMNS FROM credit_cards LIKE 'link'");
    $stmt->execute();
    $creditCardColumnInfo = $stmt->fetch();
    
    if ($creditCardColumnInfo) {
        echo "<p><strong>Current credit_cards link column definition:</strong></p>";
        echo "<pre>";
        print_r($creditCardColumnInfo);
        echo "</pre>";
        
        // Check if we need to update the column
        if (strpos($creditCardColumnInfo['Type'], 'varchar(500)') !== false || strpos($creditCardColumnInfo['Type'], 'VARCHAR(500)') !== false) {
            echo "<p class='info'>Updating credit_cards link column from VARCHAR(500) to VARCHAR(2000)...</p>";
            
            // Modify the column to increase its length
            $sql = "ALTER TABLE credit_cards MODIFY link VARCHAR(2000)";
            $pdo->exec($sql);
            
            echo "<p class='success'>✓ Column 'link' in credit_cards table successfully updated to VARCHAR(2000)</p>";
            
            // Verify the change
            $stmt = $pdo->prepare("SHOW COLUMNS FROM credit_cards LIKE 'link'");
            $stmt->execute();
            $updatedCreditCardColumnInfo = $stmt->fetch();
            
            echo "<p><strong>Updated credit_cards link column definition:</strong></p>";
            echo "<pre>";
            print_r($updatedCreditCardColumnInfo);
            echo "</pre>";
        } else {
            echo "<p class='info'>Column 'link' in credit_cards table is already updated or has a different type</p>";
        }
    }
    
    echo "<h2>Verification</h2>";
    echo "<p>Checking for any existing records that might have been affected:</p>";
    
    // Check for offers with long URLs
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM offers WHERE CHAR_LENGTH(redirect_url) > 500");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Offers with URLs longer than 500 characters: <strong>" . $result['count'] . "</strong></p>";
    
    // Check for credit cards with long links
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM credit_cards WHERE CHAR_LENGTH(link) > 500");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<p>Credit cards with links longer than 500 characters: <strong>" . $result['count'] . "</strong></p>";
    
    echo "<p class='success'>Database update completed successfully! The 'Data too long for column redirect_url' error should now be resolved.</p>";
    echo "<p><a href='admin/manage_offers.php'>Go back to Manage Offers</a></p>";
    
} catch(PDOException $e) {
    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    echo "<p class='info'>Please check your database connection and permissions.</p>";
}

echo "</div>
</body>
</html>";
?>