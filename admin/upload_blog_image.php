<?php
// Upload blog image to Cloudinary
session_start();
require_once '../config/env.php';
header('Content-Type: application/json');

// Check if file was uploaded
if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['image'];

// Validate file
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP allowed.']);
    exit;
}

// Max file size: 5MB
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
    exit;
}

// Cloudinary credentials (add these to your .env file)
$cloud_name = $_ENV['CLOUDINARY_CLOUD_NAME'] ?? '';
$api_key = $_ENV['CLOUDINARY_API_KEY'] ?? '';
$api_secret = $_ENV['CLOUDINARY_API_SECRET'] ?? '';

if (empty($cloud_name) || empty($api_key) || empty($api_secret)) {
    echo json_encode(['success' => false, 'message' => 'Cloudinary not configured. Add credentials to .env file.']);
    exit;
}

// Upload to Cloudinary
$upload_url = "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload";

$timestamp = time();
$folder = 'blog_images';

// Generate signature (parameters must be in alphabetical order)
$params_to_sign = [
    'folder' => $folder,
    'timestamp' => $timestamp
];
ksort($params_to_sign);
$signature_string = '';
foreach ($params_to_sign as $key => $value) {
    $signature_string .= "{$key}={$value}&";
}
$signature_string = rtrim($signature_string, '&');
$signature = sha1($signature_string . $api_secret);

$post_fields = [
    'file' => new CURLFile($file['tmp_name'], $file['type'], $file['name']),
    'api_key' => $api_key,
    'timestamp' => $timestamp,
    'signature' => $signature,
    'folder' => $folder
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $upload_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    $result = json_decode($response, true);
    echo json_encode([
        'success' => true,
        'url' => $result['secure_url'],
        'public_id' => $result['public_id']
    ]);
} else {
    $error_details = json_decode($response, true);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to upload to Cloudinary',
        'http_code' => $http_code,
        'error' => $error_details['error']['message'] ?? 'Unknown error',
        'details' => $response
    ]);
}
?>
