<?php
// Test script to verify price label updating functionality
include 'config/db.php';
include 'includes/price_helper.php';

// Test the format_price function
echo "Testing price formatting:\n";
echo "Fixed price (100.50): " . format_price(100.50, 'fixed') . "\n";
echo "Flat percent price (15.75): " . format_price(15.75, 'flat_percent') . "\n";
echo "Upto percent price (250.00): " . format_price(250.00, 'upto_percent') . "\n";

// Test the display_price function
echo "\nTesting price display:\n";
echo "Fixed price (100.50): " . display_price(100.50, 'fixed') . "\n";
echo "Flat percent price (15.75): " . display_price(15.75, 'flat_percent') . "\n";
echo "Upto percent price (250.00): " . display_price(250.00, 'upto_percent') . "\n";

// Test database query with price types
echo "\nTesting database query with price types:\n";
try {
    $stmt = $pdo->query("SELECT id, title, price, price_type FROM offers WHERE title LIKE 'Test %' ORDER BY id DESC LIMIT 5");
    $offers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($offers as $offer) {
        echo "Offer: " . $offer['title'] . " - Price: " . display_price($offer['price'], $offer['price_type']) . "\n";
    }
} catch (PDOException $e) {
    echo "Error querying database: " . $e->getMessage() . "\n";
}

echo "\nJavaScript functionality for dynamic label updating has been added to:\n";
echo "- admin/upload_offer.php\n";
echo "- admin/edit_offer.php\n";
echo "\nThe price label will automatically update based on the selected price type:\n";
echo "- Fixed (₹) shows 'Price (₹)'\n";
echo "- Flat Percent (%) shows 'Percent (%)'\n";
echo "- Upto Percent (%) shows 'Percent (%)'\n";
?>