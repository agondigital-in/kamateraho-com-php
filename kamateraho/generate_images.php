<?php
// Generate sample text files as placeholders for images

// Create the img directory if it doesn't exist
if (!file_exists('img')) {
    mkdir('img', 0777, true);
}

// Create sample text files as placeholders for images
file_put_contents('img/elec.jpg', 'This is a placeholder for electronics category image');
file_put_contents('img/fashion.jpg', 'This is a placeholder for fashion category image');
file_put_contents('img/offer1.jpg', 'This is a placeholder for special offer image');

echo "<h1>Sample image placeholders created successfully!</h1>";
echo "<p>Created elec.jpg, fashion.jpg, and offer1.jpg in the img folder.</p>";
echo "<p>These are text placeholders. Replace them with actual images for production use.</p>";
echo "<br><a href='index.php'>Go to Home Page</a>";
?>