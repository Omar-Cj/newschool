/**
 * Feature Selector JavaScript
 * Handles feature selection, group operations, and form validation
 */

(function($) {
    'use strict';

    // Feature Selector Class
    class FeatureSelector {
        constructor(container) {
            this.container = $(container);
            this.init();
        }

        init() {
            this.attachEventListeners();
            this.updateAllCounts();
            this.initializeTooltips();
        }

        attachEventListeners() {
            // Select all in group
            this.container.on('click', '.select-all-btn', (e) => {
                e.preventDefault();
                const groupId = $(e.currentTarget).data('group-id');
                this.selectAllInGroup(groupId);
            });

            // Deselect all in group
            this.container.on('click', '.select-none-btn', (e) => {
                e.preventDefault();
                const groupId = $(e.currentTarget).data('group-id');
                this.deselectAllInGroup(groupId);
            });

            // Individual checkbox change
            this.container.on('change', '.feature-checkbox', (e) => {
                this.handleCheckboxChange($(e.currentTarget));
            });

            // Global select all (if implemented)
            this.container.on('click', '.global-select-all', (e) => {
                e.preventDefault();
                this.selectAll();
            });

            // Global deselect all (if implemented)
            this.container.on('click', '.global-deselect-all', (e) => {
                e.preventDefault();
                this.deselectAll();
            });
        }

        selectAllInGroup(groupId) {
            const checkboxes = this.container.find(`.feature-checkbox[data-group-id="${groupId}"]`);
            checkboxes.prop('checked', true).trigger('change');
            this.showNotification(`All features selected in group`, 'success');
        }

        deselectAllInGroup(groupId) {
            const checkboxes = this.container.find(`.feature-checkbox[data-group-id="${groupId}"]`);
            checkboxes.prop('checked', false).trigger('change');
            this.showNotification(`All features deselected in group`, 'info');
        }

        selectAll() {
            this.container.find('.feature-checkbox').prop('checked', true).trigger('change');
            this.showNotification('All features selected', 'success');
        }

        deselectAll() {
            this.container.find('.feature-checkbox').prop('checked', false).trigger('change');
            this.showNotification('All features deselected', 'info');
        }

        handleCheckboxChange($checkbox) {
            const featureItem = $checkbox.closest('.feature-item');
            const isPremium = $checkbox.closest('.feature-item').find('.fa-star').length > 0;

            // Update visual state
            if ($checkbox.is(':checked')) {
                featureItem.addClass('selected');
                if (isPremium) {
                    this.showPremiumNotice();
                }
            } else {
                featureItem.removeClass('selected');
            }

            // Update counts
            this.updateGroupCount($checkbox.data('group-id'));
            this.updateTotalCount();
        }

        updateGroupCount(groupId) {
            const card = this.container.find(`.feature-group-card[data-group-id="${groupId}"]`);
            const total = card.find('.feature-checkbox').length;
            const selected = card.find('.feature-checkbox:checked').length;
            card.find('.selected-count').text(selected);

            // Update visual indicator
            if (selected === total) {
                card.addClass('all-selected');
            } else {
                card.removeClass('all-selected');
            }
        }

        updateTotalCount() {
            const total = this.container.find('.feature-checkbox:checked').length;
            $('#total-selected-count').text(total);

            // Update form validation state
            this.validateSelection();
        }

        updateAllCounts() {
            // Update all group counts
            this.container.find('.feature-group-card').each((index, card) => {
                const groupId = $(card).data('group-id');
                this.updateGroupCount(groupId);
            });

            // Update total count
            this.updateTotalCount();
        }

        validateSelection() {
            const selectedCount = this.container.find('.feature-checkbox:checked').length;
            const submitButton = $('button[type="submit"]');

            if (selectedCount === 0) {
                submitButton.attr('disabled', false); // Allow submit even with no features
                return false;
            } else {
                submitButton.attr('disabled', false);
                return true;
            }
        }

        showPremiumNotice() {
            // Show a notice that premium features are selected (optional)
            if (!this.premiumNoticeShown) {
                this.premiumNoticeShown = true;
                // Could show a toast or notice here
            }
        }

        initializeTooltips() {
            if (typeof $.fn.tooltip !== 'undefined') {
                this.container.find('[data-toggle="tooltip"]').tooltip();
            }
        }

        showNotification(message, type = 'info') {
            // Use toastr if available
            if (typeof toastr !== 'undefined') {
                toastr[type](message);
            }
        }

        // Get selected feature IDs
        getSelectedFeatures() {
            const selected = [];
            this.container.find('.feature-checkbox:checked').each(function() {
                selected.push($(this).val());
            });
            return selected;
        }

        // Get selected features by group
        getSelectedFeaturesByGroup() {
            const grouped = {};
            this.container.find('.feature-checkbox:checked').each(function() {
                const groupId = $(this).data('group-id');
                if (!grouped[groupId]) {
                    grouped[groupId] = [];
                }
                grouped[groupId].push($(this).val());
            });
            return grouped;
        }

        // Load features from array
        loadFeatures(featureIds) {
            this.container.find('.feature-checkbox').prop('checked', false);
            featureIds.forEach(id => {
                this.container.find(`.feature-checkbox[value="${id}"]`).prop('checked', true);
            });
            this.updateAllCounts();
        }
    }

    // Initialize feature selectors on page load
    $(document).ready(function() {
        if ($('.feature-selector-wrapper').length > 0) {
            window.featureSelector = new FeatureSelector('.feature-selector-wrapper');
        }
    });

    // Export to global scope
    window.FeatureSelector = FeatureSelector;

})(jQuery);

