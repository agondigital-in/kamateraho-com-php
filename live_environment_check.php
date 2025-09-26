<?php
/**
 * Live Environment Check Script
 * This script checks the current state of the live environment and verifies upload functionality
 */

// Include app configuration
include 'config/app.php';
include 'config/db.php';

echo "<h1>Live Environment Check</h1>\n";
echo "<p>Checking current environment status...</p>\n";

// Environment Information
echo "<h2>Environment Information</h2>\n";
echo "<ul>\n";
echo "<li>PHP Version: " . phpversion() . "</li>\n";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>\n";
echo "<li>Operating System: " . php_uname() . "</li>\n";
echo "<li>Current Directory: " . __DIR__ . "</li>\n";
echo "</ul>\n";

// Check if we're in a containerized environment
$isContainer = false;
if (file_exists('/.dockerenv') || (isset($_ENV['container']) && $_ENV['container'] === 'docker')) {
    $isContainer = true;
    echo "<p style='color: orange;'>Containerized environment detected (Docker/LXC/Coolify)</p>\n";
} else {
    echo "<p>Standard environment detected</p>\n";
}

// Check App Configuration
echo "<h2>App Configuration</h2>\n";
echo "<ul>\n";
echo "<li>BASE_URL: " . BASE_URL . "</li>\n";
echo "<li>UPLOAD_PATH: " . UPLOAD_PATH . "</li>\n";
echo "<li>CREDIT_CARDS_UPLOAD_PATH: " . CREDIT_CARDS_UPLOAD_PATH . "</li>\n";
echo "<li>OFFER_IMAGES_UPLOAD_PATH: " . OFFER_IMAGES_UPLOAD_PATH . "</li>\n";
echo "</ul>\n";

// Check Upload Directory Functions
echo "<h2>Upload Directory Functions</h2>\n";
$test_dirs = [
    ['name' => 'Main Uploads', 'function_result' => upload_dir(), 'path_constant' => UPLOAD_PATH],
    ['name' => 'Credit Cards', 'function_result' => upload_dir('credit_cards'), 'path_constant' => CREDIT_CARDS_UPLOAD_PATH],
    ['name' => 'Offers', 'function_result' => upload_dir('offers'), 'path_constant' => OFFER_IMAGES_UPLOAD_PATH]
];

foreach ($test_dirs as $dirInfo) {
    echo "<h3>" . $dirInfo['name'] . "</h3>\n";
    echo "<p>upload_dir() result: " . $dirInfo['function_result'] . "</p>\n";
    echo "<p>Path constant: " . $dirInfo['path_constant'] . "</p>\n";
    
    $dir = $dirInfo['function_result'];
    
    // Check if directory exists
    if (is_dir($dir)) {
        echo "<p style='color: green;'>✓ Directory exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>\n";
        // Try to create it
        if (@mkdir($dir, 0755, true)) {
            echo "<p style='color: green;'>✓ Directory created successfully</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Failed to create directory</p>\n";
        }
    }
    
    // Check permissions
    if (is_dir($dir)) {
        // Check if directory is writable
        if (is_writable($dir)) {
            echo "<p style='color: green;'>✓ Directory is writable</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Directory is not writable</p>\n";
            
            // Try to fix permissions
            if (@chmod($dir, 0755)) {
                echo "<p style='color: green;'>✓ Permissions fixed to 0755</p>\n";
                if (is_writable($dir)) {
                    echo "<p style='color: green;'>✓ Directory is now writable</p>\n";
                }
            } else {
                echo "<p style='color: orange;'>⚠ Could not change permissions</p>\n";
            }
        }
        
        // Try to create a test file
        $test_file = $dir . '/test_' . time() . '.txt';
        $test_content = "Test file created at " . date('Y-m-d H:i:s') . " in " . $dirInfo['name'] . " directory\n";
        
        if (@file_put_contents($test_file, $test_content)) {
            echo "<p style='color: green;'>✓ Successfully created test file</p>\n";
            // Clean up
            @unlink($test_file);
        } else {
            echo "<p style='color: red;'>✗ Failed to create test file</p>\n";
        }
    }
    
    echo "<hr>\n";
}

