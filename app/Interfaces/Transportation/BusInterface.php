<?php

namespace App\Interfaces\Transportation;

/**
 * Bus Repository Interface
 *
 * Defines the contract for bus repository implementations.
 * All bus data access operations should be implemented through this interface.
 */
interface BusInterface
{
    /**
     * Get all buses.
     *
     * Retrieves all bus records without pagination.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Get paginated buses.
     *
     * Retrieves buses with pagination support.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll();

    /**
     * Store a new bus.
     *
     * Creates a new bus record in the database.
     *
     * @param mixed $request Request object containing bus data
     * @return array Response array with status, message, and data
     */
    public function store($request);

    /**
     * Show a specific bus.
     *
     * Retrieves a single bus by its ID.
     *
     * @param int $id Bus ID
     * @return \App\Models\Transportation\Bus|null
     */
    public function show($id);

    /**
     * Update an existing bus.
     *
     * Updates a bus record with new data.
     *
     * @param mixed $request Request object containing updated bus data
     * @param int $id Bus ID
     * @return array Response array with status, message, and data
     */
    public function update($request, $id);

    /**
     * Delete a bus.
     *
     * Removes a bus from the database if no students are assigned.
     *
     * @param int $id Bus ID
     * @return array Response array with status, message, and data
     */
    public function destroy($id);

    /**
     * Get active buses only.
     *
     * Retrieves all buses with active status.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBuses();

    /**
     * Get data for DataTables AJAX requests.
     *
     * Processes server-side DataTables requests with pagination,
     * ordering, searching, and custom filtering.
     *
     * @param mixed $request Request object containing DataTables parameters
     * @return array DataTables formatted response array
     */
    public function getAjaxData($request);
}
