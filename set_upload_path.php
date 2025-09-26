<?php
echo "<h1>Set Custom Upload Path</h1>";

// Check if a new path was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_path'])) {
    $new_path = rtrim($_POST['upload_path'], '/');
    
    // Validate the path
    if (!empty($new_path)) {
        // Update the app.php file with the new path
        $app_file = __DIR__ . '/config/app.php';
        $content = file_get_contents($app_file);
        
        // Replace the upload path definitions
        $content = preg_replace(
            '/define\(\'UPLOAD_PATH\', \'[^\']*\');/',
            "define('UPLOAD_PATH', '$new_path');",
            $content
        );
        
        // Save the updated content
        if (file_put_contents($app_file, $content)) {
            echo "<p style='color: green;'>Upload path successfully updated to: $new_path</p>";
        } else {
            echo "<p style='color: red;'>Failed to update upload path</p>";
        }
    } else {
        echo "<p style='color: red;'>Please provide a valid upload path</p>";
    }
}

// Get current upload path
include 'config/app.php';
$current_path = defined('UPLOAD_PATH') ? UPLOAD_PATH : 'uploads';

echo "<p>Current upload path: <strong>$current_path</strong></p>";

echo "<form method='POST'>";
echo "<div class='mb-3'>";
echo "<label for='upload_path' class='form-label'>New Upload Path:</label>";
echo "<input type='text' class='form-control' id='upload_path' name='upload_path' value='$current_path' placeholder='e.g., uploads or custom/path'>";
echo "<div class='form-text'>Enter the relative path from the project root where uploaded files should be stored.</div>";
echo "</div>";
echo "<button type='submit' class='btn btn-primary'>Update Upload Path</button>";
echo "</form>";

echo "<h3>Important Notes:</h3>";
echo "<ul>";
echo "<li>The path should be relative to the project root directory</li>";
echo "<li>Make sure the web server has write permissions to this directory</li>";
echo "<li>If the directory doesn't exist, it will be created automatically</li>";
echo "<li>After changing the path, you may need to update existing file references</li>";
echo "</ul>";

echo "<p><a href='index.php'>Back to Homepage</a></p>";
?>