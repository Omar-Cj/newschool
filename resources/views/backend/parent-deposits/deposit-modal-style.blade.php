<style>
    /* Modal Header Styling */
    .modal-header-image {
        background: linear-gradient(135deg, var(--primary-color, #007bff), var(--success-color, #28a745));
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

    .parent-info {
        font-weight: 600;
        color: #495057;
    }


    .parent-details .parent-name {
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .parent-details .parent-email,
    .parent-details .parent-phone {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .balance-display {
        text-align: right;
    }

    .balance-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
    }

    .balance-amount {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--success-color, #28a745);
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

    textarea.form-control.ot-input {
        height: auto;
        min-height: 80px;
        resize: vertical;
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

    /* Input Group Styling */
    .input-group .input-group-text {
        background-color: var(--primary-color, #007bff);
        border-color: var(--primary-color, #007bff);
        color: white;
        font-weight: 600;
    }

    .input-group .form-control {
        border-left: 0;
    }

    .input-group .form-control:focus {
        border-color: var(--primary-color, #007bff);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    /* Select Dropdown Styling */
    select.form-control.ot-input {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23495057' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 12px center;
        background-size: 16px 12px;
        padding-right: 40px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
    }

    select.form-control.ot-input:hover {
        border-color: #adb5bd;
        box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.1);
    }

    select.form-control.ot-input:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2380bdff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    }

    /* Quick Amount Buttons */
    .quick-amounts {
        gap: 0.5rem;
    }

    .quick-amount {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid #e9ecef;
        background-color: white;
        color: var(--primary-color, #007bff);
    }

    .quick-amount:hover {
        border-color: var(--primary-color, #007bff);
        background-color: var(--primary-color, #007bff);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }

    .quick-amount.active {
        background-color: var(--primary-color, #007bff);
        border-color: var(--primary-color, #007bff);
        color: white;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    /* Payment Method Information */
    .alert-info {
        background: linear-gradient(135deg, #d1ecf1, #bee5eb);
        border: none;
        border-radius: 8px;
    }

    .payment-method-info {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
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

    .modal-footer .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }

    .modal-footer .btn-outline-secondary:hover {
        color: white;
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .modal-footer .ot-btn-primary {
        background-color: var(--success-color, #28a745);
        border-color: var(--success-color, #28a745);
        color: white;
    }

    .modal-footer .ot-btn-primary:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
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

        .quick-amounts {
            flex-direction: column;
        }

        .quick-amount {
            width: 100%;
            justify-content: center;
        }

        .parent-info {
            font-size: 0.9rem;
        }

        .balance-display {
            text-align: left;
            margin-top: 1rem;
        }

        .payment-method-info {
            margin-bottom: 1rem;
        }
    }

    /* Focus States */
    .form-control.ot-input:focus,
    .quick-amount:focus {
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
