<?php
echo "<h1>Fixing Upload Directory Permissions</h1>";

// Define directories that need write permissions
$directories = [
    'uploads',
    'uploads/credit_cards'
];

$base_path = __DIR__;

foreach ($directories as $dir) {
    $full_path = $base_path . '/' . $dir;
    
    echo "<h3>Processing directory: $dir</h3>";
    echo "<p>Full path: $full_path</p>";
    
    // Check if directory exists
    if (!is_dir($full_path)) {
        echo "<p>Creating directory: $dir</p>";
        if (mkdir($full_path, 0777, true)) { // Use 0777 for maximum permissions
            echo "<p style='color: green;'>Successfully created directory: $dir</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: $dir</p>";
            continue;
        }
    } else {
        echo "<p style='color: blue;'>Directory already exists: $dir</p>";
    }
    
    // Try to set permissions with more permissive settings
    echo "<p>Setting permissions for: $dir (0777)</p>";
    if (chmod($full_path, 0777)) {
        echo "<p style='color: green;'>Successfully set permissions for: $dir</p>";
    } else {
        echo "<p style='color: orange;'>Note: Could not change permissions for: $dir (this might be OK on Windows)</p>";
    }
    
    // Change ownership to current user if possible (Unix/Linux only)
    if (function_exists('posix_getuid')) {
        $user = posix_getuid();
        $group = posix_getgid();
        echo "<p>Current user ID: $user, group ID: $group</p>";
        if (chown($full_path, $user) && chgrp($full_path, $group)) {
            echo "<p style='color: green;'>Successfully changed ownership for: $dir</p>";
        } else {
            echo "<p style='color: orange;'>Note: Could not change ownership for: $dir</p>";
        }
    }
    
    // Check if directory is writable
    if (is_writable($full_path)) {
        echo "<p style='color: green;'>Directory is writable: $dir</p>";
    } else {
        echo "<p style='color: red;'>Directory is NOT writable: $dir</p>";
        
        // Try to make it writable using different approaches
        if (is_dir($full_path)) {
            // On Unix/Linux systems, try to use chmod with different flags
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                echo "<p>Trying Unix/Linux specific permission fixes...</p>";
                
                // Try to grant permissions using shell commands
                $commands = [
                    "chmod 777 \"$full_path\"",
                    "chmod -R 777 \"$full_path\"",
                    "chown -R " . (function_exists('posix_getuid') ? posix_getuid() : 'www-data') . " \"$full_path\""
                ];
                
                foreach ($commands as $command) {
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
                }
                
                // Check again if directory is writable
                if (is_writable($full_path)) {
                    echo "<p style='color: green;'>Directory is now writable: $dir</p>";
                }
            } else {
                // On Windows, we need to use icacls to grant permissions
                echo "<p>Trying to grant write permissions using icacls...</p>";
                
                // Try to grant permissions to various user groups
                $commands = [
                    "icacls \"$full_path\" /grant Everyone:(OI)(CI)F",
                    "icacls \"$full_path\" /grant Users:(OI)(CI)F",
                    "icacls \"$full_path\" /grant \"Authenticated Users\":(OI)(CI)F"
                ];
                
                foreach ($commands as $command) {
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
                }
                
                // Check again if directory is writable
                if (is_writable($full_path)) {
                    echo "<p style='color: green;'>Directory is now writable: $dir</p>";
                }
            }
        }
    }
    
    // Show directory permissions and ownership
    if (is_dir($full_path)) {
        $perms = fileperms($full_path);
        echo "<p>Directory permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
        
        if (function_exists('posix_getpwuid') && function_exists('fileowner')) {
            $owner = posix_getpwuid(fileowner($full_path));
            echo "<p>Directory owner: " . ($owner ? $owner['name'] : 'Unknown') . "</p>";
        }
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
    
    // Also test the credit_cards directory
    $test_file2 = $base_path . '/uploads/credit_cards/test_permissions.txt';
    if (file_put_contents($test_file2, $test_content)) {
        echo "<p style='color: green;'>Successfully created test file in credit_cards directory</p>";
        // Clean up test file
        if (unlink($test_file2)) {
            echo "<p style='color: green;'>Successfully cleaned up test file from credit_cards directory</p>";
        } else {
            echo "<p style='color: orange;'>Could not clean up test file from credit_cards directory (not critical)</p>";
        }
    } else {
        echo "<p style='color: red;'>Failed to create test file in credit_cards directory</p>";
    }
} else {
    echo "<p style='color: red;'>Failed to create test file. This confirms the permission issue.</p>";
}

echo "<h3>Recommendations</h3>";
echo "<ol>";
echo "<li>If the above didn't fix the issue, try running this script as Administrator/root</li>";
echo "<li>Check if your web server (Apache/Nginx) has write permissions to these directories</li>";
echo "<li>In Docker environments, you might need to adjust the container's user permissions</li>";
echo "<li>You might need to manually grant 'Full Control' to the web server user for the uploads directories</li>";
echo "<li>Check if SELinux or AppArmor is blocking access (Linux systems)</li>";
echo "</ol>";

echo "<p><a href='admin/manage_credit_cards.php'>Try uploading credit cards again</a></p>";
echo "<p><a href='admin/upload_offer.php'>Try uploading offers again</a></p>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>