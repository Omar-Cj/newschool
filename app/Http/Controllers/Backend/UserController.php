<?php

namespace App\Http\Controllers\Backend;

use App\Models\Staff\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use App\Interfaces\RoleInterface;
use App\Interfaces\UserInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Interfaces\PermissionInterface;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Interfaces\GenderInterface;
use App\Interfaces\Staff\DepartmentInterface;
use App\Interfaces\Staff\DesignationInterface;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    private $user;
    private $permission;
    private $role;
    private $designation;
    private $department;
    private $gender;

    function __construct(
        UserInterface $user,
        PermissionInterface $permission,
        RoleInterface $role,
        DesignationInterface $designation,
        DepartmentInterface $department,
        GenderInterface $gender,

        )
    {

        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        }
        $this->user         = $user;
        $this->permission   = $permission;
        $this->role         = $role;
        $this->designation  = $designation;
        $this->department   = $department;
        $this->gender       = $gender;
    }

    public function index()
    {
        $data['users'] = $this->user->getAll();
        $data['title'] = ___('staff.staff');
        return view('backend.users.index', compact('data'));
    }

    public function create()
    {
        $data['title']         = ___('staff.create_staff');
        $data['permissions']   = $this->permission->all();
        $data['roles']         = $this->role->all();
        $data['designations']  = $this->designation->all();
        $data['departments']   = $this->department->all();
        $data['genders']       = $this->gender->all();
        return view('backend.users.create', compact('data'));
    }

    public function store(UserStoreRequest $request)
    {
        $result = $this->user->store( $request);
        if ($result == 2) {
            return redirect()->route('users.index')->with('danger', ___('alert.Staff limit is over.'));
        }
        elseif ($result == 1) {
            return redirect()->route('users.index')->with('success', ___('alert.user_created_successfully'));
        }
        return redirect()->route('users.index')->with('danger',  ___('alert.something_went_wrong_please_try_again'));
    }

    public function edit($id)
    {
        $data['user']          = $this->user->show($id);
        $data['title']         = ___('staff.update_staff');
        $data['permissions']   = $this->permission->all();
        $data['roles']         = $this->role->all();
        $data['designations']  = $this->designation->all();
        $data['departments']   = $this->department->all();
        $data['genders']       = $this->gender->all();
        $data['canEditPassword'] = $this->canEditStaffPassword($data['user']);
        // dd($data);
        return view('backend.users.edit', compact('data'));
    }

    public function show($id)
    {
        $data = $this->user->show($id);
        return view('backend.users.show', compact('data'));
    }

    public function update(UserUpdateRequest $request, $id)
    {
        // Authorization check for password updates
        if ($request->filled('password')) {
            $staff = $this->user->show($id);
            if (!$this->canEditStaffPassword($staff)) {
                return redirect()->route('users.index')
                    ->with('danger', ___('alert.unauthorized_password_update'));
            }
        }

        $result = $this->user->update($request, $id);
        if ($result) {
            return redirect()->route('users.index')->with('success', ___('alert.user_updated_successfully'));
        }
        return redirect()->route('users.index')->with('danger',  ___('alert.something_went_wrong_please_try_again'));
    }

    public function delete($id)
    {
        if ($this->user->destroy($id)) :
            $success[0] = ___('alert.deleted_successfully');
            $success[1] = "Success";
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
        else :
            $success[0] = ___('alert.something_went_wrong_please_try_again');
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
        endif;
        return response()->json($success);
    }

    public function changeRole(Request $request)
    {
        $data['role_permissions'] = $this->role->show($request->role_id)->permissions;
        $data['permissions']      = $this->permission->all();
        return view('backend.users.permissions', compact('data'))->render();
    }

    public function status(Request $request)
    {

        if ($request->type == 'active') {
            $request->merge([
                'status' => 1
            ]);
            $this->user->status($request);
        }

        if ($request->type == 'inactive') {
            $request->merge([
                'status' => 0
            ]);
            $this->user->status($request);
        }

        return response()->json(["message" => __("Status update successful")], Response::HTTP_OK);
    }

    public function deletes(Request $request)
    {
        $this->user->deletes($request);

        return response()->json(["message" => __('Delete successful.')], Response::HTTP_OK);
    }

    public function changePermission($id)
    {
        $staff = Staff::with('role', 'user')->find($id);
        $data['role'] = $staff->user->role;
        $data['title']       = ___('common.Change Permission');
        $data['user_permissions'] = $staff->user->permissions;
        // Filter out permissions with null or empty keywords for data integrity
        $data['permissions'] = $this->permission->all()->filter(function($permission) {
            return !is_null($permission->keywords) && is_array($permission->keywords) && !empty($permission->keywords);
        });
        $data['staff'] = $staff;
        return view('backend.users.change_permission', compact('data'));
    }

    public function permissionUpdate(Request $request, $id)
    {
        $user = User::find($id);
        $user->permissions = $request->permissions;
        $user->save();
        return back()->with('success', ___('alert.permission_updated_successfully'));
    }

    /**
     * Check if current user can edit the target staff member's password
     *
     * Authorization Rules:
     * - Super Admin (role_id=1): Can edit ANY staff password in their school
     * - Branch Admin (role_id=2): Can edit ONLY their branch's staff passwords
     *
     * @param mixed $staff Staff model instance
     * @return bool
     */
    protected function canEditStaffPassword($staff): bool
    {
        $currentUser = auth()->user();

        if (!$currentUser) {
            return false;
        }

        // Get the staff's user record
        $staffUser = User::find($staff->user_id);

        if (!$staffUser) {
            return false;
        }

        // Super Admin (role_id = 1) - can edit any staff in their school
        if ($currentUser->role_id === 1) {
            return $currentUser->school_id === $staffUser->school_id;
        }

        // Branch Admin (role_id = 2) - only their branch
        if ($currentUser->role_id === 2) {
            return $currentUser->school_id === $staffUser->school_id
                && $currentUser->branch_id === $staffUser->branch_id;
        }

        return false;
    }
}
