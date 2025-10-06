<!-- Open Term Modal -->
<div class="modal fade" id="openTermModal" tabindex="-1" aria-labelledby="openTermModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-image">
                <h5 class="modal-title" id="openTermModalLabel">
                    <i class="fa-solid fa-calendar-plus me-2"></i>{{ ___('examination.open_new_term') }}
                </h5>
                <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
                        data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>
            <form id="openTermForm">
                @csrf
                <div class="modal-body p-4">
                    <!-- Term and Session Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="open_term_definition_id" class="form-label">
                                    {{ ___('examination.select_term_template') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-control ot-input select2" id="open_term_definition_id" name="term_definition_id" required>
                                    <option value="">{{ ___('examination.select_term') }}</option>
                                    @foreach($termDefinitions as $definition)
                                        <option value="{{ $definition->id }}">
                                            {{ $definition->name }} ({{ $definition->typical_duration_weeks }} {{ ___('examination.weeks') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="open_session_id" class="form-label">
                                    {{ ___('examination.academic_session') }} <span class="text-danger">*</span>
                                </label>
                                <select class="form-control ot-input select2" id="open_session_id" name="session_id" required>
                                    <option value="">{{ ___('examination.select_session') }}</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="open_start_date" class="form-label">
                                    {{ ___('examination.start_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" id="open_start_date" name="start_date" required>
                                <small class="form-text text-muted">
                                    {{ ___('examination.suggested_dates_will_appear') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="open_end_date" class="form-label">
                                    {{ ___('examination.end_date') }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control ot-input" id="open_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>

                    <!-- Status Preview -->
                    <div class="row mb-3" id="statusPreviewContainer" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert mb-0" id="statusPreviewAlert" role="alert">
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-info-circle mt-1 me-2"></i>
                                    <div>
                                        <strong>{{ ___('examination.status_preview') }}:</strong>
                                        <span id="statusPreviewText"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Draft Override Option -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="save_as_draft" name="save_as_draft" value="1">
                                <label class="form-check-label" for="save_as_draft">
                                    <i class="fas fa-file-alt me-1"></i>
                                    {{ ___('examination.save_as_draft') }}
                                    <small class="text-muted">({{ ___('examination.save_as_draft_help') }})</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="open_notes" class="form-label">
                                    {{ ___('examination.notes') }}
                                    <small class="text-muted">{{ ___('common.optional') }}</small>
                                </label>
                                <textarea class="form-control ot-input" id="open_notes" name="notes" rows="3"
                                          placeholder="{{ ___('examination.additional_notes_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-lg ot-btn-secondary" data-bs-dismiss="modal">
                        <span><i class="fa-solid fa-times"></i></span>
                        <span>{{ ___('common.cancel') }}</span>
                    </button>
                    <button type="submit" class="btn btn-lg ot-btn-primary" id="saveOpenTermBtn">
                        <span><i class="fa-solid fa-save"></i></span>
                        <span>{{ ___('examination.open_term') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>