/**
 * Form validation integration
 */
$(document).ready(function() {
    // Prevent form submission if validation fails (optional)
    $('form').on('submit', function(e) {
        if (window.featureSelector) {
            // Could add validation here if needed
            // For now, we allow empty selections
            return true;
        }
    });

    // Warn about premium features
    $('.feature-checkbox').on('change', function() {
        const isPremium = $(this).closest('.feature-item').find('.fa-star').length > 0;
        if (isPremium && $(this).is(':checked')) {
            // Could show a confirmation or notice
            console.log('Premium feature selected:', $(this).val());
        }
    });

    // Show feature comparison (optional enhancement)
    if ($('#show-comparison').length > 0) {
        $('#show-comparison').on('click', function() {
            showFeatureComparison();
        });
    }
});

/**
 * Feature comparison modal (optional)
 */
function showFeatureComparison() {
    const selected = window.featureSelector ? window.featureSelector.getSelectedFeatures() : [];

    // Build comparison view
    let html = '<div class="feature-comparison">';
    html += '<h5>Selected Features Summary</h5>';
    html += '<ul>';

    $('.feature-checkbox:checked').each(function() {
        const label = $(this).siblings('label').find('.feature-name').text().trim();
        html += `<li>${label}</li>`;
    });

    html += '</ul>';
    html += '</div>';

    // Could show in a modal using Bootstrap or SweetAlert
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Feature Summary',
            html: html,
            width: 600,
            confirmButtonText: 'Close'
        });
    }
}

/**
 * Keyboard shortcuts
 */
$(document).on('keydown', function(e) {
    // Ctrl/Cmd + A to select all features
    if ((e.ctrlKey || e.metaKey) && e.key === 'a' && $('.feature-selector-wrapper:focus-within').length > 0) {
        e.preventDefault();
        if (window.featureSelector) {
            window.featureSelector.selectAll();
        }
    }

    // Ctrl/Cmd + D to deselect all features
    if ((e.ctrlKey || e.metaKey) && e.key === 'd' && $('.feature-selector-wrapper:focus-within').length > 0) {
        e.preventDefault();
        if (window.featureSelector) {
            window.featureSelector.deselectAll();
        }
    }
});
