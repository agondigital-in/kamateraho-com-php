<?php
require_once 'config/db.php';
require_once 'includes/email_verification.php';

if ($pdo) {
    echo "Database connection successful!\n\n";
    
    // Test generating verification code
    echo "Testing verification code generation:\n";
    $code = generateVerificationCode();
    echo "Generated code: " . $code . "\n\n";
    
    // Test storing verification code
    echo "Testing storing verification code:\n";
    $email = "test@example.com";
    $result = storeVerificationCode($pdo, $email, $code);
    echo "Store result: " . ($result ? "Success" : "Failed") . "\n\n";
    
    // Test verifying code
    echo "Testing code verification:\n";
    $verify_result = verifyCode($pdo, $email, $code);
    echo "Verification result: " . ($verify_result ? "Valid" : "Invalid") . "\n\n";
    
    // Test checking if email is verified
    echo "Testing email verification status:\n";
    $is_verified = isEmailVerified($pdo, $email);
    echo "Email verified status: " . ($is_verified ? "Verified" : "Not verified") . "\n\n";
    
    echo "All tests completed!\n";
} else {
    echo "Database connection failed!\n";
}
?>