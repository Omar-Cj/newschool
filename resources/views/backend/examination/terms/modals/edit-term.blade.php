<!-- Edit Term Modal -->
<div class="modal fade" id="editTermModal" tabindex="-1" aria-labelledby="editTermModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-image">
                <h5 class="modal-title" id="editTermModalLabel">
                    <i class="fa-solid fa-edit me-2"></i>{{ ___('examination.edit_term') }}
                </h5>
                <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
                        data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>
            <form id="editTermForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_term_id" name="term_id">
                <div class="modal-body p-4">
                    <!-- Term Information (Read-only) -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ ___('examination.term') }}</label>
                                <input type="text" class="form-control ot-input" id="edit_term_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ ___('examination.session') }}</label>
                                <input type="text" class="form-control ot-input" id="edit_session_name" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_start_date" class="form-label">
                                    {{ ___('examination.start_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" id="edit_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_end_date" class="form-label">
                                    {{ ___('examination.end_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" id="edit_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="edit_notes" class="form-label">
                                    {{ ___('examination.notes') }}
                                    <small class="text-muted">{{ ___('common.optional') }}</small>
                                </label>
                                <textarea class="form-control ot-input" id="edit_notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-lg ot-btn-secondary" data-bs-dismiss="modal">
                        <span><i class="fa-solid fa-times"></i></span>
                        <span>{{ ___('common.cancel') }}</span>
                    </button>
                    <button type="submit" class="btn btn-lg ot-btn-primary" id="updateTermBtn">
                        <span><i class="fa-solid fa-save"></i></span>
                        <span>{{ ___('examination.update_term') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>