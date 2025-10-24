<?php
include 'config/db.php';

$stmt = $pdo->query('DESCRIBE offers');
while ($row = $stmt->fetch()) {
    if ($row['Field'] == 'price_type') {
        print_r($row);
    }
}
?>