<?php
/**
 * Coolify Permission Fix Script
 * Specifically designed to fix permission issues in Coolify containerized environments
 */

echo "<h1>Coolify Permission Fix</h1>\n";
echo "<p>Fixing permissions for containerized environment...</p>\n";

// Define all upload directories
$directories = [
    '/app/uploads',
    '/app/uploads/credit_cards',
    '/app/uploads/offers',
    __DIR__ . '/uploads',
    __DIR__ . '/uploads/credit_cards',
    __DIR__ . '/uploads/offers'
];

echo "<h2>Processing Directories</h2>\n";

foreach ($directories as $dir) {
    echo "<h3>Processing: $dir</h3>\n";
    
    // Check if directory exists
    if (is_dir($dir)) {
        echo "<p>Directory exists</p>\n";
    } else {
        echo "<p>Directory does not exist, creating...</p>\n";
        // Try to create directory with more permissive permissions
        if (@mkdir($dir, 0775, true)) {
            echo "<p style='color: green;'>✓ Directory created successfully</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Failed to create directory</p>\n";
            continue;
        }
    }
    
    // Try to set permissions to 0775 (standard for web directories)
    echo "<p>Setting permissions to 0775...</p>\n";
    if (@chmod($dir, 0775)) {
        echo "<p style='color: green;'>✓ Permissions set to 0775</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠ Could not set permissions with chmod (this is normal in some containerized environments)</p>\n";
    }
    
    // Try to change ownership to www-data (common in containerized environments)
    echo "<p>Attempting to change ownership to www-data...</p>\n";
    if (function_exists('posix_getpwnam')) {
        $user = posix_getpwnam('www-data');
        if ($user) {
            if (@chown($dir, $user['uid'])) {
                echo "<p style='color: green;'>✓ Ownership changed to www-data</p>\n";
            } else {
                echo "<p style='color: orange;'>⚠ Could not change ownership</p>\n";
            }
        } else {
            echo "<p style='color: orange;'>⚠ www-data user not found</p>\n";
        }
    } else {
        echo "<p style='color: orange;'>⚠ posix functions not available</p>\n";
    }
    
    // Check if directory is writable
    echo "<p>Checking if directory is writable...</p>\n";
    if (is_writable($dir)) {
        echo "<p style='color: green;'>✓ Directory is writable</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory is NOT writable</p>\n";
        
        // Try to create a test file to verify actual write capability
        $test_file = $dir . '/.write_test_' . time();
        if (@file_put_contents($test_file, 'test')) {
            echo "<p style='color: green;'>✓ Write test successful</p>\n";
            @unlink($test_file);
        } else {
            echo "<p style='color: red;'>✗ Write test failed</p>\n";
        }
    }
    
    echo "<hr>\n";
}

echo "<h2>Environment Information</h2>\n";
echo "<ul>\n";
echo "<li>Current user: " . (function_exists('posix_getuid') ? posix_getuid() : 'N/A') . "</li>\n";
echo "<li>Current group: " . (function_exists('posix_getgid') ? posix_getgid() : 'N/A') . "</li>\n";
echo "<li>PHP user: " . get_current_user() . "</li>\n";
echo "<li>Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>\n";
echo "</ul>\n";

// Try to determine the web server user
echo "<h2>Web Server User Detection</h2>\n";
$webServerUser = 'unknown';

// Try common methods to determine web server user
if (function_exists('posix_getpwuid')) {
    $processUser = posix_getpwuid(posix_geteuid());
    if ($processUser) {
        $webServerUser = $processUser['name'];
        echo "<p>Detected web server user: $webServerUser</p>\n";
    }
}

// Try to get from environment variables
if ($webServerUser === 'unknown') {
    $envUser = getenv('APACHE_RUN_USER') ?: getenv('NGINX_USER') ?: getenv('USER') ?: 'www-data';
    echo "<p>Using default web server user: $envUser</p>\n";
    $webServerUser = $envUser;
}

echo "<h2>Recommended Actions</h2>\n";
echo "<ol>\n";
echo "<li>If you have SSH access to your Coolify container, run these commands:</li>\n";
echo "<pre>\n";
echo "mkdir -p /app/uploads /app/uploads/credit_cards /app/uploads/offers\n";
echo "chmod -R 775 /app/uploads\n";
echo "chown -R www-data:www-data /app/uploads\n";
echo "</pre>\n";
echo "<li>If you're using a Dockerfile, make sure it includes:</li>\n";
echo "<pre>\n";
echo "RUN mkdir -p /app/uploads /app/uploads/credit_cards /app/uploads/offers \\\n";
echo "    && chmod -R 775 /app/uploads \\\n";
echo "    && chown -R www-data:www-data /app/uploads\n";
echo "</pre>\n";
echo "<li>Check your Coolify deployment settings for persistent storage if you're using it</li>\n";
echo "</ol>\n";

echo "<h2>Test Upload Functionality</h2>\n";
echo "<p>After applying the fixes, test your upload functionality:</p>\n";
echo "<ul>\n";
echo "<li><a href='/admin/upload_offer.php'>Upload Offer</a></li>\n";
echo "<li><a href='/admin/manage_credit_cards.php'>Manage Credit Cards</a></li>\n";
echo "</ul>\n";

echo "<p>Last updated: " . date('Y-m-d H:i:s') . "</p>\n";
?>