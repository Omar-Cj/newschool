<!-- Approve Payment Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fa-solid fa-check-circle"></i> {{ ___('mainapp_common.Approve Payment') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i>
                        {{ ___('mainapp_common.Approving this payment will extend the subscription and activate the school account.') }}
                    </div>

                    <div class="mb-3">
                        <strong>{{ ___('mainapp_schools.School') }}:</strong> <span id="approveSchoolName"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ ___('mainapp_common.Amount') }}:</strong> <span id="approveAmount"></span>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmApproveCheck" required>
                        <label class="form-check-label" for="confirmApproveCheck">
                            {{ ___('mainapp_common.I confirm this payment has been verified and should be approved') }}
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ ___('mainapp_common.Cancel') }}
                    </button>
                    <button type="button" class="btn btn-success" id="confirmApprove">
                        <i class="fa-solid fa-check"></i> {{ ___('mainapp_common.Approve Payment') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
