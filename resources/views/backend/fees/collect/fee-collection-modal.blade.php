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

    <form id="feeCollectionForm" method="POST" action="{{ route('fees-collect.store') }}">
        @csrf
        <input type="hidden" name="student_id" id="modal_student_id">
        <input type="hidden" name="fees_assign_childrens" id="modal_fees_assign_childrens">

        <div class="modal-body p-4">
            <!-- Selected Fees Summary -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ ___('fees.Selected Fees Summary') }}</h6>
                            <div class="student-info d-flex align-items-center">
                                <i class="fas fa-user-graduate me-2"></i>
                                <span id="summary-student-name">{{ ___('common.student') }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="outstanding-overview mb-3">
                                <small class="text-muted d-block">{{ ___('fees.Total Outstanding') }}</small>
                                <div class="h5 mb-0 fw-bold" id="summary-outstanding-amount">
                                    {{ Setting('currency_symbol') }}0.00
                                </div>
                            </div>
                            <div id="selected-fees-summary">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-6">
                                    <strong>{{ ___('fees.Total Amount') }}: </strong>
                                    <span id="total-amount">{{ Setting('currency_symbol') }}0.00</span>
                                </div>
                                <div class="col-6 text-end">
                                    <strong>{{ ___('fees.Payable Amount') }}: </strong>
                                    <span id="payable-amount">{{ Setting('currency_symbol') }}0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Configuration -->
            <div class="row">
                <!-- Payment Amount -->
                <div class="col-12 mb-3">
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
                <div class="col-12 mb-3">
                    <label class="form-label">{{ ___('fees.payment_method') }} <span class="fillable">*</span></label>
                    <div class="input-check-radio academic-section">
                        <div class="radio-inputs">
                            <label>
                                <input class="radio-input" type="radio" name="payment_method" value="cash" checked>
                                <span class="radio-tile">
                                    <span class="radio-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </span>
                                    <span class="radio-label">{{ ___('fees.Cash') }}</span>
                                </span>
                            </label>
                            <label>
                                <input class="radio-input" type="radio" name="payment_method" value="zaad">
                                <span class="radio-tile">
                                    <span class="radio-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </span>
                                    <span class="radio-label">{{ ___('fees.Zaad') }}</span>
                                </span>
                            </label>
                            <label>
                                <input class="radio-input" type="radio" name="payment_method" value="edahab">
                                <span class="radio-tile">
                                    <span class="radio-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </span>
                                    <span class="radio-label">{{ ___('fees.Edahab') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
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
                <div class="col-12 mb-3">
                    <label for="journal_id" class="form-label">
                        {{ ___('fees.Journal') }} <span class="fillable">*</span>
                    </label>
                    <select class="form-control ot-input" name="journal_id" id="journal_id" required>
                        <option value="">{{ ___('fees.select_journal') }}</option>
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>

                <!-- Discount -->
                <div class="col-md-6 mb-3">
                    <label for="discount_type" class="form-label">{{ ___('fees.Discount Type') }}</label>
                    <select class="form-control ot-input" name="discount_type" id="discount_type">
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
                <div class="col-12 mb-3">
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

        <div class="modal-footer">
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

@include('backend.fees.collect.fee-collection-modal-script')
