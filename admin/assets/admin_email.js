// Email functionality for admin panel
document.addEventListener('DOMContentLoaded', function() {
    // Email modal functionality
    const emailModal = document.getElementById('emailModal');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    const emailForm = document.getElementById('emailForm');
    const selectedUsersCount = document.getElementById('selectedUsersCount');
    const emailPreview = document.getElementById('emailPreview');
    const subjectInput = document.getElementById('emailSubject');
    const messageInput = document.getElementById('emailMessage');
    
    // Update selected users count
    function updateSelectedUsersCount() {
        const selectedCheckboxes = document.querySelectorAll('input[name="selected_users[]"]:checked');
        const count = selectedCheckboxes.length;
        
        if (selectedUsersCount) {
            selectedUsersCount.textContent = count;
        }
        
        // Enable/disable send email button
        if (sendEmailBtn) {
            sendEmailBtn.disabled = count === 0;
        }
    }
    
    // Update email preview
    function updateEmailPreview() {
        if (emailPreview) {
            const subject = subjectInput ? subjectInput.value : '';
            const message = messageInput ? messageInput.value : '';
            
            emailPreview.innerHTML = `
                <h6>Email Preview</h6>
                <p><strong>Subject:</strong> ${subject || '(No subject)'}</p>
                <p><strong>Message:</strong></p>
                <div style="white-space: pre-wrap; background: white; padding: 10px; border-radius: 5px; border: 1px solid #ddd;">
                    ${message || '(No message)'}
                </div>
            `;
        }
    }
    
    // Event listeners for checkboxes
    const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedUsersCount);
    });
    
    // Event listeners for select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedUsersCount();
        });
    }
    
    // Update select all checkbox state when individual checkboxes change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (selectAllCheckbox) {
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
    
    // Event listeners for email form inputs
    if (subjectInput) {
        subjectInput.addEventListener('input', updateEmailPreview);
    }
    
    if (messageInput) {
        messageInput.addEventListener('input', updateEmailPreview);
    }
    
    // Initialize
    updateSelectedUsersCount();
    updateEmailPreview();
    
    // Handle form submission
    if (emailForm) {
        emailForm.addEventListener('submit', function(e) {
            const selectedCheckboxes = document.querySelectorAll('input[name="selected_users[]"]:checked');
            
            // Check if any users are selected
            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one user to send the email.');
                return false;
            }
            
            // Check if subject is filled
            if (!subjectInput || !subjectInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a subject for the email.');
                subjectInput.focus();
                return false;
            }
            
            // Check if message is filled
            if (!messageInput || !messageInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a message for the email.');
                messageInput.focus();
                return false;
            }
            
            // Confirm sending
            if (!confirm(`Are you sure you want to send this email to ${selectedCheckboxes.length} selected user(s)?`)) {
                e.preventDefault();
                return false;
            }
        });
    }
});