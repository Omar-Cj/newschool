<style>
    #feeCollectionModalWidth {
        max-width: 900px;
    }

    .radio-inputs {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
    }

    .radio-inputs label {
        flex: 1;
        min-width: 120px;
    }

    .radio-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 80px;
        border: 2px solid #e9ecef;
        border-radius: 0.5rem;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.15s ease-in-out;
        padding: 1rem;
        cursor: pointer;
        position: relative;
    }

    .radio-tile:hover {
        border-color: var(--primary-color, #007bff);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .radio-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .radio-input:checked + .radio-tile {
        background-color: var(--primary-color, #007bff);
        color: white;
        border-color: var(--primary-color, #007bff);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .radio-input:checked + .radio-tile .radio-icon {
        color: white;
    }

    .radio-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        color: var(--primary-color, #007bff);
        transition: color 0.15s ease-in-out;
    }

    .radio-label {
        font-weight: 600;
        font-size: 0.9rem;
        text-align: center;
    }

    .modal-header-image {
        background: linear-gradient(135deg, var(--primary-color, #007bff), var(--secondary-color, #6c757d));
        color: white;
    }

    .modal-header-image .modal-title {
        color: white !important;
        font-weight: 600;
    }

    .modal-header-image .btn-close {
        color: white !important;
    }

    .modal-header-image .btn-close i {
        color: white !important;
    }

    .card.bg-light {
        border: 1px solid #e9ecef;
        background-color: #f8f9fa !important;
    }

    .card.bg-primary {
        background: linear-gradient(135deg, var(--primary-color, #007bff), var(--info-color, #17a2b8)) !important;
    }

    #selected-fees-summary .fee-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    #selected-fees-summary .fee-item:last-child {
        border-bottom: none;
    }

    .fee-item-name {
        font-weight: 500;
    }

    .fee-item-amount {
        font-weight: 600;
        color: var(--primary-color, #007bff);
    }

    .input-group .btn {
        border-left: 0;
    }

    .form-control:focus + .btn,
    .form-control:focus + .input-group-append .btn {
        border-color: var(--primary-color, #007bff);
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .calculation-display {
        background: linear-gradient(135deg, #28a745, #20c997);
        border-radius: 0.5rem;
        padding: 1rem;
        color: white;
        text-align: center;
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }

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

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .radio-inputs {
            flex-direction: column;
        }

        .radio-inputs label {
            min-width: 100%;
        }

        .radio-tile {
            min-height: 60px;
            padding: 0.75rem;
        }

        .radio-icon {
            font-size: 1.25rem;
        }
    }

    /* Animation for modal transitions */
    .modal.fade .modal-dialog {
        transform: translate(0, -50px);
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* Custom scrollbar for modal body */
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

    /* Summary header student info */
    .student-info {
        font-weight: 600;
        color: #495057;
    }

    .student-info .fas {
        color: var(--primary-color, #007bff);
    }

    .outstanding-overview .h5 {
        color: #dc3545;
    }

    /* Modal Footer Button Fixes + footer text color */
    .modal-footer .btn {
        color: white !important;
    }

    .modal-footer .btn-outline-secondary {
        color: #6c757d !important;
        border-color: #6c757d !important;
    }

    .modal-footer .btn-outline-secondary:hover {
        color: white !important;
        background-color: #6c757d !important;
        border-color: #6c757d !important;
    }

    .modal-footer .ot-btn-primary {
        background-color: var(--primary-color, #007bff) !important;
        border-color: var(--primary-color, #007bff) !important;
        color: white !important;
    }

    .modal-footer .ot-btn-primary:hover {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
        color: white !important;
    }

    .modal-footer .ot-btn-primary i {
        color: white !important;
    }

    /* Ensure calculation card text stays white */
    .card.bg-primary .card-body,
    .card.bg-primary .mb-1,
    .card.bg-primary strong {
        color: #fff !important;
    }

    /* Responsive adjustments for summary header */
    @media (max-width: 768px) {
        .student-info {
            font-size: 0.9rem;
        }
    }
</style>
