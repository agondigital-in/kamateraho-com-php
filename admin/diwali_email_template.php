<?php
/**
 * Diwali Special Offer Email Template for KamateRaho
 * Email-compatible version with inline CSS and no JavaScript
 */

// Prevent multiple inclusions
if (!function_exists('getDiwaliEmailTemplate')) {
function getDiwaliEmailTemplate($userName = '') {
    $template = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎆 Diwali Special Offer! - Kamate Raho</title>
</head>
<body style="margin: 0; padding: 0; font-family: \'Poppins\', Arial, sans-serif; background-color: #f5f5f5; color: #333; line-height: 1.6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 20px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); max-width: 600px;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #FFA000, #FF6F00); padding: 30px 20px; text-align: center; color: white;">
                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">🎆 Diwali Special Offer! 🎆</h1>
                            <p style="font-size: 16px; margin-top: 10px;">Light up your celebrations with us!</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p>Dear ' . ($userName ? htmlspecialchars($userName) : 'Valued Customer') . ',</p>
                            
                            <p>This Diwali, we\'re lighting up your celebrations with an exclusive festive reward! 🪔</p>
                            
                            <!-- Offer Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="background: #FFF9C4; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; border: 2px dashed #FFA000;">
                                <tr>
                                    <td>
                                        <p style="margin: 0; font-size: 16px;">Participate in our Maximum Offer and get</p>
                                        <div style="font-size: 48px; font-weight: 700; color: #D32F2F; margin: 10px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">₹1000</div>
                                        <p style="margin: 0; font-size: 16px;">credited instantly to your account balance 💸✨</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <p>But hurry — this special offer is for a limited time only!</p>
                            
                            <p>Click the button below to claim your reward now 👇</p>
                            
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="https://kamateraho.com/login.php" style="display: inline-block; background: linear-gradient(135deg, #FF5722, #F44336); color: white; text-decoration: none; padding: 15px 30px; border-radius: 50px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);">🎁 Claim My ₹1000 Now</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p>Wishing you and your family a bright, joyful, and prosperous Diwali! 🌟</p>
                            
                            <p>Best Regards,<br>
                            <strong>Team Kamate Raho</strong></p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: #333; color: #fff; text-align: center; padding: 20px; font-size: 14px;">
                            <p style="margin: 0;">© 2025 Kamate Raho. All rights reserved.</p>
                            <p style="margin: 10px 0 0 0;">If you wish to unsubscribe, <a href="#" style="color: #FFC107; text-decoration: none;">click here</a>.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    
    return $template;
}
} // Close function_exists check
?>