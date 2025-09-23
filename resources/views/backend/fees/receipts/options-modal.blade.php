{{-- Receipt Options Modal --}}
<div class="modal fade" id="receiptOptionsModal" tabindex="-1" aria-labelledby="receiptOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title" id="receiptOptionsModalLabel">
                    <i class="fa-solid fa-check-circle me-2"></i>{{ ___('fees.payment_successful') }}
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Success Message --}}
                <div class="alert alert-success d-flex align-items-center mb-4">
                    <i class="fa-solid fa-check-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">{{ ___('fees.payment_processed_successfully') }}</h5>
                        <p class="mb-0">{{ ___('fees.payment_confirmation_message') }}</p>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fa-solid fa-receipt me-2"></i>{{ ___('fees.payment_summary') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ ___('student_info.student_name') }}:</strong><br>
                                   {{ $receipt->student->first_name }} {{ $receipt->student->last_name }}</p>
                                <p><strong>{{ ___('student_info.admission_no') }}:</strong><br>
                                   {{ $receipt->student->admission_no }}</p>
                                <p><strong>{{ ___('fees.payment_status') }}:</strong><br>
                                   <span class="badge {{ $receipt->payment_status === 'partial' ? 'bg-warning' : 'bg-success' }}">
                                       {{ $receipt->payment_status === 'partial' ? ___('fees.partial_payment') : ___('fees.full_payment') }}
                                   </span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ ___('fees.receipt_no') }}:</strong><br>
                                   {{ $receipt->receipt_number }}</p>
                                <p><strong>{{ ___('fees.payment_date') }}:</strong><br>
                                   {{ dateFormat($receipt->payment_date) }}</p>
                                <p><strong>{{ ___('fees.amount_paid') }}:</strong><br>
                                   <span class="h5 text-success">{{ Setting('currency_symbol') }} {{ number_format($receipt->amount_paid, 2) }}</span></p>
                                <p class="mb-0"><strong>{{ ___('fees.payment_method') }}:</strong><br>
                                   {{ $receipt->payment_method }}</p>
                                @if($receipt->transaction_reference)
                                    <p class="mb-0"><strong>{{ ___('fees.transaction_reference') }}:</strong><br>
                                       <small class="text-muted">{{ $receipt->transaction_reference }}</small></p>
                                @endif
                            </div>
                        </div>

                        {{-- Payment Allocation Details --}}
                        @if(count($receipt->fees_affected) > 0)
                            <hr>
                            <h6 class="mb-3"><i class="fa-solid fa-list-check me-2"></i>{{ ___('fees.payment_allocation') ?? 'Payment Allocation' }}</h6>
                            <div class="row">
                                @foreach($receipt->fees_affected as $fee)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                            <div>
                                                <div class="fw-semibold">{{ $fee['name'] ?? 'Fee Payment' }}</div>
                                                @if(isset($fee['remaining_balance']))
                                                    <small class="text-muted">
                                                        @if($fee['remaining_balance'] > 0)
                                                            Remaining: {{ Setting('currency_symbol') }}{{ number_format($fee['remaining_balance'], 2) }}
                                                        @else
                                                            Fully Paid
                                                        @endif
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="text-success fw-bold">
                                                {{ Setting('currency_symbol') }}{{ number_format($fee['amount'] ?? 0, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Receipt Options --}}
                <div class="receipt-options">
                    <h5 class="mb-3"><i class="fa-solid fa-download me-2"></i>{{ ___('fees.receipt_options') }}</h5>
                    
                    <div class="row g-3">
                        {{-- Individual Receipt --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-file-pdf fa-3x text-info"></i>
                                    </div>
                                    <h6 class="card-title">{{ ___('fees.individual_receipt') }}</h6>
                                    <p class="card-text small">{{ ___('fees.individual_receipt_description') }}</p>
                                    <a href="{{ route('fees.receipt.individual', $receipt->id) }}"
                                       class="btn btn-info btn-sm w-100" target="_blank">
                                        <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_receipt') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Student Summary Receipt --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-file-lines fa-3x text-warning"></i>
                                    </div>
                                    <h6 class="card-title">{{ ___('fees.student_summary') }}</h6>
                                    <p class="card-text small">{{ ___('fees.student_summary_description') }}</p>
                                    <a href="{{ route('fees.receipt.student-summary', $receipt->student->id) }}"
                                       class="btn btn-warning btn-sm w-100" target="_blank">
                                        <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_summary') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Options --}}
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6><i class="fa-solid fa-lightbulb me-2"></i>{{ ___('fees.additional_options') }}</h6>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                            onclick="printReceipt({{ $receipt->id }})">
                                        <i class="fa-solid fa-print me-2"></i>{{ ___('fees.print_receipt') }}
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100"
                                            onclick="emailReceipt({{ $receipt->id }})">
                                        <i class="fa-solid fa-envelope me-2"></i>{{ ___('fees.email_receipt') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Group Receipt Option (if multiple payments today) --}}
                    <div class="mt-3" id="group-receipt-option" style="display: none;">
                        <div class="alert alert-warning">
                            <h6><i class="fa-solid fa-users me-2"></i>{{ ___('fees.group_receipt_available') }}</h6>
                            <p class="small mb-2">{{ ___('fees.group_receipt_description') }}</p>
                            <button type="button" class="btn btn-warning btn-sm" onclick="generateGroupReceipt()">
                                <i class="fa-solid fa-download me-2"></i>{{ ___('fees.generate_group_receipt') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>{{ ___('common.close') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="collectAnotherPayment()">
                    <i class="fa-solid fa-plus me-2"></i>{{ ___('fees.collect_another_payment') }}
                </button>
            </div>
        </div>
    </div>
</div>
