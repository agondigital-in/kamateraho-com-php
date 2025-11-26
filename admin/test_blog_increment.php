<?php
// Test script to verify blog post increment functionality

// Function to get the next post number
function getNextPostNumber($blog_dir) {
    $files = glob($blog_dir . 'post*.php');
    $max_number = 0;
    
    foreach ($files as $file) {
        if (preg_match('/post(\d+)\.php/', basename($file), $matches)) {
            $number = (int)$matches[1];
            if ($number > $max_number) {
                $max_number = $number;
            }
        }
    }
    
    return $max_number + 1;
}

// Test the function
$blog_dir = '../kamateraho/blog/';
$next_number = getNextPostNumber($blog_dir);

echo "Next blog post number should be: " . $next_number . "\n";

// List existing blog post files
echo "Existing blog post files:\n";
$files = glob($blog_dir . 'post*.php');
foreach ($files as $file) {
    if (preg_match('/post(\d+)\.php/', basename($file), $matches)) {
        echo "  " . basename($file) . " (number: " . $matches[1] . ")\n";
    }
}
?>