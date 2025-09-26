<?php
/**
 * Test script for all upload functionalities
 * This helps verify that uploads work in both local and Coolify environments
 */

include 'config/app.php';

echo "<h2>All Uploads Test</h2>\n";

// Test directories
$test_dirs = [
    ['name' => 'Main Uploads', 'path' => upload_dir(), 'constant' => 'UPLOAD_PATH'],
    ['name' => 'Credit Cards', 'path' => upload_dir('credit_cards'), 'constant' => 'CREDIT_CARDS_UPLOAD_PATH'],
    ['name' => 'Offers', 'path' => upload_dir('offers'), 'constant' => 'OFFER_IMAGES_UPLOAD_PATH']
];

foreach ($test_dirs as $dirInfo) {
    echo "<h3>" . $dirInfo['name'] . " Test</h3>\n";
    echo "<p>Path: " . $dirInfo['path'] . "</p>\n";
    echo "<p>Constant: " . $dirInfo['constant'] . " = " . constant($dirInfo['constant']) . "</p>\n";
    
    $dir = $dirInfo['path'];
    
    // Check if directory exists
    if (is_dir($dir)) {
        echo "<p style='color: green;'>✓ Directory exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>\n";
    }
    
    // Check if directory is writable
    if (is_writable($dir)) {
        echo "<p style='color: green;'>✓ Directory is writable</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory is not writable</p>\n";
    }
    
    // Try to create a test file
    $test_file = $dir . '/test_' . time() . '.txt';
    $test_content = "Test file created at " . date('Y-m-d H:i:s') . "\n";
    
    if (@file_put_contents($test_file, $test_content)) {
        echo "<p style='color: green;'>✓ Successfully created test file</p>\n";
        // Clean up
        @unlink($test_file);
    } else {
        echo "<p style='color: red;'>✗ Failed to create test file</p>\n";
    }
    
    echo "<hr>\n";
}

echo "<h3>Function Tests</h3>\n";

// Test upload_dir function
echo "<p>Testing upload_dir() function:</p>\n";
echo "<ul>\n";
echo "<li>upload_dir(): " . upload_dir() . "</li>\n";
echo "<li>upload_dir('credit_cards'): " . upload_dir('credit_cards') . "</li>\n";
echo "<li>upload_dir('offers'): " . upload_dir('offers') . "</li>\n";
echo "</ul>\n";

// Test upload_path function
echo "<p>Testing upload_path() function:</p>\n";
echo "<ul>\n";
echo "<li>upload_path(): " . upload_path() . "</li>\n";
echo "<li>upload_path('credit_cards'): " . upload_path('credit_cards') . "</li>\n";
echo "<li>upload_path('offers'): " . upload_path('offers') . "</li>\n";
echo "</ul>\n";

echo "<h3>Summary</h3>\n";
echo "<p>If all tests show green checkmarks, uploads should work correctly.</p>\n";
echo "<p><a href='admin/upload_offer.php'>Test Offer Upload</a> | <a href='admin/manage_credit_cards.php'>Test Credit Card Upload</a></p>\n";
?>