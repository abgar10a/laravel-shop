<?php

namespace App\Http\Controllers;

use App\Enums\UserTypes;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private $authService;

    public function __construct() {
        $this->authService = app(AuthService::class);
    }

    // User Login & Get Token
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $result = $this->authService->login($credentials);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json(['token' => $result['access_token']]);
    }

    // User Registration
    public function register(Request $request)
    {
        try {
            $userData = $request->validate([
                'name' => ['required', 'min:3', 'max:10'],
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'user_type' => ['required', Rule::in(UserTypes::cases())],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'postal_code' => ['required', 'string', 'max:20'],
                'password' => ['required', 'min:8', 'max:200']
            ]);

            $result = $this->authService->register($userData);

            if (isset($result['error'])) {
                return response()->json([
                    'message' => $result['message'],
                    'error' => $result['error']
                ], $result['status']);
            }

            return response()->json($result, 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    // Get Authenticated User
    public function me()
    {
        return response()->json($this->authService->getAuthenticatedUser());
    }

    // Logout
    public function logout()
    {
        return response()->json($this->authService->logout());
    }

    // Refresh Token
    public function refresh()
    {
        return response()->json($this->authService->respondWithToken($this->authService->refreshToken()));
    }
}
