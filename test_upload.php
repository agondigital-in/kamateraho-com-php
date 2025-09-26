<?php
echo "<h1>Upload Directory Test</h1>";

$upload_dir = 'uploads/credit_cards/';
$full_path = __DIR__ . '/' . $upload_dir;

echo "<p>Checking directory: $upload_dir</p>";
echo "<p>Full path: $full_path</p>";

// Check if directory exists
if (is_dir($full_path)) {
    echo "<p style='color: green;'>Directory exists</p>";
} else {
    echo "<p style='color: red;'>Directory does not exist</p>";
    // Try to create it
    if (mkdir($full_path, 0755, true)) {
        echo "<p style='color: green;'>Successfully created directory</p>";
    } else {
        echo "<p style='color: red;'>Failed to create directory</p>";
    }
}

// Check if directory is writable
if (is_writable($full_path)) {
    echo "<p style='color: green;'>Directory is writable</p>";
} else {
    echo "<p style='color: red;'>Directory is NOT writable</p>";
    // Try to change permissions
    if (chmod($full_path, 0755)) {
        echo "<p style='color: green;'>Successfully changed permissions</p>";
    } else {
        echo "<p style='color: red;'>Failed to change permissions</p>";
    }
}

// Show directory permissions
if (is_dir($full_path)) {
    $perms = fileperms($full_path);
    echo "<p>Directory permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
}

// List files in directory
if (is_dir($full_path)) {
    $files = scandir($full_path);
    echo "<p>Files in directory:</p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

echo "<p><a href='admin/manage_credit_cards.php'>Try uploading again</a></p>";
echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>