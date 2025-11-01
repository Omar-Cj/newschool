@extends('parent-panel.partials.master')

@section('title')
{{ ___('common.Fees list') }}
@endsection

@section('content')
<div class="page-content">

    {{-- Simplified Children Fees Summary Table --}}
    @if (!empty(@$data['children_fees_summary']))
        <div class="table-content table-basic mb-24">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('fees.children_fees_summary') ?? 'Children Fees Summary' }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered class-table">
                            <thead class="thead">
                                <tr>
                                    <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                    <th class="purchase">{{ ___('common.class') }} & {{ ___('common.section') }}</th>
                                    <th class="purchase text-center">{{ ___('fees.outstanding_amount') }}</th>
                                    <th class="purchase text-center">{{ ___('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @foreach (@$data['children_fees_summary'] as $summary)
                                    <tr>
                                        <td>
                                            <strong>{{ $summary['student_name'] }}</strong>
                                        </td>
                                        <td>{{ $summary['class_section'] }}</td>
                                        <td class="text-center">
                                            @if ($summary['outstanding_amount'] > 0)
                                                <span class="text-danger fw-bold">
                                                    {{ Setting('currency_symbol') }} {{ number_format($summary['outstanding_amount'], 2) }}
                                                </span>
                                            @else
                                                <span class="text-success small">
                                                    {{ ___('fees.no_dues') ?? 'No Dues' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($summary['outstanding_amount'] > 0)
                                                <button
                                                    class="btn btn-sm ot-btn-primary"
                                                    onclick="handlePayment({{ $summary['student_id'] }}, {{ $summary['outstanding_amount'] }})"
                                                    data-student-id="{{ $summary['student_id'] }}"
                                                    data-amount="{{ $summary['outstanding_amount'] }}">
                                                    <i class="fa fa-credit-card"></i> {{ ___('fees.pay') ?? 'Pay' }}
                                                </button>
                                            @else
                                                <span class="text-muted small">{{ ___('fees.paid') ?? 'Paid' }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Total Outstanding Row (only if multiple children) --}}
                                @if (count(@$data['children_fees_summary']) > 1)
                                    <tr class="table-info fw-bold">
                                        <td colspan="2" class="text-end">
                                            <strong>{{ ___('fees.total_outstanding') ?? 'Total Outstanding' }}:</strong>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $totalOutstanding = collect($data['children_fees_summary'])->sum('outstanding_amount');
                                            @endphp
                                            @if ($totalOutstanding > 0)
                                                <span class="text-danger fs-5">
                                                    {{ Setting('currency_symbol') }} {{ number_format($totalOutstanding, 2) }}
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    {{ ___('fees.all_clear') ?? 'All Clear' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection




@push('script')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        /**
         * Payment handler - to be implemented with payment API
         * @param {number} studentId - Student ID
         * @param {number} amount - Outstanding amount to pay
         */
        function handlePayment(studentId, amount) {
            // TODO: Implement payment API integration
            console.log('Payment initiated for student:', studentId, 'Amount:', amount);

            // Professional SweetAlert notification
            Swal.fire({
                icon: 'info',
                title: '{{ ___("fees.payment_coming_soon") ?? "Payment Integration Coming Soon" }}',
                html: `
                    <div style="text-align: left; padding: 10px;">
                        <p><strong>{{ ___("student_info.student_id") ?? "Student ID" }}:</strong> ${studentId}</p>
                        <p><strong>{{ ___("fees.outstanding_amount") ?? "Outstanding Amount" }}:</strong> <span style="color: #dc3545; font-weight: bold;">{{ Setting('currency_symbol') }}${amount.toFixed(2)}</span></p>
                        <hr style="margin: 15px 0;">
                        <p style="color: #6c757d; font-size: 14px;">
                            <i class="fa fa-info-circle"></i> {{ ___("fees.payment_gateway_note") ?? "This button will be connected to a secure payment gateway in the next update. You'll be able to pay via credit card, debit card, or online banking." }}
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '{{ ___("common.ok") ?? "OK" }}',
                cancelButtonText: '{{ ___("common.cancel") ?? "Cancel" }}',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'swal-wide'
                }
            });

            // Future implementation will:
            // 1. Open payment modal/gateway (Stripe, PayPal, etc.)
            // 2. Process payment via API endpoint
            // 3. Update fees_collects table with payment record
            // 4. Refresh the summary table to show updated outstanding amounts
            // 5. Send confirmation email/notification to parent

            /* Example future implementation:

            Swal.fire({
                title: '{{ ___("fees.processing_payment") ?? "Processing Payment" }}',
                text: '{{ ___("common.please_wait") ?? "Please wait..." }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/parent-panel/fees/initiate-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    student_id: studentId,
                    amount: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    // Redirect to payment gateway or open payment modal
                    window.location.href = data.payment_url;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ ___("common.error") ?? "Error" }}',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Payment error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '{{ ___("common.error") ?? "Error" }}',
                    text: '{{ ___("fees.payment_error") ?? "An error occurred while processing payment" }}'
                });
            });

            */
        }
    </script>
@endpush
