<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Set school context in session if user has school_id
        if ($user->school_id) {
            session(['school_id' => $user->school_id]);
        }

        // System Admin (role_id = 0) routes to MainApp dashboard
        if ($user->role_id == 0) {
            return redirect()->route('mainapp.dashboard');
        }

        // Redirect based on user role
        if ($this->isAdminUser($user)) {
            return redirect('/dashboard');
        }

        return redirect('/dashboard');
    }

    /**
     * Check if the user is an admin user.
     *
     * @param mixed $user
     * @return bool
     */
    private function isAdminUser($user): bool
    {
        return in_array($user->role_id, [
            RoleEnum::MAIN_SYSTEM_ADMIN,  // System-level administrator
            RoleEnum::SUPERADMIN,
            RoleEnum::ADMIN,
        ]);
    }
}
