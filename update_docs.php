<?php
echo "<h1>Updating Documentation Files</h1>";

// Get the base URL from environment
$base_url = rtrim($_ENV['APP_URL'] ?? 'https://kamateraho1.agondev.space', '/');

echo "<p>Base URL: $base_url</p>";

// Files to update
$files = [
    'INSTALL.txt',
    'authentication_flow_summary.md'
];

foreach ($files as $file) {
    $file_path = __DIR__ . '/' . $file;
    
    if (file_exists($file_path)) {
        echo "<h3>Updating $file</h3>";
        
        $content = file_get_contents($file_path);
        
        // Replace localhost URLs with the actual domain
        $content = preg_replace('/http:\/\/localhost\/kmt/', $base_url, $content);
        $content = preg_replace('/http:\/\/localhost/', $base_url, $content);
        
        // Save the updated content
        if (file_put_contents($file_path, $content)) {
            echo "<p style='color: green;'>Successfully updated $file</p>";
        } else {
            echo "<p style='color: red;'>Failed to update $file</p>";
        }
    } else {
        echo "<p>File not found: $file</p>";
    }
}

echo "<h3>Update Complete</h3>";
echo "<p>All documentation files have been updated with the correct base URL.</p>";
echo "<p><a href='index.php'>Go to Homepage</a></p>";
?>