<?php

namespace App\Repositories\Report;

use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;

class BillingReportRepository
{
    use ReturnFormatTrait;

    /**
     * Get Paid Students Report
     * Calls stored procedure GetPaidStudentsReport
     *
     * @param object $request
     * @return array
     */
    public function getPaidStudents($request): array
    {
        try {
            // Call stored procedure with parameters matching the procedure signature
            // Use filled() to convert empty strings to NULL for stored procedure
            $results = DB::select("CALL GetPaidStudentsReport(?, ?, ?, ?, ?, ?)", [
                $request->start_date,
                $request->end_date,
                $request->filled('grade') ? $request->grade : null,
                $request->filled('class_id') ? $request->class_id : null,
                $request->filled('section_id') ? $request->section_id : null,
                $request->filled('gender_id') ? $request->gender_id : null
            ]);

            // Transform results to collection
            $data = collect($results);

            // Calculate summary totals
            $totalPaidAmount = $data->sum('paid_amount');
            $totalDeposit = $data->sum('deposit_used');
            $totalDiscount = $data->sum('discount');

            // Net total calculation: Paid Amount + Deposit - Discounts
            $netTotal = $totalPaidAmount + $totalDeposit - $totalDiscount;

            return [
                'success' => true,
                'data' => $data,
                'summary' => [
                    'total_paid_amount' => number_format($totalPaidAmount, 2),
                    'total_deposit' => number_format($totalDeposit, 2),
                    'total_discount' => number_format($totalDiscount, 2),
                    'net_total' => number_format($netTotal, 2),
                ],
                'count' => $data->count()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => collect([]),
                'summary' => [
                    'total_paid_amount' => '0.00',
                    'total_deposit' => '0.00',
                    'total_discount' => '0.00',
                    'net_total' => '0.00',
                ],
                'count' => 0
            ];
        }
    }

    /**
     * Get Unpaid Students Report
     * Calls stored procedure GetUnpaidStudentsReport
     *
     * @param object $request
     * @return array
     */
    public function getUnpaidStudents($request): array
    {
        try {
            // Call stored procedure with parameters matching the procedure signature
            // Use filled() to convert empty strings to NULL for stored procedure
            // Special handling for status field: preserve 0 as valid value (inactive)
            $results = DB::select("CALL GetUnpaidStudentsReport(?, ?, ?, ?, ?, ?, ?)", [
                $request->start_date,
                $request->end_date,
                $request->filled('grade') ? $request->grade : null,
                $request->filled('class_id') ? $request->class_id : null,
                $request->filled('section_id') ? $request->section_id : null,
                $request->has('status') && $request->input('status') !== '' ? $request->input('status') : null,
                $request->filled('shift_id') ? $request->shift_id : null
            ]);

            // Transform results to collection
            $data = collect($results);

            // Calculate summary total
            $totalOutstanding = $data->sum('total_amount');

            return [
                'success' => true,
                'data' => $data,
                'summary' => [
                    'total_outstanding' => number_format($totalOutstanding, 2),
                ],
                'count' => $data->count()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => collect([]),
                'summary' => [
                    'total_outstanding' => '0.00',
                ],
                'count' => 0
            ];
        }
    }
}
