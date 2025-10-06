<?php
// Simple token management for password reset
class PasswordResetTokens {
    private static $tokenFile = __DIR__ . '/password_reset_tokens.json';
    
    // Generate a unique token for a user
    public static function generateToken($email) {
        $tokens = self::getTokens();
        $token = bin2hex(random_bytes(32)); // Generate a 64-character hex token
        $tokens[$token] = [
            'email' => $email,
            'created_at' => time()
        ];
        self::saveTokens($tokens);
        return $token;
    }
    
    // Validate a token
    public static function validateToken($token) {
        $tokens = self::getTokens();
        // Debug information
        // error_log("Validating token: " . $token);
        // error_log("Available tokens: " . print_r($tokens, true));
        
        if (isset($tokens[$token])) {
            // Check if token is less than 1 hour old
            $tokenAge = time() - $tokens[$token]['created_at'];
            // error_log("Token age: " . $tokenAge . " seconds");
            
            if ($tokenAge < 3600) { // 1 hour = 3600 seconds
                return $tokens[$token]['email'];
            } else {
                // Token expired, remove it
                // error_log("Token expired, removing it");
                unset($tokens[$token]);
                self::saveTokens($tokens);
            }
        } else {
            // error_log("Token not found");
        }
        return false;
    }
    
    // Remove a token after use
    public static function removeToken($token) {
        $tokens = self::getTokens();
        if (isset($tokens[$token])) {
            unset($tokens[$token]);
            self::saveTokens($tokens);
        }
    }
    
    // Get all tokens
    private static function getTokens() {
        if (file_exists(self::$tokenFile)) {
            $content = file_get_contents(self::$tokenFile);
            return json_decode($content, true) ?: [];
        }
        return [];
    }
    
    // Save tokens to file
    private static function saveTokens($tokens) {
        file_put_contents(self::$tokenFile, json_encode($tokens));
    }
}
?>