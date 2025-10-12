/**
 * Form Validation Utility
 * Handles client-side validation for dynamic report forms
 */

export class FormValidation {
    constructor() {
        this.translations = window.ReportConfig?.translations || {};
    }

    /**
     * Validate entire form
     * @param {HTMLFormElement} form - Form element to validate
     * @returns {Object} Validation result with isValid flag and errors
     */
    validateForm(form) {
        const result = {
            isValid: true,
            errors: {}
        };

        const fields = form.querySelectorAll('[data-parameter-name]');

        fields.forEach(field => {
            const parameterName = field.dataset.parameterName;
            const isRequired = field.dataset.required === 'true';
            const parameterType = field.dataset.parameterType;

            const fieldResult = this.validateField(field, isRequired, parameterType);

            if (!fieldResult.isValid) {
                result.isValid = false;
                result.errors[parameterName] = fieldResult.error;
                this.showFieldError(field, fieldResult.error);
            } else {
                this.clearFieldError(field);
            }
        });

        return result;
    }

    /**
     * Validate individual field
     * @param {HTMLElement} field - Field element to validate
     * @param {boolean} isRequired - Whether field is required
     * @param {string} type - Parameter type (date, number, text, etc.)
     * @returns {Object} Validation result
     */
    validateField(field, isRequired, type) {
        const value = this.getFieldValue(field);
        const result = { isValid: true, error: null };

        // Check required fields
        if (isRequired && this.isEmpty(value)) {
            result.isValid = false;
            result.error = this.translations.requiredField || 'This field is required';
            return result;
        }

        // Skip type validation if field is empty and not required
        if (this.isEmpty(value)) {
            return result;
        }

        // Type-specific validation
        switch (type) {
            case 'date':
                if (!this.isValidDate(value)) {
                    result.isValid = false;
                    result.error = 'Please enter a valid date';
                }
                break;

            case 'number':
                if (!this.isValidNumber(value)) {
                    result.isValid = false;
                    result.error = 'Please enter a valid number';
                }
                break;

            case 'email':
                if (!this.isValidEmail(value)) {
                    result.isValid = false;
                    result.error = 'Please enter a valid email address';
                }
                break;

            case 'select':
                if (value === '' || value === null) {
                    result.isValid = false;
                    result.error = 'Please select an option';
                }
                break;

            case 'multiselect':
                if (!Array.isArray(value) || value.length === 0) {
                    result.isValid = false;
                    result.error = 'Please select at least one option';
                }
                break;
        }

        return result;
    }

    /**
     * Get field value based on input type
     * @param {HTMLElement} field - Field element
     * @returns {*} Field value
     */
    getFieldValue(field) {
        if (field.type === 'checkbox') {
            return field.checked;
        }

        if (field.multiple) {
            const selectedOptions = Array.from(field.selectedOptions);
            return selectedOptions.map(option => option.value);
        }

        return field.value?.trim();
    }

    /**
     * Check if value is empty
     * @param {*} value - Value to check
     * @returns {boolean} True if empty
     */
    isEmpty(value) {
        if (value === null || value === undefined) return true;
        if (typeof value === 'string') return value.trim() === '';
        if (Array.isArray(value)) return value.length === 0;
        if (typeof value === 'boolean') return false;
        return false;
    }

    /**
     * Validate date format
     * @param {string} dateString - Date string to validate
     * @returns {boolean} True if valid date
     */
    isValidDate(dateString) {
        if (!dateString) return false;

        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date.getTime());
    }

    /**
     * Validate number format
     * @param {string} value - Value to validate
     * @returns {boolean} True if valid number
     */
    isValidNumber(value) {
        if (value === '' || value === null) return false;
        return !isNaN(value) && !isNaN(parseFloat(value));
    }

    /**
     * Validate email format
     * @param {string} email - Email to validate
     * @returns {boolean} True if valid email
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show error message for field
     * @param {HTMLElement} field - Field element
     * @param {string} errorMessage - Error message to display
     */
    showFieldError(field, errorMessage) {
        const container = field.closest('.parameter-field');
        if (!container) return;

        // Add error class
        container.classList.add('has-error');
        field.classList.add('is-invalid');

        // Remove existing error message
        this.clearFieldError(field);

        // Create and append error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = errorMessage;
        errorDiv.setAttribute('data-validation-error', 'true');

        field.parentNode.appendChild(errorDiv);

        // Add aria attributes for accessibility
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', `${field.id}-error`);
        errorDiv.id = `${field.id}-error`;
    }

    /**
     * Clear error message from field
     * @param {HTMLElement} field - Field element
     */
    clearFieldError(field) {
        const container = field.closest('.parameter-field');
        if (!container) return;

        // Remove error classes
        container.classList.remove('has-error');
        field.classList.remove('is-invalid');

        // Remove error message
        const existingError = field.parentNode.querySelector('[data-validation-error="true"]');
        if (existingError) {
            existingError.remove();
        }

        // Remove aria attributes
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
    }

    /**
     * Clear all form errors
     * @param {HTMLFormElement} form - Form element
     */
    clearAllErrors(form) {
        const fields = form.querySelectorAll('[data-parameter-name]');
        fields.forEach(field => this.clearFieldError(field));
    }

    /**
     * Display validation summary
     * @param {Object} errors - Validation errors object
     * @param {HTMLElement} container - Container to display summary
     */
    displayValidationSummary(errors, container) {
        if (!container) return;

        const errorCount = Object.keys(errors).length;
        if (errorCount === 0) {
            container.innerHTML = '';
            container.style.display = 'none';
            return;
        }

        const summaryHtml = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>${this.translations.validationError || 'Validation Error'}</strong>
                <p class="mb-2">Please correct the following errors:</p>
                <ul class="mb-0">
                    ${Object.entries(errors).map(([field, error]) =>
                        `<li>${error}</li>`
                    ).join('')}
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        container.innerHTML = summaryHtml;
        container.style.display = 'block';

        // Scroll to error summary
        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Validate field on blur
     * @param {HTMLElement} field - Field element
     * @param {boolean} isRequired - Whether field is required
     * @param {string} type - Parameter type
     */
    attachBlurValidation(field, isRequired, type) {
        field.addEventListener('blur', () => {
            const result = this.validateField(field, isRequired, type);
            if (!result.isValid) {
                this.showFieldError(field, result.error);
            } else {
                this.clearFieldError(field);
            }
        });

        // Clear error on input
        field.addEventListener('input', () => {
            if (field.classList.contains('is-invalid')) {
                this.clearFieldError(field);
            }
        });
    }
}

// Export singleton instance
export default new FormValidation();
