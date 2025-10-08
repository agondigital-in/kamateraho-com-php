// Batch action functionality
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkboxes functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name^="selected_"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Batch form submission
    const batchForm = document.getElementById('batchForm');
    if (batchForm) {
        batchForm.addEventListener('submit', function(e) {
            const selectedCheckboxes = document.querySelectorAll('input[name^="selected_"]:checked');
            const batchAction = document.querySelector('select[name="batch_action"]');
            
            // Check if any items are selected
            if (selectedCheckboxes.length === 0) {
                e.preventDefault();
                alert('Please select at least one item to perform the action.');
                return false;
            }
            
            // Check if an action is selected
            if (!batchAction || !batchAction.value) {
                e.preventDefault();
                alert('Please select an action to perform.');
                return false;
            }
            
            // Confirm action
            const actionText = batchAction.options[batchAction.selectedIndex].text;
            if (!confirm(`Are you sure you want to ${actionText.toLowerCase()} ${selectedCheckboxes.length} selected item(s)?`)) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Update select all checkbox state when individual checkboxes change
    const individualCheckboxes = document.querySelectorAll('input[name^="selected_"]');
    individualCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (selectAllCheckbox) {
                const allChecked = Array.from(individualCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });
});