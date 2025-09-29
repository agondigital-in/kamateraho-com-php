<?php
// Create sample images for testing

// Create a directory for images if it doesn't exist
if (!file_exists('img')) {
    mkdir('img', 0777, true);
}

// Create a sample electronics image
$elec = imagecreate(300, 200);
$bg = imagecolorallocate($elec, 200, 200, 200);
$textColor = imagecolorallocate($elec, 0, 0, 0);
imagestring($elec, 5, 80, 90, 'Electronics', $textColor);
imagejpeg($elec, 'img/elec.jpg');
imagedestroy($elec);

// Create a sample fashion image
$fashion = imagecreate(300, 200);
$bg = imagecolorallocate($fashion, 220, 180, 220);
$textColor = imagecolorallocate($fashion, 0, 0, 0);
imagestring($fashion, 5, 100, 90, 'Fashion', $textColor);
imagejpeg($fashion, 'img/fashion.jpg');
imagedestroy($fashion);

// Create a sample offer image
$offer = imagecreate(400, 250);
$bg = imagecolorallocate($offer, 255, 220, 150);
$textColor = imagecolorallocate($offer, 0, 0, 0);
imagestring($offer, 5, 120, 100, 'Special Offer', $textColor);
imagejpeg($offer, 'img/offer1.jpg');
imagedestroy($offer);

echo "Sample images created successfully!";
?>