<?php
// Test the Diwali email template
include 'diwali_email_template.php';

// Generate the Diwali email template
$emailHtml = getDiwaliEmailTemplate("John Doe");

// Display the generated HTML
echo $emailHtml;
?>