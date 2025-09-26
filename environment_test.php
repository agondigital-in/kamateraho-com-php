<?php
/**
 * Simple Environment Test Script
 */

echo "<h1>Environment Test</h1>\n";

// Check if we're in a Windows or Unix-like environment
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "<p>Windows environment detected</p>\n";
} else {
    echo "<p>Unix/Linux environment detected</p>\n";
}

// Check PHP version
echo "<p>PHP Version: " . phpversion() . "</p>\n";

// Check if we're in a container
if (file_exists('/.dockerenv')) {
    echo "<p>Docker container environment detected</p>\n";
} else {
    echo "<p>Not in a Docker container</p>\n";
}

// Check current working directory
echo "<p>Current directory: " . getcwd() . "</p>\n";

// Check if config files exist
$config_files = [
    'config/app.php',
    'config/db.php',
    'config/env.php'
];

foreach ($config_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ $file not found</p>\n";
    }
}

// Test including config files
echo "<h2>Testing Configuration Includes</h2>\n";

try {
    include 'config/app.php';
    echo "<p style='color: green;'>✓ app.php included successfully</p>\n";
    echo "<p>BASE_URL: " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . "</p>\n";
    echo "<p>UPLOAD_PATH: " . (defined('UPLOAD_PATH') ? UPLOAD_PATH : 'Not defined') . "</p>\n";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error including app.php: " . $e->getMessage() . "</p>\n";
}

// Test database connection
echo "<h2>Testing Database Connection</h2>\n";

try {
    include 'config/db.php';
    echo "<p style='color: green;'>✓ db.php included successfully</p>\n";
    
    // Test connection
    $stmt = $pdo->query("SELECT 1");
    if ($stmt) {
        echo "<p style='color: green;'>✓ Database connection successful</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error with database: " . $e->getMessage() . "</p>\n";
}

echo "<h2>Upload Directory Test</h2>\n";

if (function_exists('upload_dir')) {
    echo "<p style='color: green;'>✓ upload_dir() function available</p>\n";
    
    $upload_path = upload_dir();
    echo "<p>Main upload directory: $upload_path</p>\n";
    
    if (is_dir($upload_path)) {
        echo "<p style='color: green;'>✓ Upload directory exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Upload directory does not exist</p>\n";
    }
    
    if (is_writable($upload_path)) {
        echo "<p style='color: green;'>✓ Upload directory is writable</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Upload directory is not writable</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ upload_dir() function not available</p>\n";
}

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
?>