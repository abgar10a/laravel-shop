<?php

namespace App\Services;

use App\Helpers\ResponseHelper;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthService
{
    // User Login
    public function login(array $credentials)
    {
        try {
            if ($token = JWTAuth::attempt($credentials)) {

                $user = JWTAuth::user();


                return ResponseHelper::build('User logged in successfully', [
                    'token' => $token,
                    'user' => $user,
                ]);
            } else {
                return ResponseHelper::build(error: 'Invalid credentials');
            }
        } catch (\Throwable $th) {
            return ResponseHelper::build(error: 'Attempt failed');
        }
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

            return [
                ...$this->login($credentials),
                'message' => 'User created successfully',
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
        auth()->logout();
        return ResponseHelper::build(message: 'User logged out successfully');
    }

    public function forgotPassword($email)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            $code = random_int(100000, 999999);
            $resetData = [
                'email' => $email,
                'code' => $code,
                'expires_at' => now()->addMinutes(2),
                'used' => 0
            ];

            $reset = PasswordReset::where('email', $email)->first();

            if ($reset) {
                $reset->update($resetData);
            } else {
                PasswordReset::create($resetData);
            }

            return ResponseHelper::build('Password reset code created', [
                'user' => $user,
                'code' => $code,
            ]);
        }

        return ResponseHelper::build(error: 'User not found');
    }

    public function confirmCode($email, $code)
    {
        $reset = PasswordReset::where('email', $email)
            ->where('code', $code)
            ->first();

        $user = User::where('email', $email)->first();

        if (!$reset) {
            return ['error' => 'Wrong code'];
        } else if ($reset->expires_at < now() || $reset->used) {
            return ['error' => 'Code expired'];
        } else if ($user) {
            $reset->used = true;
            $reset->updated_at = now();
            $reset->save();
            return [
                'message' => 'Email confirmed',
                'user' => $user,
            ];
        } else {
            return ['error' => 'User not found'];
        }
    }

    public function resetPassword($email, $password)
    {
        $reset = PasswordReset::where('email', $email)->first();

        if (!$reset) {
            return ['error' => 'Invalid or expired code.'];
        } else if ($reset->used) {
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($password);
            $user->updated_at = now();
            $user->save();
            $reset->delete();
            return [
                'user' => $user,
                'message' => 'Password reset successful.'
            ];
        }

        return ['error' => 'User not found'];
    }
}
