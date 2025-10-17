<?php
// Simple database test for credit cards
include 'config/db.php';

echo "<h2>Database Test: Credit Cards</h2>";

if ($pdo) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    try {
        // Check if credit_cards table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'credit_cards'");
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "<p style='color: green;'>✓ credit_cards table exists</p>";
            
            // Count total credit cards
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM credit_cards");
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Total credit cards: " . $count['count'] . "</p>";
            
            // Count active credit cards
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM credit_cards WHERE is_active = 1");
            $active_count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Active credit cards: " . $active_count['count'] . "</p>";
            
            // Show sample records
            $stmt = $pdo->query("SELECT id, title, image, is_active, created_at FROM credit_cards ORDER BY created_at DESC LIMIT 5");
            $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($cards)) {
                echo "<h3>Recent Credit Cards:</h3>";
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>Active</th><th>Created</th></tr>";
                foreach ($cards as $card) {
                    echo "<tr>";
                    echo "<td>" . $card['id'] . "</td>";
                    echo "<td>" . htmlspecialchars($card['title']) . "</td>";
                    echo "<td>" . htmlspecialchars($card['image']) . "</td>";
                    echo "<td>" . ($card['is_active'] ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . $card['created_at'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p style='color: red;'>✗ credit_cards table does not exist</p>";
            echo "<p>Please run the database migration scripts to create the table.</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    echo "<p>Please check your database configuration in config/db.php</p>";
}
?>