<?php
// Test email template
include 'email_template.php';

// Test the template with sample data
$subject = "Test Email Subject";
$message = "This is a test email message.\n\nIt contains multiple lines.\n\nThank you!";
$userName = "John Doe";

// Generate the email template
$emailHtml = getEmailTemplate($subject, $message, $userName);

// Display the generated HTML
echo $emailHtml;
?>