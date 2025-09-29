<style>
    /* Minimal Statement Page Styling - Using System Components */

    /* Print Styles */
    @media print {
        .btn-group,
        .page-header .col-sm-6:last-child {
            display: none !important;
        }

        .ot-card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
    }

    /* Transaction amount color coding */
    .transaction-amount.positive {
        color: var(--bs-success) !important;
        font-weight: 600;
    }

    .transaction-amount.negative {
        color: var(--bs-danger) !important;
        font-weight: 600;
    }

    /* Transaction reference code styling */
    .reference-code {
        background: #f8f9fa;
        color: #495057;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.8rem;
        font-family: 'Courier New', monospace;
    }

    /* Empty state styling */
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

    /* Responsive design improvements */
    @media (max-width: 768px) {
        .ot_crm_summeryBox2 {
            margin-bottom: 1rem;
        }

        .table-responsive {
            font-size: 0.9rem;
        }
    }
</style>