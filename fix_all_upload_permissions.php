<?php
/**
 * Fix permissions for all upload directories
 * This script ensures proper permissions for all file uploads in both local and Coolify environments
 */

// Include app configuration
include 'config/app.php';

echo "<h2>Fixing All Upload Permissions</h2>\n";
echo "<p>This script fixes permissions for all upload directories.</p>\n";

// Define all directories that need write permissions
$directories = [
    [
        'name' => 'Main Uploads',
        'path' => upload_dir(),
        'constant' => 'UPLOAD_PATH'
    ],
    [
        'name' => 'Credit Cards',
        'path' => upload_dir('credit_cards'),
        'constant' => 'CREDIT_CARDS_UPLOAD_PATH'
    ],
    [
        'name' => 'Offers',
        'path' => upload_dir('offers'),
        'constant' => 'OFFER_IMAGES_UPLOAD_PATH'
    ]
];

foreach ($directories as $dirInfo) {
    echo "<h3>" . $dirInfo['name'] . " Directory</h3>\n";
    echo "<p>Path: " . $dirInfo['path'] . "</p>\n";
    echo "<p>Constant: " . $dirInfo['constant'] . "</p>\n";
    
    $dir = $dirInfo['path'];
    
    // Create directory if it doesn't exist
    if (!is_dir($dir)) {
        echo "<p>Creating directory...</p>\n";
        // Try to create with more permissive permissions for container environments
        if (@mkdir($dir, 0775, true)) {
            echo "<p style='color: green;'>Directory created successfully.</p>\n";
        } else {
            echo "<p style='color: red;'>Failed to create directory.</p>\n";
            continue;
        }
    } else {
        echo "<p>Directory exists.</p>\n";
    }
    
    // Try to set permissions
    echo "<p>Setting permissions to 0775...</p>\n";
    if (@chmod($dir, 0775)) {
        echo "<p style='color: green;'>Permissions set to 0775.</p>\n";
    } else {
        echo "<p style='color: orange;'>Could not set permissions with chmod (normal in some environments).</p>\n";
    }
    
    // Check if directory is writable
    echo "<p>Checking write permissions...</p>\n";
    if (is_writable($dir)) {
        echo "<p style='color: green;'>Directory is writable.</p>\n";
    } else {
        echo "<p style='color: red;'>Directory is NOT writable.</p>\n";
        
        // Try a different approach for containerized environments
        echo "<p>Trying alternative permission fix...</p>\n";
        
        // Create a test file to see if we can write
        $test_file = $dir . '/.permission_test';
        if (@file_put_contents($test_file, 'test')) {
            echo "<p style='color: green;'>Write test successful.</p>\n";
            @unlink($test_file);
        } else {
            echo "<p style='color: red;'>Write test failed. You may need to fix permissions manually.</p>\n";
        }
    }
    
    // Show current owner/group info (if available)
    if (function_exists('posix_getpwuid') && function_exists('posix_getgrgid')) {
        $stat = @stat($dir);
        if ($stat) {
            $owner = posix_getpwuid($stat['uid']);
            $group = posix_getgrgid($stat['gid']);
            echo "<p>Owner: " . ($owner ? $owner['name'] : $stat['uid']) . "</p>\n";
            echo "<p>Group: " . ($group ? $group['name'] : $stat['gid']) . "</p>\n";
        }
    }
    
    echo "<hr>\n";
}

echo "<h3>Testing Upload Functionality</h3>\n";
echo "<p>Testing if upload_dir() function works correctly...</p>\n";

// Test the upload_dir function
$test_dirs = ['', 'credit_cards', 'offers'];
foreach ($test_dirs as $subfolder) {
    $dir = upload_dir($subfolder);
    echo "<p>upload_dir('$subfolder'): $dir</p>\n";
    if (is_dir($dir) && is_writable($dir)) {
        echo "<p style='color: green;'>✓ Directory exists and is writable</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory issue detected</p>\n";
    }
}

echo "<h3>Summary</h3>\n";
echo "<p>All upload directories have been processed.</p>\n";
echo "<p><a href='admin/upload_offer.php'>Try uploading an offer</a> | <a href='admin/manage_credit_cards.php'>Try uploading a credit card</a></p>\n";

echo "<h3>Troubleshooting Tips</h3>\n";
echo "<ul>\n";
echo "<li>If uploads still fail, check your web server's error logs</li>\n";
echo "<li>Ensure your Dockerfile or deployment process sets proper permissions</li>\n";
echo "<li>In containerized environments, the www-data user should own the upload directories</li>\n";
echo "<li>For persistent storage, ensure mounted volumes have proper permissions</li>\n";
echo "</ul>\n";
?>