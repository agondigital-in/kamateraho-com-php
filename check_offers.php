<?php
include 'config/db.php';

try {
    $stmt = $pdo->prepare("SELECT id, title, price, price_type FROM offers LIMIT 10");
    $stmt->execute();
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Offers in Database:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Title</th><th>Price</th><th>Price Type</th></tr>";
    
    foreach ($offers as $offer) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($offer['id']) . "</td>";
        echo "<td>" . htmlspecialchars($offer['title']) . "</td>";
        echo "<td>" . htmlspecialchars($offer['price']) . "</td>";
        echo "<td>" . htmlspecialchars($offer['price_type']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>