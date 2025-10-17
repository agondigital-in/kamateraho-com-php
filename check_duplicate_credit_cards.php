<?php
session_start();
include 'config/db.php';

echo "<h2>Check for Duplicate Credit Cards</h2>";

if ($pdo) {
    try {
        // Fetch all credit cards ordered by title and created_at to identify duplicates
        $stmt = $pdo->query("SELECT id, title, image, link, amount, percentage, flat_rate, is_active, created_at FROM credit_cards ORDER BY title, created_at");
        $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Total credit cards in database: " . count($credit_cards) . "</p>";
        
        // Group cards by title to identify potential duplicates
        $cards_by_title = [];
        foreach ($credit_cards as $card) {
            $cards_by_title[$card['title']][] = $card;
        }
        
        // Check for duplicates
        $duplicates = [];
        foreach ($cards_by_title as $title => $cards) {
            if (count($cards) > 1) {
                $duplicates[$title] = $cards;
            }
        }
        
        if (empty($duplicates)) {
            echo "<p style='color: green;'>✓ No duplicate credit cards found</p>";
        } else {
            echo "<p style='color: red;'>✗ Found " . count($duplicates) . " duplicate credit card titles:</p>";
            echo "<ul>";
            foreach ($duplicates as $title => $cards) {
                echo "<li>" . htmlspecialchars($title) . " (" . count($cards) . " copies)</li>";
            }
            echo "</ul>";
            
            // Show details of duplicates
            echo "<h3>Duplicate Details:</h3>";
            foreach ($duplicates as $title => $cards) {
                echo "<h4>" . htmlspecialchars($title) . "</h4>";
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr><th>ID</th><th>Created At</th><th>Amount</th><th>Active</th></tr>";
                foreach ($cards as $card) {
                    echo "<tr>";
                    echo "<td>" . $card['id'] . "</td>";
                    echo "<td>" . $card['created_at'] . "</td>";
                    echo "<td>₹" . number_format($card['amount'], 2) . "</td>";
                    echo "<td>" . ($card['is_active'] ? 'Yes' : 'No') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        
        // Check for exact duplicates (same title, amount, and image)
        $exact_duplicates = [];
        $card_signatures = [];
        
        foreach ($credit_cards as $card) {
            // Create a signature for each card
            $signature = $card['title'] . '|' . $card['amount'] . '|' . $card['image'];
            if (isset($card_signatures[$signature])) {
                $exact_duplicates[] = $card;
            } else {
                $card_signatures[$signature] = true;
            }
        }
        
        if (empty($exact_duplicates)) {
            echo "<p style='color: green;'>✓ No exact duplicate credit cards found</p>";
        } else {
            echo "<p style='color: red;'>✗ Found " . count($exact_duplicates) . " exact duplicate credit cards:</p>";
            echo "<ul>";
            foreach ($exact_duplicates as $card) {
                echo "<li>ID: " . $card['id'] . " - " . htmlspecialchars($card['title']) . "</li>";
            }
            echo "</ul>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?>