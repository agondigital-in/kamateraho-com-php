<?php
// Test script to verify blog post creation and redirect

// Simulate the blog post creation process
function testBlogPostCreation() {
    // Get the highest post number to determine the next post filename
    $blog_dir = '../kamateraho/blog/';
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
    
    $next_number = $max_number + 1;
    echo "Next post number would be: post" . $next_number . ".php\n";
    
    // Test redirect
    echo "Testing redirect to blog index...\n";
    header("Location: ../kamateraho/blog/index.php");
    exit();
}

// Run the test
testBlogPostCreation();
?>