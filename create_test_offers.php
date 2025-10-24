<?php
// Script to create test offers with different price types
include 'config/db.php';

try {
    // Create test offers with different price types
    $test_offers = [
        [
            'category_id' => 28,
            'title' => 'Test Fixed Price Offer',
            'description' => 'This is a test offer with a fixed price',
            'price' => 150.00,
            'price_type' => 'fixed',
            'image' => 'https://via.placeholder.com/300x200/4CAF50/white?text=Fixed+Price',
            'redirect_url' => 'https://example.com/fixed',
            'sequence_id' => 100
        ],
        [
            'category_id' => 28,
            'title' => 'Test Flat Percent Offer',
            'description' => 'This is a test offer with a flat percent price',
            'price' => 15.50,
            'price_type' => 'flat_percent',
            'image' => 'https://via.placeholder.com/300x200/2196F3/white?text=Flat+Percent',
            'redirect_url' => 'https://example.com/flatpercent',
            'sequence_id' => 101
        ],
        [
            'category_id' => 28,
            'title' => 'Test Upto Percent Offer',
            'description' => 'This is a test offer with an upto percent price',
            'price' => 250.00,
            'price_type' => 'upto_percent',
            'image' => 'https://via.placeholder.com/300x200/FFC107/white?text=Upto+Percent',
            'redirect_url' => 'https://example.com/uptopercent',
            'sequence_id' => 102
        ]
    ];

    foreach ($test_offers as $offer) {
        $stmt = $pdo->prepare("INSERT INTO offers (category_id, title, description, price, price_type, image, redirect_url, sequence_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $offer['category_id'],
            $offer['title'],
            $offer['description'],
            $offer['price'],
            $offer['price_type'],
            $offer['image'],
            $offer['redirect_url'],
            $offer['sequence_id']
        ]);
        
        echo "Created test offer: " . $offer['title'] . " with price type: " . $offer['price_type'] . "\n";
    }
    
    echo "Test offers created successfully!\n";
} catch (PDOException $e) {
    echo "Error creating test offers: " . $e->getMessage() . "\n";
}
?>