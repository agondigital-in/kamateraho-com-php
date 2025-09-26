<?php
echo "<h1>Fixing Credit Cards Directory Permissions</h1>";

$base_path = __DIR__;
$credit_cards_dir = $base_path . '/uploads/credit_cards';

echo "<p>Processing directory: uploads/credit_cards</p>";
echo "<p>Full path: $credit_cards_dir</p>";

// Check if directory exists
if (!is_dir($credit_cards_dir)) {
    echo "<p>Creating directory: uploads/credit_cards</p>";
    if (mkdir($credit_cards_dir, 0777, true)) {
        echo "<p style='color: green;'>Successfully created directory</p>";
    } else {
        echo "<p style='color: red;'>Failed to create directory</p>";
        exit;
    }
} else {
    echo "<p style='color: blue;'>Directory already exists</p>";
}

// Set very permissive permissions
echo "<p>Setting very permissive permissions (0777)</p>";
if (chmod($credit_cards_dir, 0777)) {
    echo "<p style='color: green;'>Successfully set permissions</p>";
} else {
    echo "<p style='color: orange;'>Note: Could not change permissions (this might be OK on some systems)</p>";
}

// Try system-specific commands
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    // Unix/Linux commands
    echo "<p>Running Unix/Linux commands...</p>";
    
    $commands = [
        "chmod 777 \"$credit_cards_dir\"",
        "chmod -R 777 \"$credit_cards_dir\"",
        "chown -R www-data:www-data \"$credit_cards_dir\"",
        "chown -R " . (function_exists('posix_getuid') ? posix_getuid() : get_current_user()) . " \"$credit_cards_dir\""
    ];
    
    foreach ($commands as $command) {
        echo "<p>Running: $command</p>";
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);
        
        echo "<pre>";
        foreach ($output as $line) {
            echo htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
        echo "<p>Return code: $return_var</p>";
    }
} else {
    // Windows commands
    echo "<p>Running Windows commands...</p>";
    
    $commands = [
        "icacls \"$credit_cards_dir\" /grant Everyone:(OI)(CI)F /T",
        "icacls \"$credit_cards_dir\" /grant Users:(OI)(CI)F /T",
        "icacls \"$credit_cards_dir\" /grant \"Authenticated Users\":(OI)(CI)F /T",
        "icacls \"$credit_cards_dir\" /grant \"IIS_IUSRS\":(OI)(CI)F /T"
    ];
    
    foreach ($commands as $command) {
        echo "<p>Running: $command</p>";
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);
        
        echo "<pre>";
        foreach ($output as $line) {
            echo htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
        echo "<p>Return code: $return_var</p>";
    }
}

// Check if directory is writable
echo "<h3>Testing Permissions</h3>";
if (is_writable($credit_cards_dir)) {
    echo "<p style='color: green; font-weight: bold;'>Directory is writable!</p>";
    
    // Create a test file
    $test_file = $credit_cards_dir . '/permission_test.txt';
    $test_content = "Permission test successful!\nTime: " . date('Y-m-d H:i:s');
    
    if (file_put_contents($test_file, $test_content)) {
        echo "<p style='color: green;'>Successfully created test file</p>";
        
        // Read it back to verify
        if (file_get_contents($test_file) === $test_content) {
            echo "<p style='color: green;'>Successfully read test file</p>";
        } else {
            echo "<p style='color: red;'>Failed to read test file</p>";
        }
        
        // Delete test file
        if (unlink($test_file)) {
            echo "<p style='color: green;'>Successfully deleted test file</p>";
        } else {
            echo "<p style='color: orange;'>Could not delete test file (not critical)</p>";
        }
    } else {
        echo "<p style='color: red;'>Failed to create test file</p>";
    }
} else {
    echo "<p style='color: red; font-weight: bold;'>Directory is still NOT writable!</p>";
    
    // Show detailed information
    echo "<h3>Debug Information</h3>";
    echo "<p>Real path: " . realpath($credit_cards_dir) . "</p>";
    echo "<p>Permissions: " . substr(sprintf('%o', fileperms($credit_cards_dir)), -4) . "</p>";
    echo "<p>Web server user: " . get_current_user() . "</p>";
    
    if (function_exists('posix_getpwuid') && function_exists('fileowner')) {
        $owner = posix_getpwuid(fileowner($credit_cards_dir));
        echo "<p>Directory owner: " . ($owner ? $owner['name'] : 'Unknown') . "</p>";
    }
}

echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>If this didn't work, try running this script as Administrator/root</li>";
echo "<li>In Docker environments, you may need to adjust the container's user permissions</li>";
echo "<li>Check if SELinux or AppArmor is blocking access (Linux systems)</li>";
echo "<li>Ensure your web server process has write access to this directory</li>";
echo "</ol>";

echo "<p><a href='admin/manage_credit_cards.php'>Try uploading again</a></p>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>