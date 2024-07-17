<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }
    public function login(Request $request) {

        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (filter_var($request->get('username'), FILTER_VALIDATE_EMAIL)) {
            if (Auth::attempt(
                [
                    'email' => $request->get('username'), 
                    'password' => $request->get('password')
                ], $request->filled('remember'))) {

                return redirect('/');
            }
        } else {
            if (Auth::attempt(
                [
                    'username' => $request->get('username'), 
                    'password' => $request->get('password')
                ], $request->filled('remember'))) {

                    return redirect('/');
            }
        }

        return redirect("login");
    }
}
