<?php
session_start();
include 'config/db.php';

// Function to normalize image path (copied from index.php)
function normalize_image($path) {
    if (!$path) return '';
    // If already absolute URL, return as-is
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    // Remove leading ../ if present from legacy stored paths
    $path = preg_replace('#^\.\./#', '', $path);
    // Ensure no leading slash issues
    $path = ltrim($path, '/');  
    // Build absolute URL
    return "http://localhost/kmt/" . $path;
}

echo "<h2>Testing Credit Cards Display</h2>";

if ($pdo) {
    try {
        // Fetch credit cards (same query as in index.php)
        $stmt = $pdo->query("SELECT id, title, image, link, amount, percentage, flat_rate, is_active, created_at FROM credit_cards ORDER BY created_at DESC LIMIT 4");
        $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Credit Cards Found: " . count($credit_cards) . "</h3>";
        
        // Normalize image paths
        foreach ($credit_cards as &$card) {
            if (!empty($card['image'])) {
                $card['image'] = normalize_image($card['image']);
            }
        }
        
        if (empty($credit_cards)) {
            echo "<p>No credit cards found in the database.</p>";
        } else {
            echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";
            foreach ($credit_cards as $card) {
                echo "<div style='border: 1px solid #ccc; padding: 10px; width: 300px;'>";
                echo "<h4>" . htmlspecialchars($card['title']) . "</h4>";
                if (!empty($card['image'])) {
                    echo "<img src='" . htmlspecialchars($card['image']) . "' alt='" . htmlspecialchars($card['title']) . "' style='width: 100%; height: 150px; object-fit: contain;'>";
                } else {
                    echo "<div style='height: 150px; display: flex; align-items: center; justify-content: center; background: #f0f0f0;'>";
                    echo "<span>No Image</span>";
                    echo "</div>";
                }
                echo "<p>Amount: ₹" . number_format($card['amount'], 2) . "</p>";
                echo "<p>Percentage: " . number_format($card['percentage'], 2) . "%</p>";
                echo "<p>Flat Rate: ₹" . number_format($card['flat_rate'], 2) . "</p>";
                echo "<p>Active: " . ($card['is_active'] ? 'Yes' : 'No') . "</p>";
                echo "</div>";
            }
            echo "</div>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error fetching credit cards: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>Database connection failed.</p>";
}
?>