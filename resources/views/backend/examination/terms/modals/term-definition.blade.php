<!-- Term Definition Modal -->
<div class="modal fade" id="definitionModal" tabindex="-1" aria-labelledby="definitionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-image">
                <h5 class="modal-title" id="definitionModalLabel">
                    <i class="fa-solid fa-cog me-2"></i>{{ ___('examination.term_template') }}
                </h5>
                <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
                        data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-white" aria-hidden="true"></i>
                </button>
            </div>
            <form id="definitionForm">
                @csrf
                <input type="hidden" id="definition_id" name="definition_id">
                <div class="modal-body p-4">
                    <!-- Name and Code -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">
                                    {{ ___('common.name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control ot-input" id="name" name="name"
                                       required placeholder="{{ ___('examination.term_name_placeholder') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code" class="form-label">
                                    {{ ___('examination.code') }}
                                    <small class="text-muted">{{ ___('common.optional') }}</small>
                                </label>
                                <input type="text" class="form-control ot-input" id="code" name="code"
                                       placeholder="{{ ___('examination.term_code_placeholder') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Sequence, Duration, and Start Month -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sequence" class="form-label">
                                    {{ ___('examination.sequence_order') }} <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control ot-input" id="sequence" name="sequence"
                                       min="1" required placeholder="1">
                                <small class="form-text text-muted">
                                    {{ ___('examination.order_help_text') }}
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="typical_duration_weeks" class="form-label">
                                    {{ ___('examination.duration_weeks') }} <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control ot-input" id="typical_duration_weeks"
                                       name="typical_duration_weeks" min="1" max="52" required placeholder="12">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="typical_start_month" class="form-label">
                                    {{ ___('examination.typical_start_month') }}
                                </label>
                                <select class="form-control ot-input select2" id="typical_start_month" name="typical_start_month">
                                    <option value="">{{ ___('examination.select_month') }}</option>
                                    <option value="1">{{ ___('common.january') }}</option>
                                    <option value="2">{{ ___('common.february') }}</option>
                                    <option value="3">{{ ___('common.march') }}</option>
                                    <option value="4">{{ ___('common.april') }}</option>
                                    <option value="5">{{ ___('common.may') }}</option>
                                    <option value="6">{{ ___('common.june') }}</option>
                                    <option value="7">{{ ___('common.july') }}</option>
                                    <option value="8">{{ ___('common.august') }}</option>
                                    <option value="9">{{ ___('common.september') }}</option>
                                    <option value="10">{{ ___('common.october') }}</option>
                                    <option value="11">{{ ___('common.november') }}</option>
                                    <option value="12">{{ ___('common.december') }}</option>
                                </select>
                                <small class="form-text text-muted">
                                    {{ ___('examination.used_for_suggestions') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description" class="form-label">
                                    {{ ___('common.description') }}
                                    <small class="text-muted">{{ ___('common.optional') }}</small>
                                </label>
                                <textarea class="form-control ot-input" id="description" name="description" rows="3"
                                          placeholder="{{ ___('examination.term_description_placeholder') }}"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Active Status -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">
                                        {{ ___('common.active') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-lg ot-btn-secondary" data-bs-dismiss="modal">
                        <span><i class="fa-solid fa-times"></i></span>
                        <span>{{ ___('common.cancel') }}</span>
                    </button>
                    <button type="submit" class="btn btn-lg ot-btn-primary" id="saveDefinitionBtn">
                        <span><i class="fa-solid fa-save"></i></span>
                        <span>{{ ___('common.save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>