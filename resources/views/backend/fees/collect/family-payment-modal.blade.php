<!-- Family Payment Modal -->
<div class="modal fade" id="familyPaymentModal" tabindex="-1" aria-labelledby="familyPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header modal-header-image">
                <h5 class="modal-title" id="familyPaymentModalLabel">
                    <i class="fas fa-users me-2"></i>
                    {{ ___('fees.Family Payment') }} - <span id="family-student-name">{{ ___('common.student') }}</span>
                </h5>
                <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
                        data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>

            <form id="siblingPaymentForm" method="POST">
                @csrf
                <input type="hidden" name="payment_mode" id="sibling_payment_mode" value="direct">
                <input type="hidden" name="primary_student_id" id="primary_student_id">

                <div class="modal-body px-4 py-4">
                    <!-- Loading State -->
                    <div id="sibling-loading" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-3">{{ ___('fees.Loading sibling data') }}...</div>
                    </div>

                    <!-- No Siblings Message -->
                    <div id="no-siblings-message" class="text-center py-5" style="display: none;">
                        <div class="mb-3">
                            <i class="fas fa-info-circle fa-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">{{ ___('fees.No siblings with outstanding fees') }}</h5>
                        <p class="text-muted">{{ ___('fees.This student has no siblings with outstanding fees. Use individual payment instead.') }}</p>
                    </div>

                    <!-- Family Payment Interface -->
                    <div id="sibling-payment-interface" style="display: none;">
                        <!-- Family Summary -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                                        <h6 class="mb-0">
                                            <i class="fas fa-family me-2"></i>
                                            {{ ___('fees.Family Fee Summary') }}
                                        </h6>
                                        <div class="parent-info">
                                            <small class="text-muted">
                                                <i class="fas fa-user-tie me-1"></i>
                                                <span id="parent-name">{{ ___('common.parent') }}</span>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="text-muted small">{{ ___('fees.Total Outstanding') }}</div>
                                                <div class="fw-bold h5 mb-0" id="family-total-outstanding">$0.00</div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-muted small">{{ ___('fees.Available Deposit') }}</div>
                                                <div class="fw-bold h5 mb-0 text-success" id="family-available-deposit">$0.00</div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-muted small">{{ ___('fees.Total Payment') }}</div>
                                                <div class="fw-bold h5 mb-0 text-primary" id="family-total-payment">$0.00</div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-muted small">{{ ___('fees.Remaining Balance') }}</div>
                                                <div class="fw-bold h5 mb-0" id="family-remaining-balance">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Mode Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="fas fa-credit-card me-2"></i>
                                            {{ ___('fees.Payment Mode') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="radio" name="payment_mode_radio"
                                                           id="direct_payment_mode" value="direct" checked>
                                                    <label class="form-check-label fw-semibold" for="direct_payment_mode">
                                                        <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                                        {{ ___('fees.Direct Payment') }}
                                                    </label>
                                                    <div class="text-muted small mt-1">
                                                        {{ ___('fees.Pay with cash, Zaad, or Edahab') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-lg">
                                                    <input class="form-check-input" type="radio" name="payment_mode_radio"
                                                           id="deposit_payment_mode" value="deposit">
                                                    <label class="form-check-label fw-semibold" for="deposit_payment_mode">
                                                        <i class="fas fa-piggy-bank me-2 text-info"></i>
                                                        {{ ___('fees.Deposit Payment') }}
                                                    </label>
                                                    <div class="text-muted small mt-1">
                                                        {{ ___('fees.Deduct from available deposit balance') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Deposit Information Alert -->
                                        <div class="alert alert-info mt-3" id="deposit-info-alert" style="display: none;">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <div>
                                                    <strong>{{ ___('fees.Deposit Balance') }}:</strong>
                                                    <span id="deposit-balance-amount">$0.00</span>
                                                    <span id="deposit-balance-status" class="ms-2"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Distribution Options -->
                        <div class="row mb-4" id="distribution-options">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-calculator me-2"></i>
                                            {{ ___('fees.Payment Distribution') }}
                                        </h6>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" id="equal-distribution-btn">
                                                {{ ___('fees.Equal') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" id="proportional-distribution-btn">
                                                {{ ___('fees.Proportional') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" id="clear-distribution-btn">
                                                {{ ___('fees.Clear') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sibling List Table -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            {{ ___('fees.Siblings with Outstanding Fees') }}
                                            <span class="badge bg-primary ms-2" id="siblings-count-badge">0</span>
                                        </h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="sibling-fees-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">{{ ___('fees.Student') }}</th>
                                                        <th class="text-center">{{ ___('fees.Class') }}</th>
                                                        <th class="text-end">{{ ___('fees.Outstanding Amount') }}</th>
                                                        <th class="text-end">{{ ___('fees.Payment Amount') }}</th>
                                                        <th class="text-center">{{ ___('fees.Actions') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="sibling-fees-tbody">
                                                    <!-- Sibling rows will be populated by JavaScript -->
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr class="fw-bold">
                                                        <td colspan="2" class="ps-3">{{ ___('fees.Totals') }}</td>
                                                        <td class="text-end" id="total-outstanding-amount">$0.00</td>
                                                        <td class="text-end" id="total-payment-amount">$0.00</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Configuration -->
                        <div class="row mb-4">
                            <!-- Payment Method (shown only for direct payment mode) -->
                            <div class="col-md-6" id="payment-method-config" style="display: none;">
                                <label for="sibling_payment_method" class="form-label">
                                    {{ ___('fees.payment_method') }}
                                    <span class="fillable">*</span>
                                </label>
                                <select class="form-control select2" name="payment_method" id="sibling_payment_method">
                                    <option value="" disabled selected>{{ ___('fees.select_payment_method') }}</option>
                                    <option value="cash">{{ ___('fees.cash') }}</option>
                                    <option value="zaad">{{ ___('fees.zaad') }}</option>
                                    <option value="edahab">{{ ___('fees.edahab') }}</option>
                                </select>
                            </div>

                            <!-- Journal Selection (always visible) -->
                            <div class="col-md-6" id="journal-config">
                                <label for="sibling_journal_id" class="form-label">
                                    {{ ___('fees.Journal') }} <span class="fillable">*</span>
                                </label>
                                <select class="form-control select2" name="journal_id" id="sibling_journal_id" required>
                                    <option value="">{{ ___('fees.select_journal') }}</option>
                                    <!-- Will be populated by JavaScript -->
                                </select>
                            </div>
                        </div>

                        <!-- Payment Date and Notes -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="sibling_payment_date" class="form-label">
                                    {{ ___('fees.Payment Date') }} <span class="fillable">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" name="payment_date"
                                       id="sibling_payment_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="sibling_payment_notes" class="form-label">{{ ___('fees.Payment Notes') }}</label>
                                <textarea class="form-control ot-input" name="payment_notes" id="sibling_payment_notes"
                                          rows="2" placeholder="{{ ___('fees.enter_payment_notes') }}"></textarea>
                            </div>
                        </div>

                        <!-- Payment Summary Card -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-success text-white">
                                    <div class="card-body p-3">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Total Payment') }}</div>
                                                <strong id="summary-total-payment">$0.00</strong>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.From Deposit') }}</div>
                                                <strong id="summary-deposit-used">$0.00</strong>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Cash Required') }}</div>
                                                <strong id="summary-cash-required">$0.00</strong>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Students') }}</div>
                                                <strong id="summary-students-count">0</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" id="sibling-payment-footer" style="display: none;">
                    <button type="button" class="btn btn-outline-secondary py-2 px-4" data-bs-dismiss="modal">
                        {{ ___('ui_element.cancel') }}
                    </button>
                    <button type="button" class="btn btn-outline-primary py-2 px-4" id="validate_sibling_payment_btn">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ ___('fees.Validate Payment') }}
                    </button>
                    <button type="submit" class="btn ot-btn-primary" id="process_sibling_payment_btn" disabled>
                        <i class="fas fa-credit-card me-2"></i>
                        {{ ___('fees.Process Family Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sibling Fee Detail Modal -->
<div class="modal fade" id="siblingFeeDetailModal" tabindex="-1" aria-labelledby="siblingFeeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="siblingFeeDetailModalLabel">
                    <i class="fas fa-list me-2"></i>
                    {{ ___('fees.Fee Details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="siblingFeeDetailBody">
                <!-- Fee details will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ ___('ui_element.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for Family Payment Modal -->
<style>
.form-check-lg .form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    margin-top: 0.125rem;
}

.form-check-lg .form-check-label {
    font-size: 1rem;
    padding-left: 0.5rem;
}

#sibling-fees-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.payment-amount-input {
    border: 2px solid #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
}

.payment-amount-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.payment-amount-input.is-invalid {
    border-color: #dc3545;
}

.payment-amount-input.is-valid {
    border-color: #198754;
}

.badge-counter {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Ensure proper modal stacking for modal-in-modal */
#familyPaymentModal {
    z-index: 1060;
}

#familyPaymentModal .modal-backdrop {
    z-index: 1059;
}

#siblingFeeDetailModal {
    z-index: 1070;
}
</style>