<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\GrafanaService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct(GrafanaService $grafanaService)
    {
        $this->middleware('guest');
        $this->grafanaService = $grafanaService;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        $grafanaUserId = $this->grafanaService->createUser(
            $data['email'],
            $data['name'],
            $data['password']
        );
    
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'grafana_user_id' => $grafanaUserId,
        ]);
    }
    protected function register(Request $request)
    {
        // Validate and create the new user
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());
    
        // Generate an API token for the new user in Grafana
        $grafanaService = new GrafanaService(); // Make sure to pass the correct parameters if you modified the constructor
        $apiTokenResponse = $grafanaService->createApiToken($user->name);
    
        if (isset($apiTokenResponse['key'])) {
            // Save the API token in the database
            $user->api_token = $apiTokenResponse['key'];
            $user->save();
        } else {
            // Handle the error (e.g., show a message to the user, log the error)
        }
    
        // Login and redirect the user
        $this->guard()->login($user);
        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }
    
}
