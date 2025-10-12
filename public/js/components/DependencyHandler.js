/**
 * Dependency Handler
 * Manages cascading dropdowns and parameter dependencies
 */

import ReportApiService from '../services/ReportApiService.js';

export class DependencyHandler {
    constructor() {
        this.apiService = ReportApiService;
        this.translations = window.ReportConfig?.translations || {};
        this.dependencies = new Map();
        this.dependencyChain = new Map();
    }

    /**
     * Register parameter dependencies
     * @param {Array} parameters - Array of parameter objects
     */
    registerDependencies(parameters) {
        this.dependencies.clear();
        this.dependencyChain.clear();

        parameters.forEach(param => {
            if (param.depends_on && param.parent_id) {
                // Store dependency mapping
                if (!this.dependencies.has(param.depends_on)) {
                    this.dependencies.set(param.depends_on, []);
                }
                this.dependencies.get(param.depends_on).push(param);

                // Build dependency chain for multi-level dependencies
                this.buildDependencyChain(param);
            }
        });
    }

    /**
     * Build dependency chain for multi-level dependencies
     * @param {Object} parameter - Parameter object
     */
    buildDependencyChain(parameter) {
        const chain = [parameter.name];
        let current = parameter;

        // Traverse up the dependency tree
        while (current.depends_on) {
            chain.unshift(current.depends_on);
            // Find parent parameter
            const parentParam = Array.from(this.dependencies.values())
                .flat()
                .find(p => p.name === current.depends_on);

            if (!parentParam) break;
            current = parentParam;
        }

        this.dependencyChain.set(parameter.name, chain);
    }

    /**
     * Attach change listeners to parent fields
     * @param {HTMLFormElement} form - Form element
     */
    attachDependencyListeners(form) {
        this.dependencies.forEach((dependentParams, parentParamName) => {
            const parentField = form.querySelector(`[data-parameter-name="${parentParamName}"]`);

            if (parentField) {
                parentField.addEventListener('change', async (event) => {
                    await this.handleParentChange(event.target, dependentParams, form);
                });

                // Trigger initial load if parent has a value
                if (this.hasValue(parentField)) {
                    this.handleParentChange(parentField, dependentParams, form);
                }
            }
        });
    }

    /**
     * Handle parent field change event
     * @param {HTMLElement} parentField - Parent field element
     * @param {Array} dependentParams - Array of dependent parameters
     * @param {HTMLFormElement} form - Form element
     */
    async handleParentChange(parentField, dependentParams, form) {
        const parentValue = this.getFieldValue(parentField);

        for (const depParam of dependentParams) {
            const dependentField = form.querySelector(`[data-parameter-name="${depParam.name}"]`);

            if (!dependentField) continue;

            if (this.isEmpty(parentValue)) {
                // Reset dependent field if parent is empty
                this.resetDependentField(dependentField);
                // Also reset any children of this field
                await this.resetDependencyChain(depParam.name, form);
            } else {
                // Load dependent values
                await this.loadDependentValues(dependentField, depParam, parentValue);
            }
        }
    }

    /**
     * Load dependent values from API
     * @param {HTMLElement} field - Dependent field element
     * @param {Object} parameter - Parameter metadata
     * @param {*} parentValue - Parent field value
     */
    async loadDependentValues(field, parameter, parentValue) {
        const container = field.closest('.parameter-field');

        try {
            // Show loading state
            this.setFieldLoading(field, true);
            field.disabled = true;

            // Fetch dependent values
            const values = await this.apiService.fetchDependentValues(parameter.id, parentValue);

            // Update field options
            this.updateFieldOptions(field, values, parameter);

            // Enable field
            field.disabled = false;

        } catch (error) {
            console.error('Error loading dependent values:', error);
            this.showFieldError(field, 'Failed to load options. Please try again.');
        } finally {
            this.setFieldLoading(field, false);
        }
    }

