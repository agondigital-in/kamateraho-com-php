<?php
include 'config/app.php';

echo "<h1>Test Upload Paths</h1>";

echo "<h3>Configured Paths:</h3>";
echo "<ul>";
echo "<li>Base Upload Path: " . (defined('UPLOAD_PATH') ? UPLOAD_PATH : 'Not defined') . "</li>";
echo "<li>Credit Cards Upload Path: " . (defined('CREDIT_CARDS_UPLOAD_PATH') ? CREDIT_CARDS_UPLOAD_PATH : 'Not defined') . "</li>";
echo "<li>Offer Images Upload Path: " . (defined('OFFER_IMAGES_UPLOAD_PATH') ? OFFER_IMAGES_UPLOAD_PATH : 'Not defined') . "</li>";
echo "</ul>";

echo "<h3>Directory Paths:</h3>";
echo "<ul>";
echo "<li>Base Upload Directory: " . upload_dir() . "</li>";
echo "<li>Credit Cards Directory: " . upload_dir('credit_cards') . "</li>";
echo "</ul>";

echo "<h3>Testing Directory Creation and Permissions:</h3>";

$test_directories = [
    ['name' => 'Base Upload Directory', 'path' => upload_dir()],
    ['name' => 'Credit Cards Directory', 'path' => upload_dir('credit_cards')]
];

foreach ($test_directories as $dir_info) {
    echo "<h4>{$dir_info['name']}</h4>";
    echo "<p>Path: {$dir_info['path']}</p>";
    
    // Check if directory exists
    if (is_dir($dir_info['path'])) {
        echo "<p style='color: green;'>✓ Directory exists</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Directory does not exist, attempting to create...</p>";
        if (mkdir($dir_info['path'], 0777, true)) {
            echo "<p style='color: green;'>✓ Directory created successfully</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create directory</p>";
        }
    }
    
    // Check permissions
    if (is_dir($dir_info['path'])) {
        if (is_writable($dir_info['path'])) {
            echo "<p style='color: green;'>✓ Directory is writable</p>";
            
            // Test file creation
            $test_file = $dir_info['path'] . '/test_permissions.txt';
            $test_content = "Test file created at: " . date('Y-m-d H:i:s');
            
            if (file_put_contents($test_file, $test_content)) {
                echo "<p style='color: green;'>✓ File creation test passed</p>";
                
                // Clean up test file
                if (unlink($test_file)) {
                    echo "<p style='color: green;'>✓ Test file cleaned up</p>";
                } else {
                    echo "<p style='color: orange;'>⚠ Could not clean up test file</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ File creation test failed</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Directory is not writable</p>";
        }
        
        echo "<p>Permissions: " . substr(sprintf('%o', fileperms($dir_info['path'])), -4) . "</p>";
    }
    
    echo "<hr>";
}

echo "<h3>URL Paths:</h3>";
echo "<ul>";
echo "<li>Base Upload URL: " . url(UPLOAD_PATH) . "</li>";
echo "<li>Credit Cards URL: " . url(CREDIT_CARDS_UPLOAD_PATH) . "</li>";
echo "<li>Offer Images URL: " . url(OFFER_IMAGES_UPLOAD_PATH) . "</li>";
echo "</ul>";

echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>