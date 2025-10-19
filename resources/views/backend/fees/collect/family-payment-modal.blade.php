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
                        <!-- Compact Family Summary & Payment Mode -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-3">
                                        <div class="row align-items-center">
                                            <!-- Parent Info -->
                                            <div class="col-md-3">
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-user-tie me-1"></i>{{ ___('fees.Parent') }}
                                                </small>
                                                <div class="fw-semibold" id="parent-name">{{ ___('common.parent') }}</div>
                                            </div>

                                            <!-- Summary Metrics -->
                                            <div class="col-md-5">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">{{ ___('fees.Total Outstanding') }}</small>
                                                        <div class="fw-bold" id="family-total-outstanding">$0.00</div>
                                                    </div>
                                                    <div class="col-6">
                                                        <small class="text-muted d-block">{{ ___('fees.Available Deposit') }}</small>
                                                        <div class="fw-bold text-success" id="family-available-deposit">$0.00</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Payment Mode Toggle -->
                                            <div class="col-md-4">
                                                <small class="text-muted d-block mb-2">{{ ___('fees.Payment Mode') }}</small>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="payment_mode_radio"
                                                           id="direct_payment_mode" value="direct" checked autocomplete="off">
                                                    <label class="btn btn-outline-primary btn-sm" for="direct_payment_mode">
                                                        <i class="fas fa-money-bill-wave me-1"></i>{{ ___('fees.Direct') }}
                                                    </label>

                                                    <input type="radio" class="btn-check" name="payment_mode_radio"
                                                           id="deposit_payment_mode" value="deposit" autocomplete="off">
                                                    <label class="btn btn-outline-primary btn-sm" for="deposit_payment_mode">
                                                        <i class="fas fa-piggy-bank me-1"></i>{{ ___('fees.Deposit') }}
                                                    </label>
                                                </div>
                                                <div id="deposit-info-alert" class="text-danger small mt-1" style="display: none;">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    <span id="deposit-warning-text"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for additional summary display (kept for JS compatibility) -->
                        <div style="display: none;">
                            <span id="family-total-payment">$0.00</span>
                            <span id="family-remaining-balance">$0.00</span>
                            <span id="deposit-balance-amount">$0.00</span>
                            <span id="deposit-balance-status"></span>
                        </div>

                        <!-- Siblings Payment Table with Inline Distribution Buttons -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            {{ ___('fees.Siblings with Outstanding Fees') }}
                                            <span class="badge bg-primary ms-2" id="siblings-count-badge">0</span>
                                        </h6>
                                        <!-- Inline Distribution Buttons -->
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-secondary" id="equal-distribution-btn" title="{{ ___('fees.Equal Distribution') }}">
                                                <i class="fas fa-equals me-1"></i>{{ ___('fees.Equal') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="proportional-distribution-btn" title="{{ ___('fees.Proportional Distribution') }}">
                                                <i class="fas fa-percentage me-1"></i>{{ ___('fees.Proportional') }}
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="clear-distribution-btn" title="{{ ___('fees.Clear All') }}">
                                                <i class="fas fa-eraser me-1"></i>{{ ___('fees.Clear') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0" id="sibling-fees-table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="ps-3">{{ ___('fees.Student') }}</th>
                                                        <th class="text-center">{{ ___('fees.Class') }}</th>
                                                        <th class="text-end">{{ ___('fees.Outstanding') }}</th>
                                                        <th class="text-end">{{ ___('fees.Payment Amount') }}</th>
                                                        <th class="text-center">{{ ___('common.Actions') }}</th>
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

                        <!-- Payment Configuration - 2 Column Grid (Matches Individual Modal) -->
                        <div class="row">
                            <!-- Payment Method (shown only for direct payment mode) -->
                            <div class="col-md-6 mb-3" id="payment-method-config">
                                <label for="sibling_payment_method" class="form-label">
                                    {{ ___('fees.payment_method') }}
                                    <span class="fillable">*</span>
                                </label>
                                <select class="form-control ot-input select2" name="payment_method" id="sibling_payment_method" required>
                                    <option value="" disabled selected>{{ ___('fees.select_payment_method') }}</option>
                                    <option value="cash">{{ ___('fees.cash') }}</option>
                                    <option value="zaad">{{ ___('fees.zaad') }}</option>
                                    <option value="edahab">{{ ___('fees.edahab') }}</option>
                                </select>
                            </div>

                            <!-- Journal Selection (always visible) -->
                            <div class="col-md-6 mb-3" id="journal-config">
                                <label for="sibling_journal_id" class="form-label">
                                    {{ ___('fees.Journal') }} <span class="fillable">*</span>
                                </label>
                                <select class="form-control ot-input select2" name="journal_id" id="sibling_journal_id" required>
                                    <option value="">{{ ___('fees.select_journal') }}</option>
                                    <!-- Will be populated by JavaScript -->
                                </select>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6 mb-3">
                                <label for="sibling_payment_date" class="form-label">
                                    {{ ___('fees.Payment Date') }} <span class="fillable">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" name="payment_date"
                                       id="sibling_payment_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Payment Notes -->
                            <div class="col-md-6 mb-3">
                                <label for="sibling_payment_notes" class="form-label">{{ ___('fees.Payment Notes') }}</label>
                                <textarea class="form-control ot-input" name="payment_notes" id="sibling_payment_notes"
                                          rows="1" placeholder="{{ ___('fees.enter_payment_notes') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simplified Footer with Merged Summary -->
                <div class="modal-footer" id="sibling-payment-footer" style="display: none;">
                    <div class="row w-100 align-items-center">
                        <!-- Payment Summary Metrics -->
                        <div class="col-md-8">
                            <div class="card bg-primary text-white mb-0">
                                <div class="card-body p-2">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <small class="d-block opacity-75">{{ ___('fees.Total Payment') }}</small>
                                            <strong id="summary-total-payment">$0.00</strong>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block opacity-75">{{ ___('fees.From Deposit') }}</small>
                                            <strong id="summary-deposit-used">$0.00</strong>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block opacity-75">{{ ___('fees.Cash Required') }}</small>
                                            <strong id="summary-cash-required">$0.00</strong>
                                        </div>
                                        <div class="col-3">
                                            <small class="d-block opacity-75">{{ ___('fees.Students') }}</small>
                                            <strong id="summary-students-count">0</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn btn-outline-secondary py-2 px-3" data-bs-dismiss="modal">
                                {{ ___('ui_element.cancel') }}
                            </button>
                            <button type="button" class="btn btn-outline-primary py-2 px-4" id="validate_sibling_payment_btn">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ ___('fees.Validate Payment') }}
                            </button>
                            <button type="submit" class="btn ot-btn-primary py-2 px-4" id="process_sibling_payment_btn" disabled>
                                <i class="fas fa-credit-card me-2"></i>
                                {{ ___('fees.Process Payment') }}
                            </button>
                        </div>
                    </div>
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

<!-- Compact Styles for Family Payment Modal -->
<style>
/* Family-specific compact styles */
#familyPaymentModal .card.bg-light {
    border: 1px solid #e9ecef;
}

#familyPaymentModal .btn-group label {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

#familyPaymentModal #sibling-fees-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

#familyPaymentModal .payment-amount-input {
    border: 2px solid #dee2e6;
    border-radius: 0.375rem;
    transition: all 0.2s ease-in-out;
}

