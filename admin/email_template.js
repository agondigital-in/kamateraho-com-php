// Email template JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Set current year in footer
    document.getElementById('current-year').textContent = new Date().getFullYear();
    
    // Function to populate email template with content
    window.populateEmailTemplate = function(subject, message, userName) {
        // Set the subject
        document.getElementById('email-subject').textContent = subject;
        
        // Set the user greeting
        const greetingElement = document.getElementById('user-greeting');
        if (userName) {
            greetingElement.textContent = `Dear ${userName},`;
        } else {
            greetingElement.textContent = 'Dear User,';
        }
        
        // Set the message content
        document.getElementById('email-message').textContent = message;
    };
    
    // If we're viewing the template directly, populate with sample data
    if (window.location.pathname.includes('email_template.html')) {
        populateEmailTemplate(
            'Sample Email Subject',
            'This is a sample email message.\n\nIt contains multiple lines.\n\nThank you for using KamateRaho!',
            'John Doe'
        );
    }
});