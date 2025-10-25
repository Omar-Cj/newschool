<?php

namespace Modules\Journals\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Journals\Interfaces\JournalInterface;
use Modules\Journals\Http\Requests\JournalStoreRequest;
use Modules\Journals\Http\Requests\JournalUpdateRequest;

class JournalController extends Controller
{
    private $repo;

    public function __construct(JournalInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['title'] = ___('journals.journals');

        if ($request->has('search') || $request->has('status') || $request->has('branch') || $request->has('branch_id')) {
            $data['journals'] = $this->repo->search($request);
        } else {
            $data['journals'] = $this->repo->getPaginateAll();
        }

        // Load branches for filter dropdown
        $data['branches'] = $this->repo->getBranchesForDropdown();

        return view('journals::index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data['title'] = ___('journals.add_journal');
        $data['branches'] = $this->repo->getBranchesForDropdown();
        return view('journals::create', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JournalStoreRequest $request)
    {
        $result = $this->repo->store($request);

        if ($result['status']) {
            return redirect()->route('journals.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data['title'] = ___('journals.journal_details');
        $data['journal'] = $this->repo->show($id);

        if (!$data['journal']) {
            return redirect()->route('journals.index')->with('danger', ___('alert.data_not_found'));
        }

        return view('journals::show', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data['title'] = ___('journals.edit_journal');
        $data['journal'] = $this->repo->show($id);

        if (!$data['journal']) {
            return redirect()->route('journals.index')->with('danger', ___('alert.data_not_found'));
        }

        // Check if journal is closed (inactive)
        if ($data['journal']->status === 'inactive') {
            return redirect()->route('journals.index')->with('danger', ___('alert.cannot_edit_closed_journal'));
        }

        $data['branches'] = $this->repo->getBranchesForDropdown();
        return view('journals::edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JournalUpdateRequest $request, $id)
    {
        // Check if journal is closed (inactive) before updating
        $journal = $this->repo->show($id);
        if ($journal && $journal->status === 'inactive') {
            return redirect()->route('journals.index')->with('danger', ___('alert.cannot_edit_closed_journal'));
        }

        $result = $this->repo->update($request, $id);

        if ($result['status']) {
            return redirect()->route('journals.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->repo->destroy($id);

        if ($result['status']) {
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        } else {
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        }
    }

    /**
     * Close the specified journal (set status to inactive)
     */
    public function close($id)
    {
        // Get journal to validate transfer status
        $journal = $this->repo->show($id);

        if (!$journal) {
            $success[0] = ___('alert.data_not_found');
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        }

        // Check if journal is fully transferred before closing
        if (!$journal->isFullyTransferred()) {
            throw new \App\Exceptions\JournalNotFullyTransferredException(
                "Journal cannot be closed. Transfer progress: {$journal->progress_percentage}%. Please ensure all amounts are transferred before closing.",
                $journal->progress_percentage
            );
        }

        $result = $this->repo->close($id);

        if ($result['status']) {
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.journal_closed');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        } else {
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        }
    }

    /**
     * Open the specified journal (set status to active) - Super Admin only
     */
    public function open($id)
    {
        // Check if user is super admin (role_id = 1)
        if (auth()->user()->role_id !== 1) {
            $success[0] = ___('alert.unauthorized_action');
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success, 403);
        }

        $result = $this->repo->open($id);

        if ($result['status']) {
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.journal_opened');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        } else {
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        }
    }

    /**
     * Get journals for dropdown/select2
     */
    public function getJournalsDropdown(Request $request)
    {
        $journals = $this->repo->getJournalsForDropdown(
            $request->school_id,
            $request->branch_id
        );
        return response()->json($journals);
    }

    /**
     * Get journal details for AJAX requests
     */
    public function getJournalDetails($id)
    {
        $journal = $this->repo->show($id);

        if (!$journal) {
            return response()->json([
                'success' => false,
                'message' => ___('alert.data_not_found')
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $journal->id,
                'name' => $journal->name,
                'branch' => $journal->branch,
                'description' => $journal->description,
                'display_name' => $journal->display_name,
                'status' => $journal->status
            ]
        ]);
    }
}
