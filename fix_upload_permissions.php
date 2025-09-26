<?php
echo "<h1>Fixing Upload Directory Permissions</h1>";

// Define directories that need write permissions
$directories = [
    'uploads'
];

$base_path = __DIR__;

foreach ($directories as $dir) {
    $full_path = $base_path . '/' . $dir;
    
    echo "<h3>Processing directory: $dir</h3>";
    echo "<p>Full path: $full_path</p>";
    
    // Check if directory exists
    if (!is_dir($full_path)) {
        echo "<p>Creating directory: $dir</p>";
        if (mkdir($full_path, 0755, true)) {
            echo "<p style='color: green;'>Successfully created directory: $dir</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: $dir</p>";
            continue;
        }
    } else {
        echo "<p style='color: blue;'>Directory already exists: $dir</p>";
    }
    
    // Try to set permissions
    echo "<p>Setting permissions for: $dir (0755)</p>";
    if (chmod($full_path, 0755)) {
        echo "<p style='color: green;'>Successfully set permissions for: $dir</p>";
    } else {
        echo "<p style='color: orange;'>Note: Could not change permissions for: $dir (this might be OK on Windows)</p>";
    }
    
    // Check if directory is writable
    if (is_writable($full_path)) {
        echo "<p style='color: green;'>Directory is writable: $dir</p>";
    } else {
        echo "<p style='color: red;'>Directory is NOT writable: $dir</p>";
        // Try to make it writable
        if (is_dir($full_path)) {
            // On Windows, we need to use icacls to grant permissions
            echo "<p>Trying to grant write permissions using icacls...</p>";
            
            // Get the current user
            $user = get_current_user();
            echo "<p>Current user: $user</p>";
            
            // Try to grant permissions (this might not work in all environments)
            $command = "icacls \"$full_path\" /grant Everyone:(OI)(CI)F";
            echo "<p>Running command: $command</p>";
            
            $output = [];
            $return_var = 0;
            exec($command, $output, $return_var);
            
            echo "<p>Command output:</p><pre>";
            foreach ($output as $line) {
                echo htmlspecialchars($line) . "\n";
            }
            echo "</pre>";
            echo "<p>Return code: $return_var</p>";
            
            // Check again if directory is writable
            if (is_writable($full_path)) {
                echo "<p style='color: green;'>Directory is now writable: $dir</p>";
            } else {
                echo "<p style='color: red;'>Directory is still NOT writable: $dir</p>";
            }
        }
    }
    
    // Show directory permissions
    if (is_dir($full_path)) {
        $perms = fileperms($full_path);
        echo "<p>Directory permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
    }
    
    echo "<hr>";
}

// Create a test file to verify writing works
echo "<h3>Testing file creation</h3>";
$test_file = $base_path . '/uploads/test_permissions.txt';
$test_content = "This is a test file to verify write permissions.\nCreated on: " . date('Y-m-d H:i:s');

if (file_put_contents($test_file, $test_content)) {
    echo "<p style='color: green;'>Successfully created test file: test_permissions.txt</p>";
    // Clean up test file
    if (unlink($test_file)) {
        echo "<p style='color: green;'>Successfully cleaned up test file</p>";
    } else {
        echo "<p style='color: orange;'>Could not clean up test file (not critical)</p>";
    }
} else {
    echo "<p style='color: red;'>Failed to create test file. This confirms the permission issue.</p>";
}

echo "<h3>Recommendations</h3>";
echo "<ol>";
echo "<li>If the above didn't fix the issue, try running this script as Administrator</li>";
echo "<li>Check if your web server (Apache) has write permissions to these directories</li>";
echo "<li>In XAMPP, the Apache service typically runs under the 'SYSTEM' account</li>";
echo "<li>You might need to manually grant 'Full Control' to the 'SYSTEM' account for the uploads directories</li>";
echo "</ol>";

echo "<p><a href='admin/upload_offer.php'>Try uploading again</a></p>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>