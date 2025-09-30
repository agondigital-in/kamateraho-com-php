<?php
include 'config/db.php';

echo "<h1>Offer Images in Database</h1>";

try {
    $stmt = $pdo->query("SELECT id, title, image FROM offers ORDER BY id DESC LIMIT 10");
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($offers)) {
        echo "<p>No offers found in the database.</p>";
    } else {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>File Exists</th></tr>";
        foreach ($offers as $offer) {
            $image_path = $offer['image'];
            $file_exists = file_exists($image_path) ? 'Yes' : 'No';
            $display_path = !empty($image_path) ? htmlspecialchars($image_path) : '(empty)';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($offer['id']) . "</td>";
            echo "<td>" . htmlspecialchars($offer['title']) . "</td>";
            echo "<td>" . $display_path . "</td>";
            echo "<td>" . $file_exists . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error fetching offers: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>View Homepage</a></p>";
?>