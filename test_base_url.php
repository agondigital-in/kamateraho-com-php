<?php
include 'config/app.php';

echo "<h1>Base URL Configuration Test</h1>";

echo "<p>Environment APP_URL: " . ($_ENV['APP_URL'] ?? 'Not set') . "</p>";
echo "<p>Defined BASE_URL: " . BASE_URL . "</p>";
echo "<p>Generated URL for 'index.php': " . url('index.php') . "</p>";
echo "<p>Generated URL for 'admin/login.php': " . url('admin/login.php') . "</p>";
echo "<p>Generated CSS URL: " . css('style.css') . "</p>";

echo "<h3>Configuration Status</h3>";
if (defined('BASE_URL') && BASE_URL === 'https://kamateraho1.agondev.space') {
    echo "<p style='color: green; font-weight: bold;'>Base URL is correctly configured!</p>";
} else {
    echo "<p style='color: orange;'>Base URL is set to: " . (defined('BASE_URL') ? BASE_URL : 'Not defined') . "</p>";
}

echo "<p><a href='index.php'>Go to Homepage</a></p>";
?>