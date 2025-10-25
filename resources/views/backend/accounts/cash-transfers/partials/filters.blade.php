{{-- Filters Section --}}
<div class="card mt-20">
    <div class="card-header">
        <h5 class="mb-0">{{ ___('cash_transfer.filters') }}</h5>
    </div>
    <div class="card-body">
        <form id="filters-form">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="filter-journal">{{ ___('cash_transfer.journal') }}</label>
                        <select class="form-control ot-input select2" id="filter-journal" name="journal_id">
                            <option value="">{{ ___('common.select') }}</option>
                            {{-- Journals will be loaded via AJAX --}}
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filter-status">{{ ___('cash_transfer.status') }}</label>
                        <select class="form-control ot-input" id="filter-status" name="status">
                            <option value="">{{ ___('cash_transfer.all_status') }}</option>
                            <option value="pending">{{ ___('cash_transfer.pending') }}</option>
                            <option value="approved">{{ ___('cash_transfer.approved') }}</option>
                            <option value="rejected">{{ ___('cash_transfer.rejected') }}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filter-date-from">{{ ___('cash_transfer.date_from') }}</label>
                        <input type="date" class="form-control ot-input" id="filter-date-from" name="date_from">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="filter-date-to">{{ ___('cash_transfer.date_to') }}</label>
                        <input type="date" class="form-control ot-input" id="filter-date-to" name="date_to">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn ot-btn-primary flex-fill">
                                <i class="fa-solid fa-filter"></i> {{ ___('cash_transfer.filter') }}
                            </button>
                            <button type="button" class="btn ot-btn-secondary flex-fill" id="reset-filters">
                                <i class="fa-solid fa-rotate-right"></i> {{ ___('cash_transfer.reset') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
