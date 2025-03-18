<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            $incomingFields = $request->validate([
                'email' => 'required',
                'password' => 'required'
            ]);

            if (auth()->attempt(['email' => $incomingFields['email'], 'password' => $incomingFields['password']])) {
                $request->session()->regenerate();
                return response()->json([
                    'message' => 'Login successful',
                    'user' => Auth::user(),
                ], 200);
            }

            return response()->json([
                'message' => 'Invalid email or password',
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $incomingFields = $request->validate([
                'name' => ['required', 'min:3', 'max:10', Rule::unique('users', 'name')],
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'user_type' => ['required', Rule::in(['I', 'B'])],
                'address' => ['required', 'string', 'max:255'],
                'city' => ['required', 'string', 'max:100'],
                'postal_code' => ['required', 'string', 'max:20'],
                'password' => ['required', 'min:8', 'max:200']
            ]);

            $incomingFields['password'] = bcrypt($incomingFields['password']);

            $user = User::create($incomingFields);

            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage()
            ], 400);
        }
    }


    public function logout()
    {
        auth()->logout();
        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }
}
