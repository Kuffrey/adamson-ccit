/**
 * Admin UI Improvements for Programs Page
 * This script enhances form submission and provides better user feedback
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Programs page enhancements loaded');
    
    // Mark all forms that have card_id input with a data attribute
    document.querySelectorAll('form').forEach(form => {
        const cardIdInput = form.querySelector('input[name="card_id"]');
        if (cardIdInput) {
            form.setAttribute('data-card-id', cardIdInput.value);
            console.log(`Form marked with card ID: ${cardIdInput.value}`);
        }
    });
    
    // Add validation indicators to required fields
    document.querySelectorAll('input[name="card[title]"]').forEach(input => {
        input.setAttribute('required', 'true');
        
        // Add visual indicator for required fields
        const label = input.previousElementSibling;
        if (label && label.tagName === 'LABEL') {
            if (!label.innerHTML.includes('*')) {
                label.innerHTML += ' <span style="color:red;">*</span>';
            }
        }
    });
});