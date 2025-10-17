<?php
/**
 * Email Template for KamateRaho
 * This template can be used for sending HTML emails
 */

// Prevent multiple inclusions
if (!function_exists('getEmailTemplate')) {
function getEmailTemplate($subject, $message, $userName = '') {
    // Read the HTML template file
    $templatePath = __DIR__ . '/email_template.html';
    $cssPath = __DIR__ . '/email_template.css';
    
    if (!file_exists($templatePath) || !file_exists($cssPath)) {
        // Fallback to inline template if files don't exist
        return '<!DOCTYPE html>
<html>
<head>
    <title>' . htmlspecialchars($subject) . '</title>
</head>
<body>
    <h1>' . htmlspecialchars($subject) . '</h1>
    ' . ($userName ? '<p>Dear ' . htmlspecialchars($userName) . ',</p>' : '') . '
    <p>' . nl2br(htmlspecialchars($message)) . '</p>
    <p>Best regards,<br>KamateRaho Team</p>
</body>
</html>';
    }
    
    // Read the HTML template
    $template = file_get_contents($templatePath);
    
    // Read the CSS and inject it into the template
    $css = file_get_contents($cssPath);
    
    // Replace placeholders with actual content
    $template = str_replace('<title>Email Template</title>', '<title>' . htmlspecialchars($subject) . '</title>', $template);
    $template = str_replace('<h1 id="email-subject">Email Subject</h1>', '<h1 id="email-subject">' . htmlspecialchars($subject) . '</h1>', $template);
    
    if ($userName) {
        $template = str_replace('<p id="user-greeting"></p>', '<p id="user-greeting">Dear ' . htmlspecialchars($userName) . ',</p>', $template);
    } else {
        $template = str_replace('<p id="user-greeting"></p>', '<p id="user-greeting">Dear User,</p>', $template);
    }
    
    $template = str_replace('<p id="email-message"></p>', '<p id="email-message">' . nl2br(htmlspecialchars($message)) . '</p>', $template);
    
    // Inject CSS into the template
    $template = str_replace('<link rel="stylesheet" href="email_template.css">', '<style>' . $css . '</style>', $template);
    
    // Remove script tag since we don't need JavaScript in emails
    $template = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $template);
    
    return $template;
}
} // Close function_exists check
?>