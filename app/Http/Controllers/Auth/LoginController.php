<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use App\Services\GrafanaService;


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
    protected $grafanaService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(GrafanaService $grafanaService)
    {
        $this->middleware('guest')->except('logout');
        $this->grafanaService = $grafanaService;
    }

    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $username = $credentials[$this->username()];
        $password = $credentials['password'];
    
        // Call GrafanaService's authenticateUser method to authenticate against the Grafana API
        if ($this->grafanaService->authenticateUser($username, $password)) {
            return $credentials;
        }
    
        return []; // Return an empty array instead of false
    }
}
