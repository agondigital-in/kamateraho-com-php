<?php
/**
 * Fix permissions for credit cards upload directory
 * This script ensures proper permissions for file uploads in both local and Coolify environments
 */

// Include app configuration
include 'config/app.php';

// Get the base path (parent directory of this script)
$base_path = __DIR__;
$credit_cards_dir = $base_path . '/uploads/credit_cards';

echo "<h2>Fixing Credit Cards Upload Permissions</h2>\n";
echo "<p>Processing directory: uploads/credit_cards</p>\n";
echo "<p>Full path: $credit_cards_dir</p>\n";

// Create directory if it doesn't exist
if (!is_dir($credit_cards_dir)) {
    echo "<p>Creating directory: uploads/credit_cards</p>\n";
    if (mkdir($credit_cards_dir, 0755, true)) {
        echo "<p style='color: green;'>Directory created successfully.</p>\n";
    } else {
        echo "<p style='color: red;'>Failed to create directory.</p>\n";
        exit(1);
    }
} else {
    echo "<p>Directory already exists.</p>\n";
}

// Set proper permissions
echo "<p>Setting permissions to 0755...</p>\n";
if (chmod($credit_cards_dir, 0755)) {
    echo "<p style='color: green;'>Permissions set successfully.</p>\n";
} else {
    echo "<p style='color: orange;'>Failed to set permissions with chmod (this is normal on Windows).</p>\n";
}

// Try to set permissions with different methods based on OS
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    echo "<p>Windows detected. Trying Windows-specific permission fixes...</p>\n";
    // On Windows, we'll try to use icacls command
    $commands = [
        "icacls \"$credit_cards_dir\" /grant Users:(OI)(CI)F",
        "icacls \"$credit_cards_dir\" /grant Everyone:(OI)(CI)F"
    ];
    
    foreach ($commands as $command) {
        echo "<p>Running: $command</p>\n";
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);
        if ($return_var === 0) {
            echo "<p style='color: green;'>Command executed successfully.</p>\n";
        } else {
            echo "<p style='color: orange;'>Command failed with return code: $return_var</p>\n";
        }
        foreach ($output as $line) {
            echo "<p>$line</p>\n";
        }
    }
} else {
    echo "<p>Unix/Linux detected. Trying additional permission fixes...</p>\n";
    // On Unix/Linux systems, try to set permissions recursively
    $commands = [
        "chmod -R 755 \"$credit_cards_dir\"",
        "chown -R www-data:www-data \"$credit_cards_dir\" 2>/dev/null || echo 'chown failed (might not be root)'"
    ];
    
    foreach ($commands as $command) {
        echo "<p>Running: $command</p>\n";
        $output = [];
        $return_var = 0;
        exec($command, $output, $return_var);
        if ($return_var === 0) {
            echo "<p style='color: green;'>Command executed successfully.</p>\n";
        } else {
            echo "<p style='color: orange;'>Command failed with return code: $return_var</p>\n";
        }
        foreach ($output as $line) {
            echo "<p>$line</p>\n";
        }
    }
}

// Check if directory is writable
echo "<p>Checking if directory is writable...</p>\n";
if (is_writable($credit_cards_dir)) {
    echo "<p style='color: green;'>Directory is writable. Uploads should work correctly.</p>\n";
} else {
    echo "<p style='color: red;'>Directory is not writable. Please check permissions manually.</p>\n";
    echo "<p>Try running this script as administrator (Windows) or with sudo (Linux).</p>\n";
}

echo "<h3>Done!</h3>\n";
echo "<p><a href='admin/manage_credit_cards.php'>Go to Credit Cards Management</a></p>\n";
?>