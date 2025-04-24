<?php

namespace App\Services;

use App\Enums\UserTypes;
use App\Helpers\EmailHelper;
use App\Helpers\ResponseHelper;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            if(env('APP_TEST') && !isset($userData['user_type'])) $userData['user_type'] = UserTypes::INDIVIDUAL->value;
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
            return ResponseHelper::build(error: $th->getMessage());
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

            $emailResponse = EmailHelper::sendEmail($user, 'Email confirmation', ['code' => $code], 'email_confirmation');

            if (isset($emailResponse['error'])) {
                return $emailResponse;
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
            return ResponseHelper::build(error: 'Wrong code');
        } else if ($reset->expires_at < now() || $reset->used) {
            return ResponseHelper::build(error: 'Code expired');
        } else if ($user) {
            $reset->used = true;
            $reset->updated_at = now();
            $reset->save();
            return ResponseHelper::build('Code confirmed');
        } else {
            return ResponseHelper::build(error: 'User not found');
        }
    }

    public function resetPassword($email, $password)
    {
        $reset = PasswordReset::where('email', $email)->first();

        if (!$reset) {
            return ResponseHelper::build(error: 'Invalid or expired code.');
        } else if ($reset->used) {
            $user = User::where('email', $email)->first();
            $user->password = Hash::make($password);
            $user->updated_at = now();
            $user->save();
            $reset->delete();

            $emailResponse = EmailHelper::sendEmail($user, 'Email confirmation', [], 'password_reset');

            if (isset($emailResponse['error'])) {
                return ResponseHelper::error($emailResponse['error']);
            }

            return ResponseHelper::build('Password reset successfully');
        }

        return ResponseHelper::build(error: 'User not found');
    }

    public function handleOauthCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $password = env('APP_TEST') ? '12345678' : Str::random(16);
            return $this->register([
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName(),
                'password' => $password,
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return ResponseHelper::build('User logged in successfully', [
            'token' => $token,
            'user' => $user,
        ]);
    }
}
