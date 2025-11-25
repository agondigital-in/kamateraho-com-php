<?php
/**
 * Email Verification Helper Functions
 */

/**
 * Generate a 4-digit verification code
 * @return string
 */
function generateVerificationCode() {
    return sprintf("%04d", rand(0, 9999));
}

/**
 * Store verification code in database
 * @param PDO $pdo
 * @param string $email
 * @param string $code
 * @return bool
 */
function storeVerificationCode($pdo, $email, $code) {
    try {
        // Delete any existing codes for this email
        $stmt = $pdo->prepare("DELETE FROM email_verification WHERE email = ?");
        $stmt->execute([$email]);
        
        // Insert new verification code (expires in 1 hour)
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt = $pdo->prepare("INSERT INTO email_verification (email, verification_code, expires_at) VALUES (?, ?, ?)");
        $result = $stmt->execute([$email, $code, $expires_at]);
        
        return $result;
    } catch (PDOException $e) {
        error_log("Error storing verification code: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify code from database
 * @param PDO $pdo
 * @param string $email
 * @param string $code
 * @return bool
 */
function verifyCode($pdo, $email, $code) {
    try {
        $stmt = $pdo->prepare("SELECT id, is_verified FROM email_verification WHERE email = ? AND verification_code = ? AND expires_at > NOW()");
        $stmt->execute([$email, $code]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Mark as verified
            $updateStmt = $pdo->prepare("UPDATE email_verification SET is_verified = TRUE WHERE id = ?");
            $updateStmt->execute([$result['id']]);
            
            // Update user's email_verified status
            $userStmt = $pdo->prepare("UPDATE users SET email_verified = TRUE WHERE email = ?");
            $userStmt->execute([$email]);
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Error verifying code: " . $e->getMessage());
        return false;
    }
}

/**
 * Send verification email
 * @param string $email
 * @param string $code
 * @param string $name
 * @return bool
 */
function sendVerificationEmail($email, $code, $name = '') {
    // Include the email template
    include_once __DIR__ . '/../admin/email_template.php';
    
    // Prepare email content
    $emailSubject = 'Email Verification for KamateRaho';
    $emailMessage = "Hello" . ($name ? " $name" : "") . ",\n\nThank you for registering with KamateRaho!\n\nYour email verification code is: $code\n\nPlease enter this code on the registration page to verify your email address.\n\nThis code will expire in 1 hour.\n\nIf you did not request this verification, please ignore this email.\n\nThank you for using KamateRaho!";
    
    // Generate HTML email content
    $htmlContent = getEmailTemplate($emailSubject, $emailMessage, $name);
    
    // Prepare data for API call with HTML content
    $api_data = [
        'email' => $email,
        'subject' => $emailSubject,
        'message' => $emailMessage,
        'html' => $htmlContent
    ];
    
    // API endpoint
    $url = 'https://mail2.kamateraho.com/send-email';
    
    // Authorization token
    $token = 'km_ritik_ritikyW8joeSZUHp6zgPm8Y8';
    
    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($api_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $http_code === 200;
}

/**
 * Check if email is already verified
 * @param PDO $pdo
 * @param string $email
 * @return bool
 */
function isEmailVerified($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT email_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result && $result['email_verified'] == 1;
    } catch (PDOException $e) {
        error_log("Error checking email verification status: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if verification code exists for email
 * @param PDO $pdo
 * @param string $email
 * @return bool
 */
function hasVerificationCode($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM email_verification WHERE email = ? AND expires_at > NOW()");
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Error checking verification code: " . $e->getMessage());
        return false;
    }
}
?>