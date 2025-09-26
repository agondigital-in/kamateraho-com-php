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
$imagepath = $_ENV['IMG_PATH'] ?? '';

// Define upload paths
define('UPLOAD_PATH', 'uploads');
define('CREDIT_CARDS_UPLOAD_PATH', 'uploads/credit_cards');
define('OFFER_IMAGES_UPLOAD_PATH', 'uploads'); // Offers are stored in the main uploads folder

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

// Function to get full upload path
function upload_path($subfolder = '') {
    if ($subfolder) {
        return UPLOAD_PATH . '/' . ltrim($subfolder, '/');
    }
    return UPLOAD_PATH;
}

// Function to get full upload directory path with permission handling
function upload_dir($subfolder = '') {
    $base_dir = __DIR__ . '/../' . UPLOAD_PATH;
    if ($subfolder) {
        $full_path = $base_dir . '/' . ltrim($subfolder, '/');
    } else {
        $full_path = $base_dir;
    }
    
    // Ensure directory exists with proper permissions
    if (!is_dir($full_path)) {
        // Try to create directory with more permissive permissions for container environments
        @mkdir($full_path, 0775, true);
    }
    
    // Try to ensure directory is writable
    if (is_dir($full_path) && !is_writable($full_path)) {
        @chmod($full_path, 0775);
    }
    
    return $full_path;
}
?>