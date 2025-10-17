<?php
session_start();
include 'config/db.php';

echo "<h2>Credit Cards Data Check</h2>";

if ($pdo) {
    try {
        // Fetch all credit cards
        $stmt = $pdo->query("SELECT * FROM credit_cards ORDER BY created_at DESC");
        $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Total Credit Cards: " . count($credit_cards) . "</h3>";
        
        if (empty($credit_cards)) {
            echo "<p>No credit cards found in the database.</p>";
        } else {
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Title</th>";
            echo "<th>Image</th>";
            echo "<th>Link</th>";
            echo "<th>Amount</th>";
            echo "<th>Percentage</th>";
            echo "<th>Flat Rate</th>";
            echo "<th>Is Active</th>";
            echo "<th>Created At</th>";
            echo "</tr>";
            
            foreach ($credit_cards as $card) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($card['id']) . "</td>";
                echo "<td>" . htmlspecialchars($card['title']) . "</td>";
                echo "<td>" . htmlspecialchars($card['image']) . "</td>";
                echo "<td>" . htmlspecialchars($card['link']) . "</td>";
                echo "<td>" . htmlspecialchars($card['amount']) . "</td>";
                echo "<td>" . htmlspecialchars($card['percentage']) . "</td>";
                echo "<td>" . htmlspecialchars($card['flat_rate']) . "</td>";
                echo "<td>" . ($card['is_active'] ? 'Yes' : 'No') . "</td>";
                echo "<td>" . htmlspecialchars($card['created_at']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error fetching credit cards: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?>