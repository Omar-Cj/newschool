<!-- Reject Payment Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fa-solid fa-times-circle"></i> {{ ___('mainapp_common.Reject Payment') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        {{ ___('mainapp_common.Rejecting this payment will NOT extend the subscription. Please provide a clear reason.') }}
                    </div>

                    <div class="mb-3">
                        <strong>{{ ___('mainapp_schools.School') }}:</strong> <span id="rejectSchoolName"></span>
                    </div>

                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">
                            {{ ___('mainapp_common.Rejection Reason') }} <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                  placeholder="Please provide a clear reason for rejecting this payment (minimum 10 characters)"
                                  required minlength="10" maxlength="1000"></textarea>
                        <div class="form-text">
                            {{ ___('mainapp_common.Minimum 10 characters required') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ ___('mainapp_common.Cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmReject">
                        <i class="fa-solid fa-times"></i> {{ ___('mainapp_common.Reject Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
