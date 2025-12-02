<?php
// Run this file once to create the blog_posts table
require_once '../config/db.php';

try {
    $sql = file_get_contents(__DIR__ . '/create_blog_table.sql');
    $pdo->exec($sql);
    echo "Blog table created successfully!";
} catch (PDOException $e) {
    echo "Error creating blog table: " . $e->getMessage();
}
?>
