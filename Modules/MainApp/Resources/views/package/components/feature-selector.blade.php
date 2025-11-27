{{--
    Feature Selector Component

    Displays organized feature groups with permission features for package configuration.
    Uses FeatureGroup and PermissionFeature models with Permission relationships.

    Props:
    - $feature_groups: Collection of FeatureGroup models with permissionFeatures relationship loaded
    - $selected_features: Array of selected PermissionFeature IDs (optional, defaults to empty array)

    Expected Structure:
    - FeatureGroup: id, name, slug, description, icon, position, status
    - PermissionFeature: id, name, description, is_premium, status, feature_group_id, permission_id
    - Permission: id, attribute (the permission keyword)
--}}

@php
    $selected_features = $selected_features ?? [];
@endphp

<div class="feature-selector-wrapper">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle"></i>
                <strong>{{ ___('common.Feature Selection') }}</strong>
                <p class="mb-0">
                    {{ ___('common.Select the features that will be available in this package. Features marked with a star are premium features.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="feature-groups-container">
                @forelse($feature_groups as $group)
                    <div class="feature-group-card mb-4" data-group-id="{{ $group->id }}">
                        <div class="feature-group-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($group->icon)
                                        <i class="{{ $group->icon }}"></i>
                                    @endif
                                    <strong>{{ $group->name }}</strong>
                                    <small class="text-muted ms-2">
                                        (<span class="selected-count">0</span> / {{ $group->permissionFeatures->count() }})
                                    </small>
                                </div>
                                <div class="group-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-all-btn" data-group-id="{{ $group->id }}">
                                        <i class="fa-solid fa-check-double"></i> {{ ___('common.Select All') }}
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary select-none-btn" data-group-id="{{ $group->id }}">
                                        <i class="fa-solid fa-times"></i> {{ ___('common.Deselect All') }}
                                    </button>
                                </div>
                            </div>
                            @if($group->description)
                                <p class="text-muted mb-0 mt-2">{{ $group->description }}</p>
                            @endif
                        </div>

                        <div class="feature-group-body">
                            <div class="row">
                                @forelse($group->permissionFeatures as $feature)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="feature-item {{ in_array($feature->id, $selected_features) ? 'selected' : '' }}">
                                            <div class="form-check">
                                                <input class="form-check-input feature-checkbox"
                                                       type="checkbox"
                                                       name="permission_features[]"
                                                       value="{{ $feature->id }}"
                                                       id="feature_{{ $feature->id }}"
                                                       data-group-id="{{ $group->id }}"
                                                       {{ in_array($feature->id, $selected_features) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="feature_{{ $feature->id }}">
                                                    <span class="feature-name">
                                                        {{ $feature->name }}
                                                        @if($feature->is_premium)
                                                            <i class="fa-solid fa-star text-warning ms-1" data-toggle="tooltip" title="{{ ___('common.Premium Feature') }}"></i>
                                                        @endif
                                                    </span>
                                                    @if($feature->description)
                                                        <i class="fa-solid fa-circle-info text-muted ms-1"
                                                           data-toggle="tooltip"
                                                           title="{{ $feature->description }}"></i>
                                                    @endif
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <code>{{ $feature->permission->attribute }}</code>
                                            </small>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <p class="text-muted text-center">{{ ___('common.No features in this group') }}</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        {{ ___('common.No feature groups available. Please create feature groups first.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-secondary">
                <strong>{{ ___('common.Total Selected Features') }}:</strong>
                <span class="badge bg-primary" id="total-selected-count">{{ count($selected_features) }}</span>
            </div>
        </div>
    </div>
</div>

<style>
.feature-selector-wrapper {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.feature-group-card {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
}

.feature-group-header {
    padding: 15px 20px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.feature-group-body {
    padding: 20px;
}

.feature-item {
    padding: 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    transition: all 0.3s ease;
    background-color: #ffffff;
}

.feature-item:hover {
    border-color: #007bff;
    background-color: #f8f9ff;
}

.feature-item.selected {
    border-color: #28a745;
    background-color: #f0fff4;
}

.feature-item .form-check {
    margin-bottom: 0;
}

.feature-item .form-check-input {
    margin-top: 0.15rem;
    border: 2px solid #6c757d;
    width: 1.2em;
    height: 1.2em;
}

.feature-item .form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.feature-item .form-check-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.feature-item .form-check-label {
    cursor: pointer;
    font-weight: 500;
}

.feature-name {
    font-size: 14px;
}

.group-actions .btn {
    font-size: 12px;
    padding: 4px 12px;
}

.selected-count {
    font-weight: bold;
    color: #007bff;
}

#total-selected-count {
    font-size: 16px;
    padding: 6px 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update counts
    function updateCounts() {
        // Update group counts
        document.querySelectorAll('.feature-group-card').forEach(function(card) {
            const groupId = card.dataset.groupId;
            const totalInGroup = card.querySelectorAll('.feature-checkbox').length;
            const selectedInGroup = card.querySelectorAll('.feature-checkbox:checked').length;
            const countElement = card.querySelector('.selected-count');
            if (countElement) {
                countElement.textContent = selectedInGroup;
            }
        });

        // Update total count
        const totalSelected = document.querySelectorAll('.feature-checkbox:checked').length;
        const totalCountElement = document.getElementById('total-selected-count');
        if (totalCountElement) {
            totalCountElement.textContent = totalSelected;
        }
    }

    // Select all in group
    document.querySelectorAll('.select-all-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const groupId = this.dataset.groupId;
            document.querySelectorAll('.feature-checkbox[data-group-id="' + groupId + '"]').forEach(function(checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    });

    // Deselect all in group
    document.querySelectorAll('.select-none-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const groupId = this.dataset.groupId;
            document.querySelectorAll('.feature-checkbox[data-group-id="' + groupId + '"]').forEach(function(checkbox) {
                checkbox.checked = false;
                checkbox.dispatchEvent(new Event('change'));
            });
        });
    });

    // Handle checkbox change
    document.querySelectorAll('.feature-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const featureItem = this.closest('.feature-item');
            if (featureItem) {
                if (this.checked) {
                    featureItem.classList.add('selected');
                } else {
                    featureItem.classList.remove('selected');
                }
            }
            updateCounts();
        });
    });

    // Initial count update
    updateCounts();
});
</script>
