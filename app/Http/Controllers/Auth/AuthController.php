<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UserManagement\User\UserService;

class AuthController extends Controller
{
    use ApiResponseHelpers;

    // injecting the dependecies
    public function __construct(private readonly UserService $userService) {}
    
    // public function for registering user
   public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // ✅ Assign only the 'Client' role
        $user->assignRole('Client');

        return response()->json([
            'message' => 'Registered successfully',
            'user' => $user
        ]);
    }

    // public function for signing in user
    public function login(LoginRequest $request)
    {
        // getting user credentials from the request
        $credentials = $request->only('email', 'password');

        // attempting to signing in the user
        if (! Auth::guard()->attempt($credentials)) {
            // returning unauthenticated exception
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 400);
        }

        // finding user by email
        $user = $this->userService->findByEmail($request->email);

        // setting the token expiration time
        $tokenExpiration = now()->addHours(24);

        // creating auth token for the user
        $token = $user->createToken(name: 'auth', expiresAt: $tokenExpiration)->plainTextToken;

        // logging in the user
        Auth::guard('web')->login($user);

        // returning success response
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'User logged in successfully!',
            'data' => [
                'user' => $user->load('roles.permissions'),
                'token' => $token,
            ],
        ]);
    }

    // method for loging out the current authenticated user
    public function logout(Request $request)
    {
        if ($token = $request->bearerToken()) {
            $authToken = PersonalAccessToken::findToken($token);
            $authToken->delete();
        }

        // returning success response
        return $this->respondWithSuccess([
            'success' => true,
            'message' => 'User logged out successfully!',
        ]);
    }

    public function unauthorize()
    {
        // returning unauthenticates response
        return $this->respondUnAuthenticated('Login to continue');
    }
}
