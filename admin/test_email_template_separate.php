<?php
// Test email template with separate HTML, CSS, and JS files
include 'email_template.php';

// Test the template with sample data
$subject = "Test Email with Separate Files";
$message = "This is a test email message using separate HTML, CSS, and JavaScript files.\n\nIt contains multiple lines.\n\nThank you!";
$userName = "John Doe";

// Generate the email template
$emailHtml = getEmailTemplate($subject, $message, $userName);

// Display the generated HTML
echo $emailHtml;
?>