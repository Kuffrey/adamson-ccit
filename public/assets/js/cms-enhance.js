/**
 * Enhanced CMS Functionality
 * This script improves the admin UI with better validation, feedback, and error handling
 */

document.addEventListener('DOMContentLoaded', function() {
    // Debug info
    const isDev = window.location.hostname === 'localhost' || document.cookie.includes('debug=');
    
    if (isDev) {
        console.log('CMS Debug Mode Enabled');
        
        // Add debug toolbar if not already present
        if (!document.querySelector('#cms-debug-toolbar')) {
            const toolbar = document.createElement('div');
            toolbar.id = 'cms-debug-toolbar';
            toolbar.style.cssText = 'position:fixed;bottom:0;left:0;right:0;background:#222;color:#fff;padding:8px;z-index:9999;font-size:12px;display:flex;justify-content:space-between;';
            
            toolbar.innerHTML = `
                <div>
                    <span>Debug Mode</span> |
                    <a href="?disable_debug=1" style="color:#ff8080;">Disable</a>
                </div>
                <div>
                    <a href="?page=admin_debug" style="color:#8ff;">Debug Tools</a> |
                    Page: ${document.title}
                </div>
            `;
            
            document.body.appendChild(toolbar);
        }
    }
    
    // Enhance all card forms
    const cardForms = document.querySelectorAll('form[data-card-id]');
    cardForms.forEach(form => {
        enhanceCardForm(form);
    });
    
    // General form improvements
    const allForms = document.querySelectorAll('form');
    allForms.forEach(form => {
        // Add unsaved changes warning
        let hasChanges = false;
        const inputs = form.querySelectorAll('input, textarea, select');
        
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                hasChanges = true;
            });
        });
        
        // Only warn if not submitting the form normally
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges && !form.getAttribute('data-submitting')) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
        
        // Mark form as submitting when submitted
        form.addEventListener('submit', function() {
            form.setAttribute('data-submitting', 'true');
        });
    });
    
    // Add validation for required fields
    const requiredInputs = document.querySelectorAll('[data-required="true"], [required]');
    requiredInputs.forEach(input => {
        // Add visual indicator
        if (!input.classList.contains('required')) {
            input.classList.add('required');
            const label = input.closest('label') || document.querySelector(`label[for="${input.id}"]`);
            if (label) {
                label.innerHTML += ' <span class="required-mark" style="color:red;">*</span>';
            }
        }
        
        // Add validation on blur
        input.addEventListener('blur', function() {
            validateRequiredField(input);
        });
    });
    
    // Helper function to enhance card forms
    function enhanceCardForm(form) {
        const cardId = form.getAttribute('data-card-id');
        if (!cardId) return;
        
        console.log(`Enhancing card form for ID: ${cardId}`);
        
        // Add validation
        form.addEventListener('submit', function(e) {
            const titleInput = form.querySelector('input[name="card[title]"]');
            if (titleInput && (!titleInput.value || titleInput.value.trim() === '')) {
                e.preventDefault();
                showError(titleInput, 'Title is required');
                return false;
            }
            
            // Add timestamp for debugging
            appendHiddenField(form, 'debug_timestamp', new Date().toISOString());
        });
        
        // Add AJAX submission if not already handled
        if (!form.getAttribute('data-ajax-enhanced')) {
            form.setAttribute('data-ajax-enhanced', 'true');
            
            // Create status element if needed
            let statusEl = form.querySelector('.form-status');
            if (!statusEl) {
                statusEl = document.createElement('div');
                statusEl.className = 'form-status';
                statusEl.style.marginTop = '10px';
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.parentNode.appendChild(statusEl);
                } else {
                    form.appendChild(statusEl);
                }
            }
            
            // Update submit buttons with better labels
            const buttons = form.querySelectorAll('button[type="submit"]');
            buttons.forEach(btn => {
                if (!btn.getAttribute('data-original-text')) {
                    btn.setAttribute('data-original-text', btn.textContent);
                }
            });
        }
    }
    
    // Helper function to validate required fields
    function validateRequiredField(input) {
        const isValid = input.value && input.value.trim() !== '';
        
        if (!isValid) {
            showError(input, 'This field is required');
            return false;
        } else {
            clearError(input);
            return true;
        }
    }
    
    // Helper to show validation errors
    function showError(input, message) {
        // Clear existing error
        clearError(input);
        
        // Add error styling
        input.classList.add('is-invalid');
        
        // Create error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.85em';
        errorDiv.style.marginTop = '4px';
        errorDiv.textContent = message;
        
        // Insert after input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
        
        // Focus the input
        input.focus();
    }
    
    // Helper to clear validation errors
    function clearError(input) {
        input.classList.remove('is-invalid');
        
        // Remove any existing error messages
        const container = input.parentNode;
        const errors = container.querySelectorAll('.validation-error');
        errors.forEach(error => container.removeChild(error));
    }
    
    // Helper to append a hidden field to a form
    function appendHiddenField(form, name, value) {
        // Remove existing field with same name if it exists
        const existing = form.querySelector(`input[name="${name}"]`);
        if (existing) {
            existing.value = value;
            return;
        }
        
        // Create new field
        const field = document.createElement('input');
        field.type = 'hidden';
        field.name = name;
        field.value = value;
        form.appendChild(field);
    }
});