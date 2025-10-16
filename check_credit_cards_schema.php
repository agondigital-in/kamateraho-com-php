<?php
include 'config/db.php';

try {
    $stmt = $pdo->query('DESCRIBE credit_cards');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Credit Cards Table Schema:\n";
    echo "========================\n";
    foreach ($columns as $column) {
        echo "Column: " . $column['Field'] . "\n";
        echo "  Type: " . $column['Type'] . "\n";
        echo "  Null: " . $column['Null'] . "\n";
        echo "  Key: " . $column['Key'] . "\n";
        echo "  Default: " . $column['Default'] . "\n";
        echo "  Extra: " . $column['Extra'] . "\n\n";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>