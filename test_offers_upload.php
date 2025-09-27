<?php
echo "<h1>Testing Offers Upload Directory Permissions</h1>";

$upload_dir = 'uploads/offers/';
$full_path = __DIR__ . '/' . $upload_dir;

echo "<h3>Directory Information</h3>";
echo "<p>Upload directory: $upload_dir</p>";
echo "<p>Full path: $full_path</p>";

// Check if directory exists
if (is_dir($full_path)) {
    echo "<p style='color: green;'>✓ Directory exists</p>";
} else {
    echo "<p style='color: red;'>✗ Directory does not exist</p>";
    echo "<p>Attempting to create directory...</p>";
    if (mkdir($full_path, 0777, true)) {
        echo "<p style='color: green;'>✓ Successfully created directory</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create directory</p>";
    }
}

// Check if directory is writable
if (is_writable($full_path)) {
    echo "<p style='color: green;'>✓ Directory is writable</p>";
} else {
    echo "<p style='color: red;'>✗ Directory is NOT writable</p>";
}

// Test file creation
$test_file = $full_path . 'test_' . time() . '.txt';
$test_content = "Test file created on: " . date('Y-m-d H:i:s');

echo "<h3>File Creation Test</h3>";
if (file_put_contents($test_file, $test_content)) {
    echo "<p style='color: green;'>✓ Successfully created test file</p>";
    
    // Clean up test file
    if (unlink($test_file)) {
        echo "<p style='color: green;'>✓ Successfully cleaned up test file</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Could not clean up test file (not critical)</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Failed to create test file</p>";
}

// Show directory permissions
if (is_dir($full_path)) {
    $perms = fileperms($full_path);
    echo "<h3>Directory Permissions</h3>";
    echo "<p>Permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
}

echo "<h3>Next Steps</h3>";
echo "<p>If the tests above show any failures, you can:</p>";
echo "<ol>";
echo "<li><a href='fix_upload_permissions.php'>Run the automatic permission fix script</a></li>";
echo "<li><a href='admin/upload_offer.php'>Try uploading an offer</a></li>";
echo "</ol>";
?>
