{{-- Statistics Cards Section --}}
<div class="row mt-20" id="statistics-cards">
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card ot_crm_summeryBox2">
            <div class="card-body d-flex align-items-center">
                <div class="flex-fill">
                    <p class="ot_crm_summeryBox2-subtitle">{{ ___('cash_transfer.receipt_cash') }}</p>
                    <h3 class="ot_crm_summeryBox2-title" id="stat-receipt-cash">
                        <span class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </h3>
                    <p class="ot_crm_summeryBox2-caption">{{ ___('cash_transfer.total_paid_amount') }}</p>
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
                    <h3 class="ot_crm_summeryBox2-title" id="stat-previous-transfer">
                        <span class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </h3>
                    <p class="ot_crm_summeryBox2-caption">{{ ___('cash_transfer.transferred_amount') }}</p>
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
                    <h3 class="ot_crm_summeryBox2-title" id="stat-deposit">
                        <span class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </h3>
                    <p class="ot_crm_summeryBox2-caption">{{ ___('account.amount') }}</p>
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
                    <p class="ot_crm_summeryBox2-subtitle">{{ ___('cash_transfer.total_amount') }}</p>
                    <h3 class="ot_crm_summeryBox2-title" id="stat-total-amount">
                        <span class="loading-spinner">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </h3>
                    <p class="ot_crm_summeryBox2-caption">{{ ___('account.amount') }}</p>
                </div>
                <div class="ot_crm_summeryBox2-icon bg-info-light">
                    <i class="las la-coins text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>
