@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    <div class="page-content">
        {{-- Breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">{{ ___('account.Accounts') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cash-transfers.index') }}">{{ ___('cash_transfer.cash_transfers') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- Breadcrumb Area End --}}

        {{-- Statistics Preview Cards --}}
        <div class="row mt-20" id="create-statistics-cards">
            <div class="col-xl-3 col-md-6 col-12">
                <div class="card ot_crm_summeryBox2">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-fill">
                            <p class="ot_crm_summeryBox2-subtitle">{{ ___('cash_transfer.receipt_cash') }}</p>
                            <h3 class="ot_crm_summeryBox2-title" id="preview-receipt-cash">-</h3>
                        </div>
                        <div class="ot_crm_summeryBox2-icon bg-primary-light">
                            <i class="las la-money-bill-wave text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card ot_crm_summeryBox2">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-fill">
                            <p class="ot_crm_summeryBox2-subtitle">{{ ___('cash_transfer.previous_transfer') }}</p>
                            <h3 class="ot_crm_summeryBox2-title" id="preview-previous-transfer">-</h3>
                        </div>
                        <div class="ot_crm_summeryBox2-icon bg-success-light">
                            <i class="las la-exchange-alt text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card ot_crm_summeryBox2">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-fill">
                            <p class="ot_crm_summeryBox2-subtitle">{{ ___('cash_transfer.deposit') }}</p>
                            <h3 class="ot_crm_summeryBox2-title" id="preview-deposit">-</h3>
                        </div>
                        <div class="ot_crm_summeryBox2-icon bg-warning-light">
                            <i class="las la-wallet text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-12">
                <div class="card ot_crm_summeryBox2">
                    <div class="card-body d-flex align-items-center">
                        <div class="flex-fill">
                            <p class="ot_crm_summeryBox2-subtitle">{{ ___('cash_transfer.remaining_balance') }}</p>
                            <h3 class="ot_crm_summeryBox2-title" id="preview-remaining-balance">-</h3>
                        </div>
                        <div class="ot_crm_summeryBox2-icon bg-info-light">
                            <i class="las la-coins text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Form --}}
        <div class="card mt-20">
            <div class="card-header">
                <h5 class="mb-0">{{ $data['title'] }}</h5>
            </div>
            <div class="card-body">
                <form id="create-transfer-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="journal_id">{{ ___('cash_transfer.journal') }} <span class="text-danger">*</span></label>
                                <select class="form-control ot-input select2" id="journal_id" name="journal_id" required>
                                    <option value="">{{ ___('cash_transfer.select_journal') }}</option>
                                    {{-- Journals will be loaded via AJAX --}}
                                </select>
                                <div class="invalid-feedback">{{ ___('validation.required', ['attribute' => ___('cash_transfer.journal')]) }}</div>
                            </div>

                            <div class="form-group mt-3">
                                <label>{{ ___('cash_transfer.remaining_balance') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ Setting('currency_symbol') }}</span>
                                    <input type="text" class="form-control ot-input" id="display-remaining-balance" readonly value="-">
                                </div>
                                <small class="text-muted">{{ ___('cash_transfer.select_journal_first') }}</small>
                            </div>

                            <div class="form-group mt-3">
                                <label for="amount">{{ ___('cash_transfer.transfer_amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ Setting('currency_symbol') }}</span>
                                    <input type="number" class="form-control ot-input" id="amount" name="amount"
                                           required min="0" step="0.01" placeholder="{{ ___('cash_transfer.enter_amount') }}">
                                </div>
                                <div class="invalid-feedback">{{ ___('validation.required', ['attribute' => ___('cash_transfer.transfer_amount')]) }}</div>
                                <div class="text-danger mt-1" id="amount-error" style="display: none;"></div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="notes">{{ ___('cash_transfer.notes') }}</label>
                                <textarea class="form-control ot-input" id="notes" name="notes" rows="8" placeholder="{{ ___('cash_transfer.enter_notes') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Bar Section --}}
                    <div class="row mt-4" id="progress-bar-section" style="display: none;">
                        <div class="col-12">
                            <label>{{ ___('cash_transfer.progress') }}</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar" id="journal-progress-bar" role="progressbar"
                                     style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <span id="progress-text">0%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <button type="submit" class="btn ot-btn-primary" id="submit-btn">
                                <i class="fa-solid fa-save"></i> {{ ___('common.save') }}
                            </button>
                            <a href="{{ route('cash-transfers.index') }}" class="btn ot-btn-secondary">
                                <i class="fa-solid fa-times"></i> {{ ___('common.cancel') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // CRITICAL: Define configuration BEFORE loading cash-transfer-create.js
        window.cashTransferCreateConfig = {
            journalsApiUrl: '{{ url('/journals-data') }}',
            createApiUrl: '{{ route('cash-transfers.store') }}',
            indexUrl: '{{ route('cash-transfers.index') }}',
            currencySymbol: '{{ Setting('currency_symbol') }}',
            translations: {
                success: '{{ ___('cash_transfer.transfer_created') }}',
                error: '{{ ___('alert.something_went_wrong') }}',
                amountExceedsBalance: '{{ ___('cash_transfer.amount_exceeds_balance') }}',
                selectJournalFirst: '{{ ___('cash_transfer.select_journal_first') }}',
                loading: '{{ ___('cash_transfer.loading') }}'
            }
        };

        // Log configuration for debugging
        console.log('✅ [CREATE-CONFIG] Configuration Loaded:', window.cashTransferCreateConfig);
    </script>

    <!-- Now load cash-transfer-create.js AFTER configuration is defined -->
    <script src="{{ asset('backend/js/cash-transfer-create.js') }}?v={{ time() }}"></script>
@endpush
