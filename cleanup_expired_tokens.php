<?php
// Script to clean up expired password reset tokens
include 'password_reset_tokens.php';

// This script will clean up expired tokens when run
// You can set up a cron job to run this script periodically

class TokenCleanup {
    public static function cleanupExpiredTokens() {
        $tokenFile = __DIR__ . '/password_reset_tokens.json';
        
        if (file_exists($tokenFile)) {
            $content = file_get_contents($tokenFile);
            $tokens = json_decode($content, true) ?: [];
            
            $cleanedTokens = [];
            $currentTime = time();
            
            // Keep only tokens that are less than 1 hour old
            foreach ($tokens as $token => $data) {
                if ($currentTime - $data['created_at'] < 3600) {
                    $cleanedTokens[$token] = $data;
                }
            }
            
            // Save the cleaned tokens back to the file
            file_put_contents($tokenFile, json_encode($cleanedTokens));
            
            echo "Cleaned up " . (count($tokens) - count($cleanedTokens)) . " expired tokens.\n";
        } else {
            echo "No token file found.\n";
        }
    }
}

// Run the cleanup if this script is executed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_NAME'])) {
    TokenCleanup::cleanupExpiredTokens();
}
?>