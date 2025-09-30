<?php
// This is a simplified test file to demonstrate the filtering functionality
// It shows how the sorting would work with sample data

// Sample offers data (simulating database results)
$sample_offers = [
    ['id' => 1, 'title' => 'Sample Offer 1', 'price' => 99.99, 'image' => '', 'created_at' => '2025-09-20'],
    ['id' => 2, 'title' => 'Sample Offer 2', 'price' => 149.99, 'image' => '', 'created_at' => '2025-09-22'],
    ['id' => 3, 'title' => 'Sample Offer 3', 'price' => 79.99, 'image' => '', 'created_at' => '2025-09-18'],
    ['id' => 4, 'title' => 'Sample Offer 4', 'price' => 199.99, 'image' => '', 'created_at' => '2025-09-25'],
];

// Function to sort offers based on selected criteria
function sortOffers($offers, $sortOption) {
    usort($offers, function($a, $b) use ($sortOption) {
        switch ($sortOption) {
            case 'price_asc':
                return $a['price'] <=> $b['price'];
            case 'newest':
                return strtotime($b['created_at']) <=> strtotime($a['created_at']);
            case 'oldest':
                return strtotime($a['created_at']) <=> strtotime($b['created_at']);
            case 'price_desc':
            default:
                return $b['price'] <=> $a['price'];
        }
    });
    return $offers;
}

// Test different sorting options
echo "<h2>Testing Offer Sorting</h2>";

echo "<h3>Default (Price High to Low)</h3>";
$sorted = sortOffers($sample_offers, 'price_desc');
foreach ($sorted as $offer) {
    echo "Offer: " . $offer['title'] . " - Price: ₹" . number_format($offer['price'], 2) . "<br>";
}

echo "<h3>Price Low to High</h3>";
$sorted = sortOffers($sample_offers, 'price_asc');
foreach ($sorted as $offer) {
    echo "Offer: " . $offer['title'] . " - Price: ₹" . number_format($offer['price'], 2) . "<br>";
}

echo "<h3>Newest First</h3>";
$sorted = sortOffers($sample_offers, 'newest');
foreach ($sorted as $offer) {
    echo "Offer: " . $offer['title'] . " - Date: " . $offer['created_at'] . "<br>";
}
?>