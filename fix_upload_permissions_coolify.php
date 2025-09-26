<?php
/**
 * Fix upload permissions for Coolify deployment
 * This script specifically addresses permission issues in containerized environments
 */

// Include app configuration
include 'config/app.php';

echo "<h2>Fixing Upload Permissions for Coolify</h2>\n";
echo "<p>This script fixes common permission issues in containerized environments.</p>\n";

// Define directories that need write permissions
$directories = [
    __DIR__ . '/uploads',
    __DIR__ . '/uploads/credit_cards',
    __DIR__ . '/uploads/offers'
];

foreach ($directories as $dir) {
    echo "<h3>Processing: " . basename($dir) . "</h3>\n";
    
    // Create directory if it doesn't exist
    if (!is_dir($dir)) {
        echo "<p>Creating directory...</p>\n";
        if (mkdir($dir, 0755, true)) {
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

echo "<h3>Additional Recommendations for Coolify:</h3>\n";
echo "<ol>\n";
echo "<li>Make sure your Dockerfile or build process sets proper permissions</li>\n";
echo "<li>Consider adding this to your Dockerfile:<br>\n";
echo "<pre>RUN mkdir -p /app/uploads && chmod -R 775 /app/uploads</pre></li>\n";
echo "<li>If using persistent storage, ensure the mounted volume has proper permissions</li>\n";
echo "</ol>\n";

echo "<p><a href='admin/manage_credit_cards.php'>Try uploading a credit card now</a></p>\n";
?>