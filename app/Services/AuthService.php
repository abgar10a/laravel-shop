<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    // User Login
    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            return ['error' => 'Unauthorized', 'status' => 401];
        }

        return $this->respondWithToken($token);
    }

    // User Registration
    public function register(array $userData)
    {
        try {
            $user = User::create($userData);

            $credentials = [
                'email' => $user->email,
                'password' => $userData['password']
            ];

//            $token = JWTAuth::attempt($credentials);

            return [
                'message' => 'User created successfully',
                ...$this->login($credentials),
            ];
        } catch (\Throwable $th) {
            return [
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
                'status' => 400
            ];
        }
    }

    // Get Authenticated User
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return ['message' => 'Successfully logged out'];
    }

    // Refresh Token
    public function refreshToken()
    {
        return Auth::refresh();
    }

    // Format Token Response
    public function respondWithToken($token)
    {
        return [
            'access_token' => $token, //TODO camelcase
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];
    }
}