#familyPaymentModal .payment-amount-input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

#familyPaymentModal .payment-amount-input.is-invalid {
    border-color: #dc3545;
}

#familyPaymentModal .payment-amount-input.is-valid {
    border-color: #198754;
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

/* Compact table styling */
#sibling-fees-table th,
#sibling-fees-table td {
    padding: 0.75rem;
    vertical-align: middle;
}

#sibling-fees-table thead th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Footer summary card styling */
#sibling-payment-footer .card.bg-primary {
    background: linear-gradient(135deg, var(--primary-color, #007bff), var(--info-color, #17a2b8)) !important;
    border: none;
}

#sibling-payment-footer .card.bg-primary .card-body {
    color: #fff !important;
}

#sibling-payment-footer .opacity-75 {
    opacity: 0.85;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #sibling-payment-footer .row {
        flex-direction: column;
    }

    #sibling-payment-footer .col-md-8,
    #sibling-payment-footer .col-md-4 {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    #sibling-payment-footer .text-end {
        text-align: center !important;
    }
}
</style>

<!-- Receipt Options Modal -->
<div class="modal fade" id="receiptOptionsModal" tabindex="-1" aria-labelledby="receiptOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="receiptOptionsModalLabel">
                    <i class="fas fa-receipt me-2"></i>Payment Receipts
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Receipt content will be dynamically inserted here by JavaScript -->
            </div>
        </div>
    </div>
</div>