<?php
/**
 * Script to fix upload directory permissions for credit cards
 * This ensures the upload directory works in both local and Coolify environments
 */

include 'config/app.php';

// Get the upload directory for credit cards
$upload_dir = upload_dir('credit_cards');

echo "Checking upload directory: $upload_dir\n";

// Create directory if it doesn't exist
if (!is_dir($upload_dir)) {
    echo "Directory does not exist. Creating...\n";
    if (mkdir($upload_dir, 0755, true)) {
        echo "Directory created successfully.\n";
    } else {
        echo "Failed to create directory.\n";
        exit(1);
    }
} else {
    echo "Directory already exists.\n";
}

// Set proper permissions
echo "Setting permissions to 0755...\n";
if (chmod($upload_dir, 0755)) {
    echo "Permissions set successfully.\n";
} else {
    echo "Failed to set permissions.\n";
}

// Check if directory is writable
echo "Checking if directory is writable...\n";
if (is_writable($upload_dir)) {
    echo "Directory is writable.\n";
} else {
    echo "Directory is not writable. Please check permissions manually.\n";
}

echo "Done!\n";
?>