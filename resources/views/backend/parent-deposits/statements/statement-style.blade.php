<style>
    /* Statement Header Card */
    .statement-header-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .statement-header-card .card-header {
        background: linear-gradient(135deg, var(--primary-color, #007bff), var(--info-color, #17a2b8));
        color: white;
        border: none;
        padding: 1.5rem;
    }

    .statement-header-card .card-header h5 {
        color: white !important;
        font-weight: 600;
    }

    .statement-period .badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }

    /* Parent Details Grid */
    .parent-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }

    .parent-detail-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color, #007bff);
        transition: all 0.3s ease;
    }

    .parent-detail-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .detail-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .detail-icon i {
        font-size: 1.2rem;
    }

    .detail-content {
        flex: 1;
    }

    .detail-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
    }

    /* Statement Meta */
    .statement-meta {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 8px;
        height: 100%;
    }

    .meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.5rem 0;
    }

    .meta-item:last-child {
        margin-bottom: 0;
    }

    .meta-item i {
        width: 20px;
        font-size: 1.1rem;
    }

    .meta-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-right: 0.5rem;
    }

    .meta-value {
        font-weight: 600;
        color: #212529;
    }

    /* Balance Summary Cards */
    .balance-summary-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .balance-summary-card .card-header {
        background: linear-gradient(135deg, var(--success-color, #28a745), var(--info-color, #17a2b8));
        color: white;
        border: none;
        padding: 1.5rem;
    }

    .balance-summary-card .card-header h5 {
        color: white !important;
        font-weight: 600;
    }

    .balance-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .balance-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .balance-available {
        border-left: 4px solid var(--success-color, #28a745);
    }

    .balance-reserved {
        border-left: 4px solid var(--warning-color, #ffc107);
    }

    .balance-deposits {
        border-left: 4px solid var(--info-color, #17a2b8);
    }

    .balance-withdrawals {
        border-left: 4px solid var(--danger-color, #dc3545);
    }

    .balance-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.5rem;
    }

    .balance-available .balance-icon {
        background: rgba(40, 167, 69, 0.1);
        color: var(--success-color, #28a745);
    }

    .balance-reserved .balance-icon {
        background: rgba(255, 193, 7, 0.1);
        color: var(--warning-color, #ffc107);
    }

    .balance-deposits .balance-icon {
        background: rgba(23, 162, 184, 0.1);
        color: var(--info-color, #17a2b8);
    }

    .balance-withdrawals .balance-icon {
        background: rgba(220, 53, 69, 0.1);
        color: var(--danger-color, #dc3545);
    }

    .balance-content {
        flex: 1;
    }

    .balance-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .balance-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
    }

    .balance-available .balance-amount {
        color: var(--success-color, #28a745);
    }

    .balance-reserved .balance-amount {
        color: var(--warning-color, #ffc107);
    }

    .balance-deposits .balance-amount {
        color: var(--info-color, #17a2b8);
    }

    .balance-withdrawals .balance-amount {
        color: var(--danger-color, #dc3545);
    }

    /* Period Statistics Card */
    .period-stats-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
        height: 100%;
    }

    .period-stats-card .card-header {
        background: linear-gradient(135deg, var(--warning-color, #ffc107), var(--secondary-color, #6c757d));
        color: white;
        border: none;
        padding: 1.5rem;
    }

    .period-stats-card .card-header h5 {
        color: white !important;
        font-weight: 600;
    }

    .stats-table {
        margin: 0;
    }

    .stats-table tr {
        border-bottom: 1px solid #e9ecef;
    }

    .stats-table tr:last-child {
        border-bottom: none;
        font-weight: 600;
        background: #f8f9fa;
    }

    .stats-table td {
        padding: 0.75rem 0;
        vertical-align: middle;
    }

    .stats-table .text-success {
        color: var(--success-color, #28a745) !important;
    }

    .stats-table .text-danger {
        color: var(--danger-color, #dc3545) !important;
    }

    .stats-table .text-info {
        color: var(--info-color, #17a2b8) !important;
    }

    .stats-table .text-primary {
        color: var(--primary-color, #007bff) !important;
    }

    /* Transaction History */
    .transaction-history-card {
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        overflow: hidden;
    }

    .transaction-history-card .card-header {
        background: linear-gradient(135deg, var(--dark-color, #343a40), var(--secondary-color, #6c757d));
        color: white;
        border: none;
        padding: 1.5rem;
    }

    .transaction-history-card .card-header h5 {
        color: white !important;
        font-weight: 600;
    }

    .transaction-badge {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
    }

    .transaction-table {
        margin: 0;
    }

    .transaction-table thead th {
        background: #f8f9fa;
        border: none;
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .transaction-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
    }

    .transaction-table tbody tr:hover {
        background: #f8f9fa;
    }

    .transaction-amount {
        font-weight: 600;
        font-size: 1.1rem;
    }

    .transaction-amount.positive {
        color: var(--success-color, #28a745);
    }

    .transaction-amount.negative {
        color: var(--danger-color, #dc3545);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #6c757d;
        font-size: 1.1rem;
    }

    /* Print Styles */
    @media print {
        .btn-group,
        .page-header .col-sm-6:last-child {
            display: none !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .balance-card:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        .parent-detail-item:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .parent-details-grid {
            grid-template-columns: 1fr;
        }

        .parent-detail-item {
            padding: 0.75rem;
        }

        .detail-icon {
            width: 35px;
            height: 35px;
            margin-right: 0.75rem;
        }

        .balance-card {
            padding: 1rem;
            flex-direction: column;
            text-align: center;
        }

        .balance-icon {
            margin-right: 0;
            margin-bottom: 0.75rem;
        }

        .balance-amount {
            font-size: 1.25rem;
        }

        .transaction-table {
            font-size: 0.9rem;
        }

        .transaction-table thead th,
        .transaction-table tbody td {
            padding: 0.75rem 0.5rem;
        }
    }

    /* Animation for loading states */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .balance-card,
    .parent-detail-item {
        animation: fadeInUp 0.6s ease-out;
    }

    .balance-card:nth-child(1) { animation-delay: 0.1s; }
    .balance-card:nth-child(2) { animation-delay: 0.2s; }
    .balance-card:nth-child(3) { animation-delay: 0.3s; }
    .balance-card:nth-child(4) { animation-delay: 0.4s; }

    /* Transaction Table Enhancements */
    .transaction-date .date-main {
        font-weight: 600;
        color: #212529;
    }

    .transaction-date .date-time {
        font-size: 0.8rem;
    }

    .reference-code {
        background: #f8f9fa;
        color: #495057;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-family: 'Courier New', monospace;
    }

    .transaction-description {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .student-name {
        font-weight: 500;
        color: #495057;
    }

    .balance-after {
        font-weight: 600;
        color: #495057;
    }

    /* Enhanced Button Group */
    .btn-group .btn {
        border-radius: 6px;
        margin-right: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
</style>
