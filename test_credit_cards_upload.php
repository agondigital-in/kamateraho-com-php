<?php
/**
 * Test script for credit cards upload functionality
 * This helps verify that uploads work in both local and Coolify environments
 */

include 'config/app.php';

echo "<h2>Credit Cards Upload Test</h2>\n";

// Test 1: Check if upload directory function works
echo "<h3>Test 1: Upload Directory Function</h3>\n";
$upload_dir = upload_dir('credit_cards');
echo "<p>Upload directory path: $upload_dir</p>\n";

if (is_dir($upload_dir)) {
    echo "<p style='color: green;'>Upload directory exists.</p>\n";
} else {
    echo "<p style='color: red;'>Upload directory does not exist.</p>\n";
}

// Test 2: Check if directory is writable
echo "<h3>Test 2: Directory Writable Check</h3>\n";
if (is_writable($upload_dir)) {
    echo "<p style='color: green;'>Upload directory is writable.</p>\n";
} else {
    echo "<p style='color: red;'>Upload directory is not writable.</p>\n";
}

// Test 3: Check UPLOAD_PATH constant
echo "<h3>Test 3: UPLOAD_PATH Constant</h3>\n";
echo "<p>UPLOAD_PATH constant: " . UPLOAD_PATH . "</p>\n";
echo "<p>CREDIT_CARDS_UPLOAD_PATH constant: " . CREDIT_CARDS_UPLOAD_PATH . "</p>\n";

// Test 4: Try to create a test file
echo "<h3>Test 4: File Creation Test</h3>\n";
$test_file = $upload_dir . '/test.txt';
$test_content = "This is a test file to verify write permissions.\n";
if (file_put_contents($test_file, $test_content)) {
    echo "<p style='color: green;'>Successfully created test file.</p>\n";
    // Clean up test file
    unlink($test_file);
} else {
    echo "<p style='color: red;'>Failed to create test file.</p>\n";
}

echo "<h3>Test Complete</h3>\n";
echo "<p><a href='admin/manage_credit_cards.php'>Go to Credit Cards Management</a></p>\n";
?>