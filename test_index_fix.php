<?php
// Test script to verify the credit cards fix
session_start();
include 'config/db.php';
include 'config/app.php';

// Normalize image path to an absolute URL using BASE_URL
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
    return url($path);
}

echo "<h2>Testing Credit Cards Fix</h2>";

if ($pdo) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Fetch active credit cards (using the fixed query)
    try {
        echo "<h3>Fetching credit cards...</h3>";
        $stmt = $pdo->query("SELECT id, title, image, link, amount, percentage, flat_rate, is_active, created_at FROM credit_cards ORDER BY created_at DESC LIMIT 4");
        $credit_cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Found " . count($credit_cards) . " credit cards</p>";
        
        // Normalize image paths for credit cards
        foreach ($credit_cards as &$card) {
            if (!empty($card['image'])) {
                $card['image'] = normalize_image($card['image']);
            }
        }
        
        if (empty($credit_cards)) {
            echo "<p>No credit cards found. This might indicate:</p>";
            echo "<ul>";
            echo "<li>No credit cards have been added yet</li>";
            echo "<li>All credit cards are marked as inactive</li>";
            echo "<li>There's a database connection issue</li>";
            echo "</ul>";
        } else {
            echo "<h3>Credit Cards Display Test:</h3>";
            echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";
            foreach ($credit_cards as $card) {
                echo "<div style='border: 1px solid #ddd; border-radius: 8px; padding: 15px; width: 300px;'>";
                echo "<h4>" . htmlspecialchars($card['title']) . "</h4>";
                if (!empty($card['image'])) {
                    echo "<img src='" . htmlspecialchars($card['image']) . "' alt='" . htmlspecialchars($card['title']) . "' style='width: 100%; height: 150px; object-fit: contain;'>";
                } else {
                    echo "<div style='height: 150px; display: flex; align-items: center; justify-content: center; background: #f5f5f5;'>";
                    echo "<span>No Image</span>";
                    echo "</div>";
                }
                echo "<p><strong>Amount:</strong> ₹" . number_format($card['amount'], 2) . "</p>";
                echo "<p><strong>Percentage:</strong> " . number_format($card['percentage'], 2) . "%</p>";
                echo "<p><strong>Flat Rate:</strong> ₹" . number_format($card['flat_rate'], 2) . "</p>";
                echo "<p><strong>Status:</strong> " . ($card['is_active'] ? 'Active' : 'Inactive') . "</p>";
                echo "</div>";
            }
            echo "</div>";
        }
    } catch(PDOException $e) {
        echo "<p style='color: red;'>Error fetching credit cards: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

echo "<h3>Debug Information:</h3>";
echo "<p><strong>BASE_URL:</strong> " . BASE_URL . "</p>";
?>