    /**
     * Update select field options
     * @param {HTMLSelectElement} field - Select field element
     * @param {Array} values - Array of option objects {value, label}
     * @param {Object} parameter - Parameter metadata
     */
    updateFieldOptions(field, values, parameter) {
        // Clear existing options
        field.innerHTML = '';

        // Add placeholder option
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = parameter.placeholder || `Select ${parameter.label}`;
        field.appendChild(placeholder);

        // Add new options
        values.forEach(option => {
            const optionElement = document.createElement('option');
            optionElement.value = option.value;
            optionElement.textContent = option.label;
            field.appendChild(optionElement);
        });

        // Reset to placeholder
        field.value = '';

        // Trigger change event to update any dependent fields
        field.dispatchEvent(new Event('change', { bubbles: true }));
    }

    /**
     * Reset dependent field to initial state
     * @param {HTMLElement} field - Field element to reset
     */
    resetDependentField(field) {
        if (field.tagName === 'SELECT') {
            // Keep only the placeholder option
            const placeholder = field.querySelector('option[value=""]');
            field.innerHTML = '';
            if (placeholder) {
                field.appendChild(placeholder.cloneNode(true));
            }
            field.value = '';
        } else {
            field.value = '';
        }

        field.disabled = true;
        this.setFieldLoading(field, false);

        // Trigger change event
        field.dispatchEvent(new Event('change', { bubbles: true }));
    }

    /**
     * Reset entire dependency chain
     * @param {string} parameterName - Starting parameter name
     * @param {HTMLFormElement} form - Form element
     */
    async resetDependencyChain(parameterName, form) {
        const dependents = this.dependencies.get(parameterName);

        if (!dependents || dependents.length === 0) return;

        for (const depParam of dependents) {
            const field = form.querySelector(`[data-parameter-name="${depParam.name}"]`);
            if (field) {
                this.resetDependentField(field);
                // Recursively reset children
                await this.resetDependencyChain(depParam.name, form);
            }
        }
    }

    /**
     * Check if field has a value
     * @param {HTMLElement} field - Field element
     * @returns {boolean} True if field has value
     */
    hasValue(field) {
        const value = this.getFieldValue(field);
        return !this.isEmpty(value);
    }

    /**
     * Get field value
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
     * Set loading state for field
     * @param {HTMLElement} field - Field element
     * @param {boolean} isLoading - Loading state
     */
    setFieldLoading(field, isLoading) {
        const container = field.closest('.parameter-field');
        if (!container) return;

        if (isLoading) {
            container.classList.add('dependent-field');

            // Add spinner if not exists
            if (!container.querySelector('.spinner-border')) {
                const spinner = document.createElement('div');
                spinner.className = 'spinner-border text-primary';
                spinner.setAttribute('role', 'status');
                spinner.innerHTML = '<span class="visually-hidden">Loading...</span>';
                container.appendChild(spinner);
            }
        } else {
            container.classList.remove('dependent-field');

            // Remove spinner
            const spinner = container.querySelector('.spinner-border');
            if (spinner) {
                spinner.remove();
            }
        }
    }

    /**
     * Show error message for field
     * @param {HTMLElement} field - Field element
     * @param {string} message - Error message
     */
    showFieldError(field, message) {
        const container = field.closest('.parameter-field');
        if (!container) return;

        // Remove existing error
        const existingError = container.querySelector('.dependency-error');
        if (existingError) existingError.remove();

        // Create error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback dependency-error';
        errorDiv.style.display = 'block';
        errorDiv.textContent = message;

        field.parentNode.appendChild(errorDiv);
        field.classList.add('is-invalid');
    }

    /**
     * Get dependency chain for a parameter
     * @param {string} parameterName - Parameter name
     * @returns {Array} Dependency chain
     */
    getDependencyChain(parameterName) {
        return this.dependencyChain.get(parameterName) || [];
    }

    /**
     * Check if parameter has dependencies
     * @param {string} parameterName - Parameter name
     * @returns {boolean} True if has dependencies
     */
    hasDependencies(parameterName) {
        return this.dependencies.has(parameterName);
    }

    /**
     * Get all dependent parameters for a parent
     * @param {string} parentParamName - Parent parameter name
     * @returns {Array} Array of dependent parameters
     */
    getDependents(parentParamName) {
        return this.dependencies.get(parentParamName) || [];
    }

    /**
     * Clear all dependencies
     */
    clear() {
        this.dependencies.clear();
        this.dependencyChain.clear();
    }
}

// Export singleton instance
export default new DependencyHandler();
