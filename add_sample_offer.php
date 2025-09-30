<?php
include 'config/db.php';

echo "<h1>Adding Sample Offer for Testing</h1>";

// Add a sample offer to the database using existing category ID
try {
    $stmt = $pdo->prepare("INSERT INTO offers (category_id, title, description, price, image, redirect_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        23, // category_id (using existing category)
        'Sample Offer for Testing', // title
        'This is a sample offer for testing the Trending Promotion Tasks section.', // description
        99.99, // price
        'uploads/offers/sample-offer.jpg', // image
        'https://example.com/sample-offer', // redirect_url
    ]);
    
    $offer_id = $pdo->lastInsertId();
    echo "<p style='color: green;'>✓ Sample offer added successfully with ID: $offer_id</p>";
    
    // Add another sample offer
    $stmt = $pdo->prepare("INSERT INTO offers (category_id, title, description, price, image, redirect_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        24, // category_id (using existing category)
        'Second Sample Offer', // title
        'This is another sample offer for testing.', // description
        149.99, // price
        'uploads/offers/second-sample.jpg', // image
        'https://example.com/second-offer', // redirect_url
    ]);
    
    $offer_id2 = $pdo->lastInsertId();
    echo "<p style='color: green;'>✓ Second sample offer added successfully with ID: $offer_id2</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error adding sample offer: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>View Homepage with Trending Tasks</a></p>";
?>