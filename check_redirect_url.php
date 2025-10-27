<?php
include 'config/db.php';

try {
    $stmt = $pdo->prepare("DESCRIBE offers");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Offers Table Structure</h2>";
    echo "<pre>";
    foreach ($columns as $column) {
        if ($column['Field'] == 'redirect_url') {
            print_r($column);
        }
    }
    echo "</pre>";
    
    // Also check the current max length of existing URLs
    $stmt = $pdo->prepare("SELECT MAX(CHAR_LENGTH(redirect_url)) as max_length FROM offers");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h3>Current Max URL Length in Database:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>