// Database Connection Test
echo "<h2>Database Connection Test</h2>\n";
try {
    // Test basic connection
    $stmt = $pdo->query("SELECT 1");
    if ($stmt) {
        echo "<p style='color: green;'>✓ Database connection successful</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Database connection failed</p>\n";
    }
    
    // Test if required tables exist
    $tables = ['credit_cards', 'offers', 'offer_images', 'categories'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Table '$table' exists</p>\n";
            } else {
                echo "<p style='color: orange;'>⚠ Table '$table' does not exist</p>\n";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ Error checking table '$table': " . $e->getMessage() . "</p>\n";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection error: " . $e->getMessage() . "</p>\n";
}

// File Upload Simulation Test
echo "<h2>File Upload Simulation Test</h2>\n";
echo "<p>Simulating file upload process...</p>\n";

// Test credit cards upload directory
$cc_dir = upload_dir('credit_cards');
echo "<p>Credit Cards Directory: $cc_dir</p>\n";

if (is_dir($cc_dir) && is_writable($cc_dir)) {
    $test_file = $cc_dir . '/simulation_test_' . time() . '.png';
    $test_content = str_repeat('A', 1024); // 1KB of test data
    
    if (@file_put_contents($test_file, $test_content)) {
        echo "<p style='color: green;'>✓ Credit cards upload simulation successful</p>\n";
        // Verify the file was created
        if (file_exists($test_file)) {
            echo "<p style='color: green;'>✓ Test file confirmed on disk</p>\n";
            // Clean up
            @unlink($test_file);
        } else {
            echo "<p style='color: red;'>✗ Test file not found on disk</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Credit cards upload simulation failed</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ Credit cards directory not writable</p>\n";
}

// Test offers upload directory
$offers_dir = upload_dir('offers');
echo "<p>Offers Directory: $offers_dir</p>\n";

if (is_dir($offers_dir) && is_writable($offers_dir)) {
    $test_file = $offers_dir . '/simulation_test_' . time() . '.jpg';
    $test_content = str_repeat('B', 2048); // 2KB of test data
    
    if (@file_put_contents($test_file, $test_content)) {
        echo "<p style='color: green;'>✓ Offers upload simulation successful</p>\n";
        // Verify the file was created
        if (file_exists($test_file)) {
            echo "<p style='color: green;'>✓ Test file confirmed on disk</p>\n";
            // Clean up
            @unlink($test_file);
        } else {
            echo "<p style='color: red;'>✗ Test file not found on disk</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Offers upload simulation failed</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ Offers directory not writable</p>\n";
}

// Summary and Recommendations
echo "<h2>Summary and Recommendations</h2>\n";

$issues = 0;
$warnings = 0;

// Check all directories
foreach ($test_dirs as $dirInfo) {
    $dir = $dirInfo['function_result'];
    if (!is_dir($dir)) {
        $issues++;
    } elseif (!is_writable($dir)) {
        $issues++;
    }
}

if ($issues > 0) {
    echo "<p style='color: red; font-weight: bold;'>✗ $issues critical issues found that need to be fixed</p>\n";
    echo "<p>Recommendations:</p>\n";
    echo "<ol>\n";
    echo "<li>Run the fix_all_upload_permissions.php script</li>\n";
    echo "<li>Check directory ownership (should be www-data:www-data in containerized environments)</li>\n";
    echo "<li>Ensure sufficient disk space is available</li>\n";
    echo "</ol>\n";
} else {
    echo "<p style='color: green; font-weight: bold;'>✓ All upload directories are properly configured</p>\n";
    echo "<p>Upload functionality should work correctly.</p>\n";
}

echo "<h2>Next Steps</h2>\n";
echo "<ul>\n";
echo "<li><a href='admin/upload_offer.php'>Test Offer Upload</a></li>\n";
echo "<li><a href='admin/manage_credit_cards.php'>Test Credit Card Upload</a></li>\n";
echo "<li><a href='fix_all_upload_permissions.php'>Run Permission Fix Script</a></li>\n";
echo "</ul>\n";

echo "<p>Last checked: " . date('Y-m-d H:i:s') . "</p>\n";
?>