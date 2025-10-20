<?php

namespace App\Interfaces\Fees;

use Illuminate\Http\Request;

interface ReceiptInterface
{
    /**
     * Get all receipts with pagination
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll();

    /**
     * Get receipts data for DataTables AJAX server-side processing
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getAjaxData(Request $request);

    /**
     * Get receipt by ID
     *
     * @param int $id
     * @return \App\Models\Fees\Receipt|null
     */
    public function show($id);
}
