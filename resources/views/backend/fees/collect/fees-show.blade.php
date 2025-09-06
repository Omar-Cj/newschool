<div class="modal-content" id="modalWidth">
    <div class="modal-header modal-header-image">
        <h5 class="modal-title" id="modalLabel2">
            {{ ___('fees.fees_collect') }}
        </h5>
        <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
            data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times text-white" aria-hidden="true"></i></button>
    </div>
    <form action="{{ route('fees-collect.store') }}" enctype="multipart/form-data" method="post" id="visitForm">
        @csrf
        <input type="hidden" name="student_id" value="{{$data['student_id']}}" />
    <div class="modal-body p-5">

        <div class="row mb-3">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="exampleDataList" class="form-label ">{{ ___('fees.due_date') }} <span
                                class="fillable">*</span></label>
                        <input class="form-control ot-input @error('date') is-invalid @enderror" name="date"
                            list="datalistOptions" id="exampleDataList" type="date"
                            placeholder="{{ ___('fees.enter_date') }}" value="{{ old('date') }}" required>
                        @error('date')
                            <div id="validationServer04Feedback" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ ___('fees.payment_method') }} <span class="fillable">*</span></label>
                        <div class="input-check-radio academic-section @error('payment_method') is-invalid @enderror">
                        @foreach (\Config::get('site.payment_methods') as $key=>$item)

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" {{$key == 1 ? 'checked':''}} value="{{$key}}" id="flexCheckDefault{{$key}}" />
                                <label class="form-check-label ps-2 pe-5" for="flexCheckDefault{{$key}}">{{ ___($item) }}</label>
                            </div>
                        @endforeach
                    </div>

                </div>

                    @if($data['is_siblings_discount'])
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="">Siblings discount ({{ $data['siblings_discount_name'] }})</label>
{{--                                Siblings discount (first sibling - 10%)--}}
                                <span class="text-success"></span>
                            </div>

                            <div class="col-md-3">
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" class="form-control ot-input me-2" name="discount_amount_value" value="{{ @$data['siblings_discount_percentage']??'' }}">
                                    <button class="btn ot-btn-success" type="button" id="applyDiscount">
                                        {{ ___('common.apply') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif


                    {{--                    <div class="row">--}}
{{--                        <div class="col-md-3 mb-3">--}}
{{--                            <label for="discountType" class="form-label">Discount Type</label>--}}
{{--                            <select class="form-select" id="discountType" name="discount_type" aria-label="Default select example">--}}
{{--                                <option value="" selected disabled>Select Type</option>--}}
{{--                                <option value="percentage">Percentage</option>--}}
{{--                                <option value="fixed">Fixed</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}

{{--                        <div class="col-md-3 mb-3" id="discountAmountGroup">--}}
{{--                            <label for="discountAmount" class="form-label">Discount Amount</label>--}}
{{--                            <input type="number" class="form-control" id="discountAmount" name="discount_amount" placeholder="Enter discount amount">--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    @if(($data['early_payment_discount_percentage'] ?? 0) > 0)
                        <div class="text-center">
{{--                            Early payment discount (super early-10%)  --}}
                        <span class="text-success">{{$data['early_payment_discount_percentage']}}% {{___('fees.discount for '). $data['discount_name'].(' discount applied')}}</span>
                        </div>
                    @endif
                </div>
        </div>
        </div>
        <div class="table-responsive table_height_450 niceScroll">
            <table class="table table-bordered role-table" id="students_table">
                <thead class="thead">
                    <tr>
                        <th class="purchase">{{ ___('fees.group') }}</th>
                        <th class="purchase">{{ ___('fees.type') }}</th>
                        <th class="purchase">{{ ___('fees.due_date') }}</th>
                        <th class="purchase">{{ ___('fees.amount') }} ({{ Setting('currency_symbol') }})</th>
                        <th class="purchase">{{ ___('fees.Discount') }} ({{ Setting('currency_symbol') }})</th>
                        @if(($data['early_payment_discount_percentage'] ?? 0) > 0)
                            <th class="purchase">{{ ucfirst($data['discount_name']) }} ({{ Setting('currency_symbol') }})</th>
                        @endif
                        <th class="purchase">{{ ___('tax.Tax') }} ({{ Setting('currency_symbol') }})</th>
                    </tr>
                </thead>
                <tbody class="tbody">
                    @php
                        $total = 0;
                    @endphp
                    @foreach (@$data['fees_assign_children'] as $item)


                    @if(!($item->fees_collect_count && $item->feesCollect && $item->feesCollect->isPaid()))
                    @php
                        $earlyPaymentDiscount =  calculateDiscount(@$item->feesMaster->amount, $data['early_payment_discount_percentage']?? 0);
                        $siblingsDiscount = calculateDiscount(@$item->feesMaster->amount, $data['siblings_discount_percentage']);
                        $fineAmount = 0;
                        $totalAddition = 0;
                        $totalDeduction = 0;
                        $taxAmount = calculateTax($item->feesMaster->amount);
                        if(date('Y-m-d') > $item->feesMaster->date) {
                            $fineAmount = @$item->feesMaster->fine_amount;
                        }

                        $totalAddition += $fineAmount + $taxAmount;
                        $totalDeduction += $earlyPaymentDiscount + $siblingsDiscount;
                        $total += (@$item->feesMaster->amount + $totalAddition) - $totalDeduction;

                    @endphp
                    <input type="hidden" name="fees_assign_childrens[]" value="{{$item->id}}">
                    <input type="hidden" name="amounts[]" value="{{$item->feesMaster->amount}}">
                    <input type="hidden" name="early_payment_percentage" value="{{$data['early_payment_discount_percentage']?? 0}}">
                    @if(date('Y-m-d') > $item->feesMaster->date)
                        <input type="hidden" name="fine_amounts[]" value="{{$item->feesMaster->fine_amount}}">
                    @else
                        <input type="hidden" name="fine_amounts[]" value="0">
                    @endif
                    <tr
                        data-amount="{{ @$item->feesMaster->amount }}"
                        data-fine="{{ date('Y-m-d') > $item->feesMaster->date ? @$item->feesMaster->fine_amount : 0 }}"
                        data-tax="{{ calculateTax(@$item->feesMaster->amount) }}"
                    >
                        <td>{{ @$item->feesMaster->group->name }}</td>
                        <td>{{ @$item->feesMaster->type->name }}</td>
                        <td>{{ dateFormat(@$item->feesMaster->date) }}</td>
                        <td>
                            {{ @$item->feesMaster->amount }}
                            @if(date('Y-m-d') > $item->feesMaster->date)
                                <span class="text-danger">+ {{ @$item->feesMaster->fine_amount }}</span>
                            @endif
                        </td>
                        <td class="discount-cell">{{$data['discount_amount']}}</td>
                        @if(($data['early_payment_discount_percentage'] ?? 0) > 0)
                            <td>{{ $earlyPaymentDiscount }}</td>
                        @endif
                        <td>{{ calculateTax(@$item->feesMaster->amount) }}</td>
                    </tr>

                    @endif
                    @endforeach

                    <tr>
                        <td></td>
                        <td></td>
                        <td><strong>{{ ___('common.total payable') }}</strong></td>
                        <td id="totalPayable">{{ @$total }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary py-2 px-4"
            data-bs-dismiss="modal">{{ ___('ui_element.cancel') }}</button>
            @if($total != 0)
        <button type="submit" class="btn ot-btn-primary" id="confirm-payment-btn"
            >{{ ___('ui_element.confirm') }}</button>
        <button type="button" class="btn btn-success ms-2" id="simple-payment-btn"
            onclick="submitSimplePayment()">{{ ___('fees.pay_now_alternative') }}</button>
            @endif
    </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#applyDiscount').on('click', function () {
        let userDiscountPercentage = parseFloat($('input[name="discount_amount_value"]').val()) || 0;
        let earlyDiscountPercentage = parseFloat($('input[name="early_payment_percentage"]').val()) || 0;

        let total = 0;

        $('#students_table tbody tr').each(function () {
            let $row = $(this);

            let baseAmount = parseFloat($row.data('amount')) || 0;
            let fineAmount = parseFloat($row.data('fine')) || 0;
            let taxAmount = parseFloat($row.data('tax')) || 0;

            let subtotal = baseAmount + fineAmount;

            let earlyDiscountAmount = (subtotal * earlyDiscountPercentage) / 100;
            let userDiscountAmount = (subtotal * userDiscountPercentage) / 100;

            let finalAmount = subtotal - earlyDiscountAmount - userDiscountAmount;

            let rowTotal = finalAmount + taxAmount;
            total += rowTotal;

            $row.find('.discount-cell').text(userDiscountAmount.toFixed(2));
        });

        $('#totalPayable').text(total.toFixed(2));
    });

    // Enhanced form submission with receipt options
    $('#visitForm').on('submit', function(e) {
        e.preventDefault();
        
        console.log('Form submission started'); // Debug log
        
        // Validate form first
        const requiredFields = $(this).find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
                console.log('Required field missing:', $(this).attr('name'));
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            showAlert('{{ ___("common.please_fill_required_fields") }}', 'error');
            return;
        }
        
        const formData = new FormData(this);
        const submitBtn = $('#confirm-payment-btn');
        
        // Debug form data
        console.log('Form data being sent:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ ___("fees.processing_payment") }}');
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 30000, // 30 second timeout
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            beforeSend: function() {
                console.log('AJAX request starting...');
            },
            success: function(response) {
                console.log('Payment response received:', response); // Debug log
                
                if (response && response.success) {
                    console.log('Payment successful, hiding modal...');
                    
                    // Try multiple ways to hide the modal (Bootstrap compatibility)
                    try {
                        // Bootstrap 5 method
                        const modalElement = document.querySelector('.modal.show');
                        if (modalElement) {
                            const modal = bootstrap.Modal.getInstance(modalElement);
                            if (modal) {
                                modal.hide();
                            } else {
                                // Fallback: hide manually
                                modalElement.style.display = 'none';
                                modalElement.classList.remove('show');
                                document.body.classList.remove('modal-open');
                                const backdrop = document.querySelector('.modal-backdrop');
                                if (backdrop) backdrop.remove();
                            }
                        }
                    } catch (e) {
                        console.log('Bootstrap modal method failed, trying jQuery...');
                        try {
                            // jQuery/Bootstrap 4 method
                            $('.modal').modal('hide');
                        } catch (e2) {
                            console.log('jQuery modal method failed, using manual method...');
                            // Manual hide
                            $('.modal').hide();
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                        }
                    }
                    
                    // Show success message first
                    showAlert('{{ ___("fees.payment_successful") }}', 'success');
                    
                    // Small delay to ensure modal is hidden before showing new one
                    setTimeout(function() {
                        if (response.payment_id) {
                            console.log('Redirecting to receipt options for payment ID:', response.payment_id);
                            // Direct redirect to receipt options page (more reliable)
                            window.location.href = '{{ url("/fees/receipt/options") }}/' + response.payment_id;
                        } else {
                            console.log('No payment ID, reloading page...');
                            // Fallback: just show success and reload
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        }
                    }, 1500);
                } else {
                    console.log('Payment failed:', response);
                    showAlert(response?.message || '{{ ___("fees.payment_failed") }}', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error Details:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
                
                let message = '{{ ___("fees.payment_processing_error") }}';
                
                if (xhr.status === 422) {
                    // Validation error
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        message = Object.values(errors).flat().join(', ');
                    } else {
                        message = xhr.responseJSON?.message || message;
                    }
                } else if (xhr.status === 500) {
                    message = '{{ ___("fees.server_error") }}';
                } else if (xhr.status === 0) {
                    message = '{{ ___("fees.network_error") }}';
                } else if (status === 'timeout') {
                    message = '{{ ___("fees.request_timeout") }}';
                }
                
                showAlert(message, 'error');
            },
            complete: function(xhr, status) {
                console.log('AJAX request completed with status:', status);
                // Only reset button state if we're not redirecting
                if (xhr.status !== 200 || !xhr.responseJSON?.success) {
                    submitBtn.prop('disabled', false).html('{{ ___("ui_element.confirm") }}');
                }
            }
        });
    });

    // Show receipt options modal
    function showReceiptOptionsModal(paymentId, paymentDetails) {
        try {
            console.log('Showing receipt modal for payment:', paymentId, paymentDetails); // Debug log
            
            const modalHtml = `
            <div class="modal fade" id="receiptOptionsModal" tabindex="-1" aria-labelledby="receiptOptionsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h4 class="modal-title">
                                <i class="fa-solid fa-check-circle me-2"></i>{{ ___('fees.payment_successful') }}
                            </h4>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success d-flex align-items-center mb-4">
                                <i class="fa-solid fa-check-circle me-3 fa-2x"></i>
                                <div>
                                    <h5 class="mb-1">{{ ___('fees.payment_processed_successfully') }}</h5>
                                    <p class="mb-0">{{ ___('fees.payment_confirmation_message') }}</p>
                                </div>
                            </div>
                            
                            <div class="card border-primary mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fa-solid fa-receipt me-2"></i>{{ ___('fees.payment_summary') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>{{ ___('student_info.student_name') }}:</strong><br>
                                               ${paymentDetails.student_name}</p>
                                            <p><strong>{{ ___('student_info.admission_no') }}:</strong><br>
                                               ${paymentDetails.admission_no}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>{{ ___('fees.payment_date') }}:</strong><br>
                                               ${paymentDetails.payment_date}</p>
                                            <p><strong>{{ ___('fees.amount_paid') }}:</strong><br>
                                               <span class="h5 text-success">{{ Setting('currency_symbol') }} ${paymentDetails.amount}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="receipt-options">
                                <h5 class="mb-3"><i class="fa-solid fa-download me-2"></i>{{ ___('fees.receipt_options') }}</h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card h-100 border-info">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fa-solid fa-file-pdf fa-3x text-info"></i>
                                                </div>
                                                <h6 class="card-title">{{ ___('fees.individual_receipt') }}</h6>
                                                <p class="card-text small">{{ ___('fees.individual_receipt_description') }}</p>
                                                <a href="${getReceiptUrl('individual', paymentId)}" 
                                                   class="btn btn-info btn-sm w-100" target="_blank">
                                                    <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_receipt') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card h-100 border-warning">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fa-solid fa-file-lines fa-3x text-warning"></i>
                                                </div>
                                                <h6 class="card-title">{{ ___('fees.student_summary') }}</h6>
                                                <p class="card-text small">{{ ___('fees.student_summary_description') }}</p>
                                                <a href="${getReceiptUrl('student-summary', paymentDetails.student_id)}" 
                                                   class="btn btn-warning btn-sm w-100" target="_blank">
                                                    <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_summary') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="alert alert-info">
                                        <h6><i class="fa-solid fa-lightbulb me-2"></i>{{ ___('fees.additional_options') }}</h6>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                                        onclick="printReceipt(${paymentId})">
                                                    <i class="fa-solid fa-print me-2"></i>{{ ___('fees.print_receipt') }}
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-outline-success btn-sm w-100" 
                                                        onclick="emailReceipt(${paymentId})">
                                                    <i class="fa-solid fa-envelope me-2"></i>{{ ___('fees.email_receipt') }}
                                                </button>
                                            </div>
                                        </div>
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
        `;
        
        // Remove existing modal if any
        $('#receiptOptionsModal').remove();
        
        // Add modal to body and show
        $('body').append(modalHtml);
        $('#receiptOptionsModal').modal('show');
        
        } catch (error) {
            console.error('Error showing receipt modal:', error);
            showAlert('{{ ___("fees.receipt_modal_error") }}', 'error');
        }
    }

    // Helper functions
    function getReceiptUrl(type, id) {
        const baseUrl = '{{ url("/") }}';
        return `${baseUrl}/fees/receipt/${type}/${id}`;
    }

    function printReceipt(paymentId) {
        const printWindow = window.open(getReceiptUrl('individual', paymentId), '_blank', 'width=800,height=600');
        printWindow.onload = function() {
            printWindow.print();
        };
    }

    function emailReceipt(paymentId) {
        showAlert('{{ ___("fees.email_feature_coming_soon") }}', 'info');
    }

    function collectAnotherPayment() {
        $('#receiptOptionsModal').modal('hide');
        window.location.reload();
    }

    // Simple payment submission without AJAX (fallback)
    function submitSimplePayment() {
        console.log('Using simple payment submission...');
        
        // Validate form first
        const requiredFields = $('#visitForm').find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            showAlert('Please fill all required fields', 'error');
            return;
        }
        
        // Disable button and show loading
        $('#simple-payment-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');
        
        // Add a hidden field to indicate simple payment
        $('#visitForm').append('<input type="hidden" name="simple_payment" value="1">');
        
        // Submit form normally (non-AJAX)
        document.getElementById('visitForm').submit();
    }

    function showAlert(message, type) {
        // Create a more user-friendly alert
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                <strong>${type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Info!'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

</script>

