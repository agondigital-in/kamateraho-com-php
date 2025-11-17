<?php
require_once 'config/db.php';
require_once 'includes/email_verification.php';

if ($pdo) {
    echo "Database connection successful!\n\n";
    
    // Test checking if verification code exists
    echo "Testing verification code check:\n";
    $email = "test@example.com";
    
    // First check - should be false (no code stored)
    $has_code = hasVerificationCode($pdo, $email);
    echo "Has verification code (before storing): " . ($has_code ? "Yes" : "No") . "\n";
    
    // Store a verification code
    $code = generateVerificationCode();
    storeVerificationCode($pdo, $email, $code);
    echo "Stored verification code: $code\n";
    
    // Second check - should be true (code stored)
    $has_code = hasVerificationCode($pdo, $email);
    echo "Has verification code (after storing): " . ($has_code ? "Yes" : "No") . "\n";
    
    echo "\nAll tests completed!\n";
} else {
    echo "Database connection failed!\n";
}
?>