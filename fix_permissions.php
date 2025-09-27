<?php
echo "<h1>Fixing Directory Permissions</h1>";

// Define directories that need write permissions
$directories = [
    'uploads',
    'uploads/credit_cards',
    'uploads/offers'
];

$base_path = __DIR__;

foreach ($directories as $dir) {
    $full_path = $base_path . DIRECTORY_SEPARATOR . $dir;
    
    if (!is_dir($full_path)) {
        echo "<p>Creating directory: $dir</p>";
        if (mkdir($full_path, 0755, true)) {
            echo "<p style='color: green;'>Successfully created directory: $dir</p>";
        } else {
            echo "<p style='color: red;'>Failed to create directory: $dir</p>";
        }
    }
    
    // Try to set permissions
    echo "<p>Setting permissions for: $dir</p>";
    if (chmod($full_path, 0755)) {
        echo "<p style='color: green;'>Successfully set permissions for: $dir</p>";
    } else {
        echo "<p style='color: red;'>Failed to set permissions for: $dir</p>";
    }
    
    // Check if directory is writable
    if (is_writable($full_path)) {
        echo "<p style='color: green;'>Directory is writable: $dir</p>";
    } else {
        echo "<p style='color: red;'>Directory is NOT writable: $dir</p>";
    }
}

echo "<p><a href='admin/manage_credit_cards.php'>Try uploading again</a></p>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>