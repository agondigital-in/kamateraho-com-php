<?php
/**
 * Environment variable loader
 * Loads variables from .env file into $_ENV and $_SERVER superglobals
 */

function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments and empty lines
        if (strpos($line, '#') === 0 || empty(trim($line))) {
            continue;
        }
        
        // Parse key=value pairs
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Remove quotes if present
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        // Set in environment
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
        putenv("$key=$value");
    }
}

// Load the environment file
loadEnv(__DIR__ . '/../.env');

// Generate app key if not set
if (empty($_ENV['APP_KEY']) && php_sapi_name() !== 'cli') {
    $_ENV['APP_KEY'] = bin2hex(random_bytes(32));
    $_SERVER['APP_KEY'] = $_ENV['APP_KEY'];
    putenv("APP_KEY={$_ENV['APP_KEY']}");
}