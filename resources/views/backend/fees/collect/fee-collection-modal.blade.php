@include('backend.fees.collect.fee-collection-modal-style')

<div class="modal-content" id="feeCollectionModalWidth">
    <div class="modal-header modal-header-image">
        <h5 class="modal-title" id="feeCollectionModalLabel">
            {{ ___('fees.Fee Collection') }}
        </h5>
        <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
                data-bs-dismiss="modal" aria-label="Close">
            <i class="fa fa-times text-white" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Family Payment Interface -->
    <div class="modal-body px-0 pt-3 pb-0">

            <form id="siblingPaymentForm" method="POST" action="{{ route('fees.siblings.process') }}">
                @csrf
                <input type="hidden" name="payment_mode" id="sibling_payment_mode" value="direct">
                <input type="hidden" name="primary_student_id" id="primary_student_id">
                <input type="hidden" name="student_id" id="modal_student_id">
                <input type="hidden" name="fees_source" value="family_payment">

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
                                                    <!-- Will be populated by JavaScript -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Configuration -->
                        <div class="row">
                            <!-- Payment Method -->
                            <div class="col-md-6 mb-3" id="family-payment-method-config">
                                <label for="family_payment_method" class="form-label">
                                    {{ ___('fees.payment_method') }}
                                    <span class="fillable">*</span>
                                </label>
                                <select class="form-control select2" name="family_payment_method" id="family_payment_method" required>
                                    <option value="" disabled selected>{{ ___('fees.select_payment_method') }}</option>
                                    <option value="cash">{{ ___('fees.cash') }}</option>
                                    <option value="zaad">{{ ___('fees.zaad') }}</option>
                                    <option value="edahab">{{ ___('fees.edahab') }}</option>
                                </select>
                                <div class="form-text">{{ ___('fees.choose_payment_method_hint') }}</div>
                            </div>


                            <!-- Journal Selection -->
                            <div class="col-md-6 mb-3" id="family-journal-config">
                                <label for="family_journal_id" class="form-label">
                                    {{ ___('fees.Journal') }} <span class="fillable">*</span>
                                </label>
                                <select class="form-control select2" name="family_journal_id" id="family_journal_id" required>
                                    <option value="">{{ ___('fees.select_journal') }}</option>
                                    <!-- Will be populated by JavaScript -->
                                </select>
                            </div>

                            <!-- Discount Type -->
                            <div class="col-md-6 mb-3">
                                <label for="family_discount_type" class="form-label">{{ ___('fees.Discount Type') }}</label>
                                <select class="form-control select2" name="family_discount_type" id="family_discount_type">
                                    <option value="">{{ ___('fees.No Discount') }}</option>
                                    <option value="fixed">{{ ___('fees.Fixed Amount') }}</option>
                                    <option value="percentage">{{ ___('fees.Percentage') }}</option>
                                </select>
                            </div>

                            <!-- Discount Amount -->
                            <div class="col-md-6 mb-3">
                                <label for="family_discount_amount" class="form-label">{{ ___('fees.Discount Value') }}</label>
                                <input type="number" class="form-control ot-input" name="family_discount_amount"
                                       id="family_discount_amount" step="0.01" min="0" disabled>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6 mb-3">
                                <label for="family_payment_date" class="form-label">
                                    {{ ___('fees.Payment Date') }} <span class="fillable">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" name="family_payment_date"
                                       id="family_payment_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Payment Notes -->
                            <div class="col-12 mb-3">
                                <label for="family_payment_notes" class="form-label">{{ ___('fees.Payment Notes') }}</label>
                                <textarea class="form-control ot-input" name="family_payment_notes" id="family_payment_notes"
                                          rows="3" placeholder="{{ ___('fees.enter_payment_notes') }}"></textarea>
                            </div>
                        </div>

                        <!-- Family Payment Summary -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-body p-3">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Total Payment') }}</div>
                                                <strong id="family-total-payment">{{ Setting('currency_symbol') }}0.00</strong>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Discount') }}</div>
                                                <strong id="family-discount-amount">{{ Setting('currency_symbol') }}0.00</strong>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Net Amount') }}</div>
                                                <strong id="family-net-amount">{{ Setting('currency_symbol') }}0.00</strong>
                                            </div>
                                            <div class="col-3">
                                                <div class="mb-1">{{ ___('fees.Students') }}</div>
                                                <strong id="family-students-count">0</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Payment Interface (Fallback) -->
                    <div id="individual-payment-interface" style="display: none;">
                        <!-- Individual Payment Form Fields -->
                        <div class="row">
                            <!-- Payment Amount -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_amount" class="form-label">
                                    {{ ___('fees.Payment Amount') }} ({{ Setting('currency_symbol') }})
                                    <span class="fillable">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control ot-input" name="payment_amount"
                                           id="payment_amount" step="0.01" min="0" required>
                                    <button type="button" class="btn btn-outline-secondary" id="pay_full_amount">
                                        {{ ___('fees.Pay Full') }}
                                    </button>
                                </div>
                                <small class="text-muted">{{ ___('fees.partial_payment_allowed') }}</small>
                            </div>

                            <!-- Payment Method -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_method" class="form-label">
                                    {{ ___('fees.payment_method') }}
                                    <span class="fillable">*</span>
                                </label>
                                <select class="form-control select2" name="payment_method" id="payment_method" required>
                                    <option value="" disabled selected>{{ ___('fees.select_payment_method') }}</option>
                                    <option value="cash">{{ ___('fees.cash') }}</option>
                                    <option value="zaad">{{ ___('fees.zaad') }}</option>
                                    <option value="edahab">{{ ___('fees.edahab') }}</option>
                                </select>
                                <div class="form-text">{{ ___('fees.choose_payment_method_hint') }}</div>
                            </div>

                            <!-- Transaction Reference (for digital payments) -->
                            <div class="col-12 mb-3" id="transaction_reference_field" style="display: none;">
                                <label for="transaction_reference" class="form-label">
                                    {{ ___('fees.Transaction Reference') }} <span class="fillable">*</span>
                                </label>
                                <input type="text" class="form-control ot-input" name="transaction_reference"
                                       id="transaction_reference" placeholder="{{ ___('fees.enter_transaction_reference') }}">
                            </div>

                            <!-- Journal Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="journal_id" class="form-label">
                                    {{ ___('fees.Journal') }} <span class="fillable">*</span>
                                </label>
                                <select class="form-control select2" name="journal_id" id="journal_id" required>
                                    <option value="">{{ ___('fees.select_journal') }}</option>
                                    <!-- Will be populated by JavaScript -->
                                </select>
                            </div>

                            <!-- Discount -->
                            <div class="col-md-6 mb-3">
                                <label for="discount_type" class="form-label">{{ ___('fees.Discount Type') }}</label>
                                <select class="form-control select2" name="discount_type" id="discount_type">
                                    <option value="">{{ ___('fees.No Discount') }}</option>
                                    <option value="fixed">{{ ___('fees.Fixed Amount') }}</option>
                                    <option value="percentage">{{ ___('fees.Percentage') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="discount_amount" class="form-label">{{ ___('fees.Discount Value') }}</label>
                                <input type="number" class="form-control ot-input" name="discount_amount"
                                       id="discount_amount" step="0.01" min="0" disabled>
                            </div>

                            <!-- Payment Date -->
                            <div class="col-md-6 mb-3">
                                <label for="payment_date" class="form-label">
                                    {{ ___('fees.Payment Date') }} <span class="fillable">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" name="payment_date"
                                       id="payment_date" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <!-- Payment Notes -->
                            <div class="col-12 mb-3">
                                <label for="payment_notes" class="form-label">{{ ___('fees.Payment Notes') }}</label>
                                <textarea class="form-control ot-input" name="payment_notes" id="payment_notes"
                                          rows="3" placeholder="{{ ___('fees.enter_payment_notes') }}"></textarea>
                            </div>
                        </div>

                        <!-- Real-time Calculation Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-primary text-white">
                                    <div class="card-body p-3">
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="mb-1">{{ ___('fees.Payment Amount') }}</div>
                                                <strong id="display_payment_amount">{{ Setting('currency_symbol') }}0.00</strong>
                                            </div>
                                            <div class="col-4">
                                                <div class="mb-1">{{ ___('fees.Discount') }}</div>
                                                <strong id="display_discount_amount">{{ Setting('currency_symbol') }}0.00</strong>
                                            </div>
                                            <div class="col-4">
                                                <div class="mb-1">{{ ___('fees.Net Amount') }}</div>
                                                <strong id="display_net_amount">{{ Setting('currency_symbol') }}0.00</strong>
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

                    <div class="modal-footer" id="individual-payment-footer" style="display: none;">
                        <button type="button" class="btn btn-outline-secondary py-2 px-4" data-bs-dismiss="modal">
                            {{ ___('ui_element.cancel') }}
                        </button>
                        <button type="submit" class="btn ot-btn-primary" id="process_payment_btn">
                            <i class="fas fa-credit-card me-2"></i>
                            {{ ___('fees.Process Payment') }}
                        </button>
                    </div>
            </form>
        </div>
</div>

<!-- Fee Details Modal -->
<div class="modal fade" id="siblingFeeDetailModal" tabindex="-1" aria-labelledby="siblingFeeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="siblingFeeDetailModalLabel">
                    <i class="fas fa-eye me-2"></i>
                    Fee Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="siblingFeeDetailBody">
                <!-- Fee details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@include('backend.fees.collect.fee-collection-modal-script')