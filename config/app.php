<?php
/**
 * Application configuration
 * This file provides application-wide configuration settings
 */

// Load environment variables if not already loaded
if (!isset($_ENV['APP_URL'])) {
    require_once __DIR__ . '/env.php';
}

// Define base URL from environment or default to localhost
define('BASE_URL', rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'));

// Function to generate full URLs
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

// Function to generate asset URLs
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

// Function to generate CSS URLs
function css($path) {
    return url('css/' . ltrim($path, '/'));
}

// Function to generate JS URLs
function js($path) {
    return url('js/' . ltrim($path, '/'));
}

// Function to generate image URLs
function image($path) {
    return url('images/' . ltrim($path, '/'));
}
?>