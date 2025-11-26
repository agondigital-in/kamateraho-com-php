<?php
// Demo script to show automatic blog post increment

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

// Demo
$blog_dir = '../kamateraho/blog/';
$next_number = getNextPostNumber($blog_dir);

echo "<h2>Automatic Blog Post Numbering Demo</h2>";
echo "<p>Current highest blog post number: " . ($next_number - 1) . "</p>";
echo "<p>Next blog post will be automatically numbered as: <strong>post" . $next_number . ".php</strong></p>";

// Show how this would work in practice
$title = "How to Earn ₹1000 Daily with KamateRaho";
$date = "Nov 27, 2025";
$author = "Admin";
$excerpt = "Learn the secrets to earning ₹1000 every day through our platform";
$image_url = "https://example.com/blog-image.jpg";

echo "<h3>Example Blog Post Creation</h3>";
echo "<p>Title: " . $title . "</p>";
echo "<p>Will be saved as: <strong>post" . $next_number . ".php</strong></p>";
echo "<p>Will automatically appear in the blog index with the correct numbering</p>";

// Show the HTML that would be added to the index
echo "<h3>HTML that would be added to blog index:</h3>";
echo "<pre>";
echo "&lt;!-- Blog Post " . $next_number . " --&gt;\n";
echo "&lt;div class=\"blog-card\"&gt;\n";
echo "    &lt;img src=\"" . $image_url . "\" alt=\"" . $title . "\" class=\"blog-image\"&gt;\n";
echo "    &lt;div class=\"blog-content\"&gt;\n";
echo "        &lt;h3&gt;" . $title . "&lt;/h3&gt;\n";
echo "        &lt;div class=\"blog-meta\"&gt;\n";
echo "            &lt;span&gt;&lt;i class=\"far fa-calendar\"&gt;&lt;/i&gt; " . $date . "&lt;/span&gt;\n";
echo "            &lt;span&gt;&lt;i class=\"far fa-user\"&gt;&lt;/i&gt; " . $author . "&lt;/span&gt;\n";
echo "        &lt;/div&gt;\n";
echo "        &lt;p class=\"blog-excerpt\"&gt;" . $excerpt . "&lt;/p&gt;\n";
echo "        &lt;a href=\"post" . $next_number . ".php\" class=\"read-more\"&gt;Read More&lt;/a&gt;\n";
echo "    &lt;/div&gt;\n";
echo "&lt;/div&gt;";
echo "</pre>";

echo "<p>This demonstrates how the system automatically increments blog post numbers without manual intervention.</p>";
?>