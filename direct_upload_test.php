<?php
/**
 * Direct Upload Test Script
 * This script tests the upload functionality directly without going through the web interface
 */

// Include app configuration
include 'config/app.php';

echo "<h1>Direct Upload Test</h1>\n";
echo "<p>Testing upload functionality directly...</p>\n";

// Test the upload_dir function
echo "<h2>Testing upload_dir() function</h2>\n";

$test_paths = [
    '',
    'credit_cards',
    'offers'
];

foreach ($test_paths as $path) {
    $full_path = upload_dir($path);
    echo "<p>upload_dir('$path') = $full_path</p>\n";
    
    // Check if directory exists
    if (is_dir($full_path)) {
        echo "<p style='color: green;'>✓ Directory exists</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>\n";
        // Try to create it
        if (mkdir($full_path, 0755, true)) {
            echo "<p style='color: green;'>✓ Directory created successfully</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Failed to create directory</p>\n";
        }
    }
    
    // Check if directory is writable
    if (is_writable($full_path)) {
        echo "<p style='color: green;'>✓ Directory is writable</p>\n";
    } else {
        echo "<p style='color: red;'>✗ Directory is not writable</p>\n";
        // Try to fix permissions
        if (chmod($full_path, 0755)) {
            echo "<p style='color: green;'>✓ Permissions fixed</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Failed to fix permissions</p>\n";
        }
    }
    
    echo "<hr>\n";
}

// Test actual file creation
echo "<h2>Testing Actual File Creation</h2>\n";

$test_content = "This is a test file created at " . date('Y-m-d H:i:s') . "\n";
$test_filename = "direct_test_" . time() . ".txt";

// Test main uploads directory
$main_upload_dir = upload_dir();
$test_file_path = $main_upload_dir . '/' . $test_filename;

echo "<p>Creating test file in main uploads directory: $test_file_path</p>\n";

if (file_put_contents($test_file_path, $test_content)) {
    echo "<p style='color: green;'>✓ Test file created successfully in main directory</p>\n";
    
    // Verify file exists
    if (file_exists($test_file_path)) {
        echo "<p style='color: green;'>✓ Test file confirmed to exist</p>\n";
        
        // Clean up
        if (unlink($test_file_path)) {
            echo "<p style='color: green;'>✓ Test file cleaned up successfully</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠ Could not clean up test file</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Test file does not exist after creation</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to create test file in main directory</p>\n";
}

// Test credit cards directory
$cc_upload_dir = upload_dir('credit_cards');
$cc_test_file_path = $cc_upload_dir . '/' . $test_filename;

echo "<p>Creating test file in credit cards directory: $cc_test_file_path</p>\n";

if (file_put_contents($cc_test_file_path, $test_content)) {
    echo "<p style='color: green;'>✓ Test file created successfully in credit cards directory</p>\n";
    
    // Verify file exists
    if (file_exists($cc_test_file_path)) {
        echo "<p style='color: green;'>✓ Test file confirmed to exist</p>\n";
        
        // Clean up
        if (unlink($cc_test_file_path)) {
            echo "<p style='color: green;'>✓ Test file cleaned up successfully</p>\n";
        } else {
            echo "<p style='color: orange;'>⚠ Could not clean up test file</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Test file does not exist after creation</p>\n";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to create test file in credit cards directory</p>\n";
}

echo "<h2>Summary</h2>\n";
echo "<p>If all tests show green checkmarks, the upload functionality should work correctly.</p>\n";
echo "<p>You can now test the web interface:</p>\n";
echo "<ul>\n";
echo "<li><a href='admin/upload_offer.php'>Upload Offer</a></li>\n";
echo "<li><a href='admin/manage_credit_cards.php'>Manage Credit Cards</a></li>\n";
echo "</ul>\n";

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
?>