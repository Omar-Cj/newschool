{{-- Approve Transfer Modal --}}
<div class="modal fade" id="approveTransferModal" tabindex="-1" aria-labelledby="approveTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveTransferModalLabel">{{ ___('cash_transfer.approve_transfer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ ___('cash_transfer.confirm_approve') }}</p>
                <input type="hidden" id="approve-transfer-id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn ot-btn-secondary" data-bs-dismiss="modal">{{ ___('common.cancel') }}</button>
                <button type="button" class="btn ot-btn-primary" id="confirm-approve-btn">
                    <i class="fa-solid fa-check"></i> {{ ___('cash_transfer.approve') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Reject Transfer Modal --}}
<div class="modal fade" id="rejectTransferModal" tabindex="-1" aria-labelledby="rejectTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectTransferModalLabel">{{ ___('cash_transfer.reject_transfer') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reject-transfer-form">
                <div class="modal-body">
                    <p>{{ ___('cash_transfer.confirm_reject') }}</p>
                    <input type="hidden" id="reject-transfer-id">
                    <div class="form-group mt-3">
                        <label for="rejection-reason">{{ ___('cash_transfer.rejection_reason') }} <span class="text-danger">*</span></label>
                        <textarea class="form-control ot-input" id="rejection-reason" name="reason" rows="3" required placeholder="{{ ___('cash_transfer.enter_rejection_reason') }}"></textarea>
                        <div class="invalid-feedback">{{ ___('validation.required', ['attribute' => ___('cash_transfer.rejection_reason')]) }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn ot-btn-secondary" data-bs-dismiss="modal">{{ ___('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger" id="confirm-reject-btn">
                        <i class="fa-solid fa-times"></i> {{ ___('cash_transfer.reject') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
