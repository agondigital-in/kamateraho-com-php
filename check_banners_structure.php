<?php
// Check banners table structure
include 'config/db.php';

if (!$pdo) {
    die("Database connection failed");
}

try {
    $stmt = $pdo->query("DESCRIBE banners");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Banners table structure:\n";
    foreach ($columns as $column) {
        echo "{$column['Field']}: {$column['Type']} " . 
             ($column['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . 
             (isset($column['Default']) ? ' DEFAULT ' . $column['Default'] : '') . "\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>