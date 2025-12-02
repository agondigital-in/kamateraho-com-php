<?php
// Fix blog_posts table - remove published_date column or add default value
require_once 'config/db.php';

try {
    // Check if published_date column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM blog_posts LIKE 'published_date'");
    $column_exists = $stmt->rowCount() > 0;
    
    if ($column_exists) {
        echo "Found 'published_date' column. Removing it...<br>";
        $pdo->exec("ALTER TABLE blog_posts DROP COLUMN published_date");
        echo "✓ Column removed successfully!<br>";
    } else {
        echo "✓ No 'published_date' column found. Table is correct.<br>";
    }
    
    // Show current table structure
    echo "<h3>Current Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE blog_posts");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "<td>{$column['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green;'><strong>✓ Blog table fixed successfully!</strong></p>";
    echo "<p>You can now <a href='admin/manage_blog.php'>create blog posts</a>.</p>";
    echo "<p><strong>Note:</strong> Delete this file (fix_blog_table.php) after running.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
