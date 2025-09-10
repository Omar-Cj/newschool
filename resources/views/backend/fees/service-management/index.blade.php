@extends('backend.master')
@section('title')
    {{ ___('fees.service_management_dashboard') }}
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('fees.service_management_dashboard') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('fees.service_management') }}</li>
                    </ol>
                </div>
                <div class="col-sm-6 text-end">
                    <button type="button" class="btn btn-lg ot-btn-primary" data-bs-toggle="modal" data-bs-target="#bulkSubscriptionModal">
                        <i class="fa-solid fa-users-cog"></i> {{ ___('fees.bulk_subscription') }}
                    </button>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area E n d --}}

        {{-- Service Overview Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-primary">
                    <div class="card-body text-center p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <i class="fa-solid fa-cogs fa-2x text-primary"></i>
                            <span id="total-services" class="h3 text-primary mb-0">0</span>
                        </div>
                        <small class="text-muted">{{ ___('fees.total_active_services') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-success">
                    <div class="card-body text-center p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <i class="fa-solid fa-user-graduate fa-2x text-success"></i>
                            <span id="students-with-services" class="h3 text-success mb-0">0</span>
                        </div>
                        <small class="text-muted">{{ ___('fees.students_with_services') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-warning">
                    <div class="card-body text-center p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <i class="fa-solid fa-exclamation-triangle fa-2x text-warning"></i>
                            <span id="pending-services" class="h3 text-warning mb-0">0</span>
                        </div>
                        <small class="text-muted">{{ ___('fees.services_due_soon') }}</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-info">
                    <div class="card-body text-center p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <i class="fa-solid fa-dollar-sign fa-2x text-info"></i>
                            <span id="total-revenue" class="h3 text-info mb-0">$0</span>
                        </div>
                        <small class="text-muted">{{ ___('fees.projected_revenue') }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ ___('fees.quick_actions') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-primary w-100" onclick="showStudentSearch()">
                            <i class="fa-solid fa-search"></i>
                            {{ ___('fees.find_student_services') }}
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-success w-100" onclick="showBulkDiscount()">
                            <i class="fa-solid fa-percentage"></i>
                            {{ ___('fees.apply_bulk_discount') }}
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-warning w-100" onclick="showOverdueServices()">
                            <i class="fa-solid fa-clock"></i>
                            {{ ___('fees.overdue_services') }}
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-info w-100" onclick="exportServiceReport()">
                            <i class="fa-solid fa-download"></i>
                            {{ ___('fees.export_report') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Categories Breakdown --}}
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ ___('fees.service_subscriptions_overview') }}</h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary active" data-filter="all">{{ ___('common.all') }}</button>
                            <button type="button" class="btn btn-outline-primary" data-filter="mandatory">{{ ___('fees.mandatory') }}</button>
                            <button type="button" class="btn btn-outline-primary" data-filter="optional">{{ ___('fees.optional') }}</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="services-table">
                                <thead>
                                    <tr>
                                        <th>{{ ___('fees.service_name') }}</th>
                                        <th>{{ ___('fees.category') }}</th>
                                        <th>{{ ___('fees.subscriptions') }}</th>
                                        <th>{{ ___('fees.revenue') }}</th>
                                        <th>{{ ___('fees.status') }}</th>
                                        <th>{{ ___('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="services-table-body">
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <div class="spinner-border" role="status">
                                                <span class="sr-only">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ ___('fees.recent_activities') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="recent-activities">
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Academic Level Analysis --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">{{ ___('fees.academic_level_analysis') }}</h5>
            </div>
            <div class="card-body">
                <div class="row" id="academic-level-stats">
                    <div class="col-12 text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Bulk Subscription Modal --}}
    <div class="modal fade" id="bulkSubscriptionModal" tabindex="-1" aria-labelledby="bulkSubscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="bulkSubscriptionModalLabel">
                        <i class="fa-solid fa-users-cog me-2"></i>{{ ___('fees.bulk_service_subscription') }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bulk-subscription-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bulk-classes" class="form-label">{{ ___('academic.classes') }}</label>
                                <select name="classes[]" id="bulk-classes" class="form-control select2" multiple required>
                                    <!-- Classes will be loaded dynamically -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bulk-sections" class="form-label">{{ ___('academic.sections') }}</label>
                                <select name="sections[]" id="bulk-sections" class="form-control select2" multiple>
                                    <option value="">{{ ___('common.all_sections') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="bulk-services" class="form-label">{{ ___('fees.services') }}</label>
                                <select name="services[]" id="bulk-services" class="form-control select2" multiple required>
                                    <!-- Services will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bulk-due-date" class="form-label">{{ ___('fees.due_date') }}</label>
                                <input type="date" name="due_date" id="bulk-due-date" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bulk-notes" class="form-label">{{ ___('common.notes') }}</label>
                                <textarea name="notes" id="bulk-notes" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            {{ ___('fees.bulk_subscription_info') }}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn ot-btn-secondary" data-bs-dismiss="modal">
                        {{ ___('common.cancel') }}
                    </button>
                    <button type="button" id="bulk-preview-btn" class="btn ot-btn-info">
                        <i class="fa-solid fa-eye"></i> {{ ___('fees.preview') }}
                    </button>
                    <button type="button" id="bulk-subscribe-btn" class="btn ot-btn-primary" style="display: none;">
                        <i class="fa-solid fa-bolt"></i> {{ ___('fees.subscribe_all') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Student Search Modal --}}
    <div class="modal fade" id="studentSearchModal" tabindex="-1" aria-labelledby="studentSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="studentSearchModalLabel">
                        <i class="fa-solid fa-search me-2"></i>{{ ___('fees.student_service_search') }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" id="student-search-input" class="form-control" placeholder="{{ ___('fees.search_by_name_admission') }}">
                        </div>
                        <div class="col-md-3">
                            <select id="class-filter" class="form-control">
                                <option value="">{{ ___('common.all_classes') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select id="service-filter" class="form-control">
                                <option value="">{{ ___('common.all_services') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn ot-btn-primary w-100" onclick="searchStudentServices()">
                                <i class="fa-solid fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div id="student-search-results">
                        <div class="text-center text-muted">
                            {{ ___('fees.enter_search_criteria') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
<style>
    .service-stats-card {
        transition: transform 0.2s;
    }
    .service-stats-card:hover {
        transform: translateY(-2px);
    }
    
    .activity-item {
        border-left: 3px solid #e9ecef;
        padding-left: 15px;
        margin-bottom: 15px;
        position: relative;
    }
    
    .activity-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 8px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #0d6efd;
    }
    
    .activity-item.success::before { background-color: #198754; }
    .activity-item.warning::before { background-color: #ffc107; }
    .activity-item.danger::before { background-color: #dc3545; }
    
    .quick-action-btn {
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    #services-table tbody tr {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    #services-table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endpush

@push('script')
<script>
$(document).ready(function() {
    // Load dashboard data
    loadDashboardStats();
    loadServicesTable();
    loadRecentActivities();
    loadAcademicLevelStats();
    
    // Initialize modals
    initializeBulkSubscriptionModal();
    
    // Service filter buttons
    $('.btn-group button[data-filter]').on('click', function() {
        $('.btn-group button').removeClass('active');
        $(this).addClass('active');
        
        const filter = $(this).data('filter');
        filterServicesTable(filter);
    });
    
    // Bulk subscription handlers
    $('#bulk-preview-btn').on('click', previewBulkSubscription);
    $('#bulk-subscribe-btn').on('click', executeBulkSubscription);
});

function loadDashboardStats() {
    $.get('/student-services/dashboard-stats')
        .done(function(response) {
            if (response.success) {
                const stats = response.data;
                $('#total-services').text(stats.total_services || 0);
                $('#students-with-services').text(stats.students_with_services || 0);
                $('#pending-services').text(stats.services_due_soon || 0);
                $('#total-revenue').text('$' + (stats.projected_revenue || 0).toLocaleString());
            }
        });
}

function loadServicesTable() {
    $.get('/student-services/services-overview')
        .done(function(response) {
            if (response.success) {
                displayServicesTable(response.data);
            }
        });
}

function displayServicesTable(services) {
    let html = '';
    services.forEach(service => {
        const statusClass = service.active_subscriptions > 0 ? 'success' : 'secondary';
        html += `
            <tr onclick="viewServiceDetails(${service.id})">
                <td>
                    <strong>${service.name}</strong>
                    <br><small class="text-muted">${service.code}</small>
                </td>
                <td>
                    <span class="badge badge-info">${service.category}</span>
                    ${service.is_mandatory ? '<span class="badge badge-warning ms-1">Mandatory</span>' : ''}
                </td>
                <td>${service.active_subscriptions}</td>
                <td>$${service.total_revenue.toLocaleString()}</td>
                <td><span class="badge badge-${statusClass}">${service.active_subscriptions > 0 ? 'Active' : 'Inactive'}</span></td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:viewServiceDetails(${service.id})">View Details</a></li>
                            <li><a class="dropdown-item" href="javascript:manageSubscriptions(${service.id})">Manage Subscriptions</a></li>
                            <li><a class="dropdown-item" href="javascript:applyServiceDiscount(${service.id})">Apply Discount</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `;
    });
    $('#services-table-body').html(html);
}

function loadRecentActivities() {
    $.get('/student-services/recent-activities')
        .done(function(response) {
            if (response.success) {
                displayRecentActivities(response.data);
            }
        });
}

function displayRecentActivities(activities) {
    let html = '';
    activities.forEach(activity => {
        const typeClass = activity.type === 'subscription' ? 'success' : activity.type === 'discount' ? 'warning' : 'info';
        html += `
            <div class="activity-item ${typeClass}">
                <small class="text-muted float-end">${activity.time}</small>
                <div class="fw-bold">${activity.title}</div>
                <small class="text-muted">${activity.description}</small>
            </div>
        `;
    });
    $('#recent-activities').html(html);
}

function loadAcademicLevelStats() {
    $.get('/student-services/academic-level-stats')
        .done(function(response) {
            if (response.success) {
                displayAcademicLevelStats(response.data);
            }
        });
}

function displayAcademicLevelStats(stats) {
    let html = '';
    const levels = ['kg', 'primary', 'secondary', 'high_school'];
    
    levels.forEach(level => {
        const levelData = stats[level] || {students: 0, services: 0, revenue: 0};
        html += `
            <div class="col-md-3">
                <div class="card service-stats-card">
                    <div class="card-body text-center">
                        <h5 class="text-primary">${level.toUpperCase()}</h5>
                        <div class="row">
                            <div class="col-4">
                                <h6>${levelData.students}</h6>
                                <small class="text-muted">Students</small>
                            </div>
                            <div class="col-4">
                                <h6>${levelData.services}</h6>
                                <small class="text-muted">Services</small>
                            </div>
                            <div class="col-4">
                                <h6>$${levelData.revenue}</h6>
                                <small class="text-muted">Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    $('#academic-level-stats').html(html);
}

function initializeBulkSubscriptionModal() {
    $('#bulkSubscriptionModal').on('shown.bs.modal', function() {
        // Load classes and services for bulk subscription
        $.get('/academic/classes')
            .done(function(response) {
                let options = '';
                response.forEach(cls => {
                    options += `<option value="${cls.id}">${cls.name}</option>`;
                });
                $('#bulk-classes').html(options);
            });
            
        $.get('/fees/types')
            .done(function(response) {
                let options = '';
                response.forEach(service => {
                    options += `<option value="${service.id}">${service.name} ($${service.amount})</option>`;
                });
                $('#bulk-services').html(options);
            });
    });
}

// Quick action functions
function showStudentSearch() {
    $('#studentSearchModal').modal('show');
}

function showBulkDiscount() {
    // Implementation for bulk discount modal
    alert('Bulk discount functionality coming soon!');
}

function showOverdueServices() {
    // Implementation for overdue services view
    alert('Overdue services view coming soon!');
}

function exportServiceReport() {
    window.location.href = '/student-services/export-report';
}

// Service management functions
function viewServiceDetails(serviceId) {
    window.location.href = `/fees/types/${serviceId}/details`;
}

function manageSubscriptions(serviceId) {
    window.location.href = `/student-services/service/${serviceId}/manage`;
}

function applyServiceDiscount(serviceId) {
    // Implementation for applying discount to specific service
    alert('Service discount functionality coming soon!');
}

function filterServicesTable(filter) {
    // Implementation for filtering services table
    console.log('Filtering by:', filter);
}

function previewBulkSubscription() {
    const formData = $('#bulk-subscription-form').serialize();
    
    $.post('/student-services/bulk-preview', formData)
        .done(function(response) {
            if (response.success) {
                alert(`Preview: ${response.data.total_students} students will be subscribed to ${response.data.total_services} services.`);
                $('#bulk-subscribe-btn').show();
            }
        });
}

function executeBulkSubscription() {
    const formData = $('#bulk-subscription-form').serialize();
    
    if (!confirm('Are you sure you want to proceed with bulk subscription?')) {
        return;
    }
    
    $.post('/student-services/bulk-subscribe', formData)
        .done(function(response) {
            if (response.success) {
                alert('Bulk subscription completed successfully!');
                $('#bulkSubscriptionModal').modal('hide');
                loadDashboardStats();
                loadServicesTable();
            }
        });
}

function searchStudentServices() {
    const searchTerm = $('#student-search-input').val();
    const classFilter = $('#class-filter').val();
    const serviceFilter = $('#service-filter').val();
    
    if (!searchTerm && !classFilter && !serviceFilter) {
        alert('Please enter search criteria');
        return;
    }
    
    $.get('/student-services/search', {
        search: searchTerm,
        class: classFilter,
        service: serviceFilter
    })
    .done(function(response) {
        if (response.success) {
            displayStudentSearchResults(response.data);
        }
    });
}

function displayStudentSearchResults(results) {
    let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Student</th><th>Class</th><th>Services</th><th>Total Amount</th><th>Actions</th></tr></thead><tbody>';
    
    results.forEach(student => {
        html += `
            <tr>
                <td>${student.name}<br><small class="text-muted">${student.admission_no}</small></td>
                <td>${student.class}</td>
                <td>${student.services_count}</td>
                <td>$${student.total_amount}</td>
                <td><button class="btn btn-sm btn-outline-primary" onclick="viewStudentServices(${student.id})">View Services</button></td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    $('#student-search-results').html(html);
}

function viewStudentServices(studentId) {
    window.location.href = `/student/${studentId}#services`;
}
</script>
@endpush