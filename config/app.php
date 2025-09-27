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
define('OFFER_IMAGES_UPLOAD_PATH', 'uploads/offers'); // Offers are stored in the offers subfolder

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

// Function to get full upload directory path with enhanced permission handling for containerized environments
function upload_dir($subfolder = '') {
    // Optional environment override (absolute path). Useful in containers/hosting.
    $env_base = $_ENV['UPLOAD_BASE_DIR'] ?? $_SERVER['UPLOAD_BASE_DIR'] ?? '';

    // Candidate base directories to try (in order)
    $possible_bases = [];
    if (!empty($env_base)) {
        $possible_bases[] = rtrim($env_base, '/\\') . '/' . UPLOAD_PATH;
    }
    $possible_bases[] = __DIR__ . '/../' . UPLOAD_PATH;     // Project root uploads/
    $possible_bases[] = __DIR__ . '/../../' . UPLOAD_PATH;  // If running from a nested context
    $possible_bases[] = getcwd() . '/' . UPLOAD_PATH;       // Current working directory

    // Avoid hard-coding non-writable container paths by default; only use /app if explicitly writable
    $possible_bases[] = '/app/' . UPLOAD_PATH;              // Container path (try only if writable)

    $base_dir = null;
    foreach ($possible_bases as $base) {
        // Try to create the base if it doesn't exist
        if (!is_dir($base)) {
            @mkdir($base, 0775, true);
        }
        // Accept only if directory exists and is writable
        if (is_dir($base) && is_writable($base)) {
            $base_dir = $base;
            break;
        }
    }

    // Final fallback: system temp directory
    if ($base_dir === null) {
        $base_dir = rtrim(sys_get_temp_dir(), '/\\') . '/' . UPLOAD_PATH;
        if (!is_dir($base_dir)) {
            @mkdir($base_dir, 0775, true);
        }
    }

    // Append subfolder if provided
    $full_path = $base_dir;
    if ($subfolder) {
        $full_path .= '/' . ltrim($subfolder, '/');
    }

    // Ensure final directory exists and is writable
    if (!is_dir($full_path)) {
        @mkdir($full_path, 0775, true);
    }
    if (is_dir($full_path) && !is_writable($full_path)) {
        @chmod($full_path, 0775);
    }

    return $full_path;
}
?>