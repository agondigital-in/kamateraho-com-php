<?php
include 'config/db.php';

if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM offers LIMIT 5");
        $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<pre>";
        print_r($offers);
        echo "</pre>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No database connection";
}
?>