<?php

namespace App\Repositories\Fees;

use App\Enums\Settings;
use App\Models\Fees\Receipt;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Fees\ReceiptInterface;

class ReceiptRepository implements ReceiptInterface
{
    use ReturnFormatTrait;
    use CommonHelperTrait;

    private $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Get all receipts with pagination
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll()
    {
        return $this->receipt->with(['student', 'academicYear', 'session'])
            ->latest('payment_date')
            ->paginate(Settings::PAGINATE);
    }

    /**
     * Get receipts data for DataTables AJAX server-side processing.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getAjaxData($request)
    {
        try {
            // Base query with eager loading for performance
            $query = $this->receipt->with([
                'student',
                'paymentTransactions.feesCollect.feeType',
                'academicYear',
                'session',
                'branch'
            ])
            // Scope to active session if available
            ->when(setting('session'), function ($q) {
                $q->where('session_id', setting('session'));
            });

            // Apply custom filters

            // Student search filter (search by name, admission number, or receipt number)
            if ($request->filled('student_search')) {
                $search = $request->student_search;
                $query->where(function ($q) use ($search) {
                    $q->where('receipt_number', 'LIKE', "%{$search}%")
                      ->orWhere('student_name', 'LIKE', "%{$search}%")
                      ->orWhereHas('student', function ($studentQuery) use ($search) {
                          $studentQuery->where('admission_no', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Date range filters
            if ($request->filled('from_date')) {
                $query->whereDate('payment_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('payment_date', '<=', $request->to_date);
            }

            // Payment method filter
            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            // Collector filter
            if ($request->filled('collector_id')) {
                $query->where('collected_by', $request->collector_id);
            }

            // Family payments only filter
            if ($request->filled('family_payments_only') && $request->family_payments_only == '1') {
                $query->whereNotNull('payment_session_id');
            }

            // DataTables global search functionality
            if ($request->filled('search.value')) {
                $searchValue = $request->input('search.value');
                $query->where(function ($q) use ($searchValue) {
                    $q->where('receipt_number', 'LIKE', "%{$searchValue}%")
                      ->orWhere('student_name', 'LIKE', "%{$searchValue}%")
                      ->orWhere('class', 'LIKE', "%{$searchValue}%")
                      ->orWhere('transaction_reference', 'LIKE', "%{$searchValue}%")
                      ->orWhereHas('student', function ($studentQuery) use ($searchValue) {
                          $studentQuery->where('first_name', 'LIKE', "%{$searchValue}%")
                                      ->orWhere('last_name', 'LIKE', "%{$searchValue}%")
                                      ->orWhere('admission_no', 'LIKE', "%{$searchValue}%");
                      });
                });
            }

            // Get total count before filtering
            $recordsTotal = $this->receipt
                ->when(setting('session'), function ($q) {
                    $q->where('session_id', setting('session'));
                })
                ->count();

            // Get filtered count
            $recordsFiltered = $query->count();

            // Apply ordering
            $orderColumnIndex = $request->input('order.0.column', 6); // Default to payment_date
            $orderDirection = $request->input('order.0.dir', 'desc');

            $columns = [
                'id',                    // 0
                'receipt_number',        // 1
                'student_name',          // 2
                'class',                 // 3
                'total_amount',          // 4
                'discount_amount',       // 5
                'payment_date',          // 6
                'payment_method',        // 7
                'collected_by',          // 8
                'payment_status',        // 9
                'actions'                // 10
            ];

            $orderColumn = $columns[$orderColumnIndex] ?? 'payment_date';

            // Handle special column ordering
            if ($orderColumn === 'collected_by') {
                // We can't easily order by relationship, so we'll skip this or use a subquery
                $query->orderBy('payment_date', $orderDirection);
            } else if ($orderColumn !== 'actions') {
                $query->orderBy($orderColumn, $orderDirection);
            }

            // Apply pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $receipts = $query->skip($start)->take($length)->get();

            // Performance optimization: Pre-fetch family receipt counts to avoid N+1 queries
            $familyCounts = [];
            $familySessionIds = $receipts->pluck('payment_session_id')->filter()->unique();

            if ($familySessionIds->isNotEmpty()) {
                $familyCounts = $this->receipt->whereIn('payment_session_id', $familySessionIds)
                    ->groupBy('payment_session_id')
                    ->selectRaw('payment_session_id, COUNT(*) as receipt_count')
                    ->pluck('receipt_count', 'payment_session_id')
                    ->toArray();
            }

            // Format data for DataTables
            $data = [];
            $counter = $start + 1;

            foreach ($receipts as $receipt) {
                $row = [];

                // 0. Serial number
                $row[] = $counter++;

                // 1. Receipt Number with badge
                $receiptNumberHtml = '<div class="fw-semibold text-primary">' . e($receipt->receipt_number) . '</div>';

                // Add receipt type badge
                if ($receipt->source_type === 'payment_transaction') {
                    $receiptNumberHtml .= '<small class="badge badge-basic-info-text">' . ___('fees.enhanced') . '</small>';
                } else {
                    $receiptNumberHtml .= '<small class="badge badge-basic-secondary-text">' . ___('fees.legacy') . '</small>';
                }

                $row[] = $receiptNumberHtml;

                // 2. Student Name with admission number
                $studentName = $receipt->student
                    ? $receipt->student->first_name . ' ' . $receipt->student->last_name
                    : $receipt->student_name;
                $admissionNo = $receipt->student ? $receipt->student->admission_no : '-';

                $studentHtml = '<div class="fw-semibold">' . e($studentName) . '</div>';
                $studentHtml .= '<small class="text-muted">' . ___('student_info.admission_no') . ': ' . e($admissionNo) . '</small>';
                $row[] = $studentHtml;

                // 3. Class & Section
                $classSection = e($receipt->class) . ' - ' . e($receipt->section ?? 'N/A');
                $row[] = $classSection;

                // 4. Amount Paid with family indicator
                // IMPORTANT: Display total_amount (before discount) in Amount Paid column
                $currencySymbol = Setting('currency_symbol', '$');

                $amountHtml = '<div class="fw-semibold text-success">' . $currencySymbol . ' ' . number_format($receipt->total_amount, 2) . '</div>';

                // Add family payment indicator (using pre-fetched counts - no N+1 query)
                if ($receipt->isPartOfFamilyPayment()) {
                    $familyCount = $familyCounts[$receipt->payment_session_id] ?? 1;
                    $amountHtml .= '<small class="badge badge-info" title="' . ___('fees.family_payment') . '">';
                    $amountHtml .= '<i class="fas fa-users"></i> ' . ___('fees.family') . ' (' . $familyCount . ')';
                    $amountHtml .= '</small>';
                }

                $row[] = $amountHtml;

                // 5. Discount Amount
                $discountHtml = '<div class="text-center">';
                if ($receipt->discount_amount > 0) {
                    $discountHtml .= '<span class="fw-semibold text-danger">-' . $currencySymbol . ' ' . number_format($receipt->discount_amount, 2) . '</span>';
                } else {
                    $discountHtml .= '<span class="text-muted">' . $currencySymbol . ' 0.00</span>';
                }
                $discountHtml .= '</div>';
                $row[] = $discountHtml;

                // 6. Payment Date
                $row[] = dateFormat($receipt->payment_date);

                // 7. Payment Method with badge
                $paymentMethodName = $receipt->getPaymentMethodName();
                $paymentMethodHtml = '<span class="badge badge-basic-info-text">' . e($paymentMethodName) . '</span>';

                $row[] = $paymentMethodHtml;

                // 8. Collected By
                $collectorName = $receipt->collector ? $receipt->collector->name : '-';
                $row[] = e($collectorName);

                // 9. Payment Status
                $statusHtml = '';
                if ($receipt->payment_status === 'partial' || ($receipt->discount_amount > 0 && $receipt->discount_amount < $receipt->total_amount)) {
                    $statusHtml = '<span class="badge badge-basic-warning-text">' . ___('fees.partial') . '</span>';
                } else {
                    $statusHtml = '<span class="badge badge-basic-success-text">' . ___('fees.paid') . '</span>';
                }
                $row[] = $statusHtml;

                // 10. Actions dropdown
                $actionsHtml = '<div class="dropdown dropdown-action">';
                $actionsHtml .= '<button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">';
                $actionsHtml .= '<i class="fa-solid fa-ellipsis"></i>';
                $actionsHtml .= '</button>';
                $actionsHtml .= '<ul class="dropdown-menu dropdown-menu-end">';

                // Print action
                $actionsHtml .= '<li>';
                $actionsHtml .= '<a class="dropdown-item" href="javascript:void(0);" onclick="printReceipt(' . $receipt->id . ')">';
                $actionsHtml .= '<span class="icon mr-8"><i class="fa-solid fa-print"></i></span>';
                $actionsHtml .= ___('common.print');
                $actionsHtml .= '</a>';
                $actionsHtml .= '</li>';

                // Download action
                $downloadUrl = route('fees.receipt.individual', $receipt->id);
                $actionsHtml .= '<li>';
                $actionsHtml .= '<a class="dropdown-item" target="_blank" href="' . $downloadUrl . '">';
                $actionsHtml .= '<span class="icon mr-8"><i class="fa-solid fa-download"></i></span>';
                $actionsHtml .= ___('common.download');
                $actionsHtml .= '</a>';
                $actionsHtml .= '</li>';

                // TODO: View Family action - requires fees.receipt.family route implementation
                // if ($receipt->isPartOfFamilyPayment()) {
                //     $familyUrl = route('fees.receipt.family', $receipt->payment_session_id);
                //     $actionsHtml .= '<li>';
                //     $actionsHtml .= '<a class="dropdown-item" href="' . $familyUrl . '">';
                //     $actionsHtml .= '<span class="icon mr-8"><i class="fa-solid fa-users"></i></span>';
                //     $actionsHtml .= ___('fees.view_family');
                //     $actionsHtml .= '</a>';
                //     $actionsHtml .= '</li>';
                // }

                $actionsHtml .= '</ul>';
                $actionsHtml .= '</div>';

                $row[] = $actionsHtml;

                $data[] = $row;
            }

            // Return DataTables formatted response
            return [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ];

        } catch (\Throwable $th) {
            \Log::error('Receipt AJAX data fetch failed: ' . $th->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'exception' => $th->getTraceAsString()
            ]);

            return [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => ___('alert.something_went_wrong_please_try_again')
            ];
        }
    }

    /**
     * Get receipt by ID
     *
     * @param int $id
     * @return \App\Models\Fees\Receipt|null
     */
    public function show($id)
    {
        return $this->receipt->with([
            'student',
            'paymentTransactions.feesCollect.feeType',
            'academicYear',
            'session',
            'branch'
        ])->find($id);
    }
}
