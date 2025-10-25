{{-- View Transfer Details Modal --}}
<div class="modal fade" id="viewTransferModal" tabindex="-1" aria-labelledby="viewTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTransferModalLabel">{{ ___('cash_transfer.transfer_details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="transfer-details-content">
                <div class="text-center py-5">
                    <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                    <p class="mt-3">{{ ___('cash_transfer.loading') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn ot-btn-secondary" data-bs-dismiss="modal">{{ ___('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Template for transfer details (will be populated by JavaScript) --}}
<template id="transfer-details-template">
    <div class="row">
        <div class="col-md-6">
            <table class="table table-borderless">
                <tr>
                    <th>{{ ___('cash_transfer.journal') }}:</th>
                    <td id="detail-journal"></td>
                </tr>
                <tr>
                    <th>{{ ___('cash_transfer.transferred_amount') }}:</th>
                    <td id="detail-amount"></td>
                </tr>
                <tr>
                    <th>{{ ___('cash_transfer.status') }}:</th>
                    <td id="detail-status"></td>
                </tr>
                <tr>
                    <th>{{ ___('cash_transfer.transferred_by') }}:</th>
                    <td id="detail-transferred-by"></td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-borderless">
                <tr>
                    <th>{{ ___('cash_transfer.date_transferred') }}:</th>
                    <td id="detail-date-transferred"></td>
                </tr>
                <tr>
                    <th>{{ ___('cash_transfer.approved_by') }}:</th>
                    <td id="detail-approved-by"></td>
                </tr>
                <tr>
                    <th>{{ ___('cash_transfer.date_approved') }}:</th>
                    <td id="detail-date-approved"></td>
                </tr>
                <tr id="rejection-reason-row" style="display: none;">
                    <th>{{ ___('cash_transfer.rejection_reason') }}:</th>
                    <td id="detail-rejection-reason"></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mt-3" id="notes-section">
        <div class="col-12">
            <h6>{{ ___('cash_transfer.notes') }}:</h6>
            <p id="detail-notes" class="text-muted"></p>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <h6>{{ ___('cash_transfer.payment_method_breakdown') }}:</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ ___('cash_transfer.payment_method') }}</th>
                            <th class="text-end">{{ ___('account.amount') }} ({{ Setting('currency_symbol') }})</th>
                        </tr>
                    </thead>
                    <tbody id="payment-methods-table">
                        {{-- Will be populated by JavaScript --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
