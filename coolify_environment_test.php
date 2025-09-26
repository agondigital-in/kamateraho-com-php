<?php
/**
 * Coolify Environment Test
 * Specifically designed to diagnose issues in Coolify containerized environments
 */

echo "<h1>Coolify Environment Test</h1>\n";
echo "<p>Testing environment-specific configuration...</p>\n";

// Check if we're in a containerized environment
echo "<h2>Environment Detection</h2>\n";

$isContainer = false;
$containerType = 'unknown';

// Check for Docker
if (file_exists('/.dockerenv')) {
    $isContainer = true;
    $containerType = 'Docker';
    echo "<p style='color: green;'>✓ Docker container detected</p>\n";
}

// Check for common container indicators
if (file_exists('/proc/1/cgroup')) {
    $cgroup = file_get_contents('/proc/1/cgroup');
    if (strpos($cgroup, 'docker') !== false) {
        $isContainer = true;
        $containerType = 'Docker (cgroup)';
        echo "<p style='color: green;'>✓ Docker container detected via cgroup</p>\n";
    } elseif (strpos($cgroup, 'lxc') !== false) {
        $isContainer = true;
        $containerType = 'LXC';
        echo "<p style='color: green;'>✓ LXC container detected</p>\n";
    }
}

// Check for Coolify-specific indicators
if (getenv('COOLIFY_APP_NAME') || getenv('COOLIFY_DEPLOYMENT_UUID')) {
    $isContainer = true;
    $containerType = 'Coolify';
    echo "<p style='color: green;'>✓ Coolify environment detected</p>\n";
}

if (!$isContainer) {
    echo "<p>No container environment detected</p>\n";
}

echo "<p>Container type: $containerType</p>\n";

// Test application paths
echo "<h2>Application Path Testing</h2>\n";

$currentDir = __DIR__;
echo "<p>Current directory: $currentDir</p>\n";

$possibleUploadPaths = [
    $currentDir . '/uploads',
    $currentDir . '/../uploads',
    '/app/uploads',
    '/var/www/html/uploads'
];

foreach ($possibleUploadPaths as $path) {
    echo "<h3>Testing path: $path</h3>\n";
    
    if (is_dir($path)) {
        echo "<p style='color: green;'>✓ Directory exists</p>\n";
        
        if (is_writable($path)) {
            echo "<p style='color: green;'>✓ Directory is writable</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Directory is not writable</p>\n";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Directory does not exist</p>\n";
        
        // Check if parent directory is writable
        $parentDir = dirname($path);
        if (is_dir($parentDir) && is_writable($parentDir)) {
            echo "<p style='color: green;'>✓ Parent directory is writable, can create directory</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Parent directory is not writable</p>\n";
        }
    }
}

// Test the upload_dir function
echo "<h2>Upload Directory Function Test</h2>\n";

// Include app configuration
include 'config/app.php';

echo "<p>Testing upload_dir() function:</p>\n";

$testDirs = ['', 'credit_cards', 'offers'];
foreach ($testDirs as $subfolder) {
    $result = upload_dir($subfolder);
    echo "<p>upload_dir('$subfolder') = $result</p>\n";
    
    if (is_dir($result)) {
        echo "<p style='color: green;'>✓ Directory exists</p>\n";
        
        if (is_writable($result)) {
            echo "<p style='color: green;'>✓ Directory is writable</p>\n";
        } else {
            echo "<p style='color: red;'>✗ Directory is not writable</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>\n";
    }
}

// Test user and permissions
echo "<h2>User and Permissions Test</h2>\n";

echo "<ul>\n";
echo "<li>Current PHP user: " . get_current_user() . "</li>\n";
echo "<li>Effective user ID: " . (function_exists('posix_geteuid') ? posix_geteuid() : 'N/A') . "</li>\n";
echo "<li>Effective group ID: " . (function_exists('posix_getegid') ? posix_getegid() : 'N/A') . "</li>\n";

if (function_exists('posix_getpwuid')) {
    $userInfo = posix_getpwuid(posix_geteuid());
    if ($userInfo) {
        echo "<li>User info: " . $userInfo['name'] . " (UID: " . $userInfo['uid'] . ")</li>\n";
    }
}

echo "</ul>\n";

// Test creating a file in each directory
echo "<h2>File Creation Test</h2>\n";

foreach ($testDirs as $subfolder) {
    $dir = upload_dir($subfolder);
    echo "<h3>Testing: $dir</h3>\n";
    
    if (is_dir($dir)) {
        $testFile = $dir . '/.coolify_test_' . time() . '.txt';
        $testContent = "Coolify test file created at " . date('Y-m-d H:i:s') . "\n";
        
        if (@file_put_contents($testFile, $testContent)) {
            echo "<p style='color: green;'>✓ Successfully created test file</p>\n";
            
            // Verify it exists
            if (file_exists($testFile)) {
                echo "<p style='color: green;'>✓ Test file confirmed to exist</p>\n";
                
                // Clean up
                if (@unlink($testFile)) {
                    echo "<p style='color: green;'>✓ Test file cleaned up</p>\n";
                } else {
                    echo "<p style='color: orange;'>⚠ Could not clean up test file</p>\n";
                }
            } else {
                echo "<p style='color: red;'>✗ Test file does not exist after creation</p>\n";
            }
        } else {
            echo "<p style='color: red;'>✗ Failed to create test file</p>\n";
            echo "<p>Check permissions on $dir</p>\n";
        }
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist</p>\n";
    }
    
    echo "<hr>\n";
}

echo "<h2>Recommendations</h2>\n";
echo "<ol>\n";
echo "<li>Run the <a href='coolify_permission_fix.php'>Coolify Permission Fix script</a></li>\n";
echo "<li>If you have SSH access to your Coolify container, manually run:</li>\n";
echo "<pre>\n";
echo "mkdir -p /app/uploads /app/uploads/credit_cards /app/uploads/offers\n";
echo "chmod -R 775 /app/uploads\n";
echo "chown -R www-data:www-data /app/uploads\n";
echo "</pre>\n";
echo "<li>Check your Dockerfile to ensure it sets proper permissions</li>\n";
echo "<li>Verify your Coolify deployment settings for persistent storage</li>\n";
echo "</ol>\n";

echo "<p>Test completed at: " . date('Y-m-d H:i:s') . "</p>\n";
?>