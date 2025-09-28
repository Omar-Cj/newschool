<style>
    /* Modal Header Styling */
    .modal-header-image {
        background: linear-gradient(135deg, var(--primary-color, #007bff), var(--info-color, #17a2b8));
        color: white;
        border-bottom: none;
    }

    .modal-header-image .modal-title {
        color: white !important;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .modal-header-image .btn-close {
        color: white !important;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .modal-header-image .btn-close:hover {
        opacity: 1;
    }

    .modal-header-image .btn-close i {
        color: white !important;
        font-size: 1.2rem;
    }

    /* Parent Information Card */
    .card.bg-light {
        border: 1px solid #e9ecef;
        background-color: #f8f9fa !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .parent-details {
        font-weight: 600;
        color: #495057;
    }

    .parent-detail-item {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .parent-detail-item:last-child {
        margin-bottom: 0;
    }

    .balance-display {
        text-align: right;
    }

    .balance-display .h5 {
        color: var(--success-color, #28a745);
        font-weight: 700;
    }

    /* Form Styling */
    .form-control.ot-input {
        height: 48px;
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 400;
        line-height: 1.5;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control.ot-input:focus {
        border-color: var(--primary-color, #007bff);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .form-control.ot-input:hover {
        border-color: #adb5bd;
    }

    .form-label {
        font-weight: 500;
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 8px;
        color: var(--ot-text-title, #212529);
    }

    .form-text {
        font-size: 12px;
        color: var(--ot-text-subtitle, #6c757d);
        margin-top: 4px;
    }

    /* Quick Date Range Buttons */
    .quick-date-ranges {
        gap: 0.5rem;
    }

    .quick-date-range {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        background-color: white;
        color: var(--primary-color, #007bff);
    }

    .quick-date-range:hover {
        border-color: var(--primary-color, #007bff);
        background-color: var(--primary-color, #007bff);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .quick-date-range.active {
        background-color: var(--primary-color, #007bff);
        border-color: var(--primary-color, #007bff);
        color: white;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    /* Action Buttons */
    .btn {
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    /* Balance Summary Cards */
    .card.border-success {
        border-color: #28a745 !important;
    }

    .card.border-info {
        border-color: #17a2b8 !important;
    }

    .card.border-success .card-body {
        background: linear-gradient(135deg, #f8fff9, #e8f5e8);
    }

    .card.border-info .card-body {
        background: linear-gradient(135deg, #f0f9ff, #e1f5fe);
    }

    /* Information Alert */
    .alert-info {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        border: none;
        border-radius: 8px;
    }

    .alert-info h6 {
        color: #0c5460;
        font-weight: 600;
    }

    /* Modal Footer */
    .modal-footer {
        border-top: 1px solid #e9ecef;
        background-color: #f8f9fa;
    }

    .modal-footer .btn {
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .modal-footer .btn-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .modal-footer .btn-secondary:hover {
        color: white;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    /* Loading States */
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }

    .loading-spinner .spinner-border {
        color: var(--primary-color, #007bff);
    }

    /* Error and Success Messages */
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .success-message {
        color: #28a745;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Modal Animation */
    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* Custom Scrollbar */
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 1rem;
        }

        .quick-date-ranges {
            flex-direction: column;
        }

        .quick-date-range {
            width: 100%;
            justify-content: center;
        }

        .parent-details {
            font-size: 0.8rem;
        }

        .balance-display {
            text-align: left;
            margin-top: 1rem;
        }
    }

    /* Focus States */
    .form-control.ot-input:focus,
    .quick-date-range:focus {
        outline: none;
    }

    /* Disabled States */
    .form-control.ot-input:disabled {
        background-color: #f8f9fa;
        border-color: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
        opacity: 0.65;
    }
</style>