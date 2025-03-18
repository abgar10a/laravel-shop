<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request by checking if the user type matches the required type.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string ...$types Allowed user types
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        if (!empty($types)) {
            $user = $request->user();

            if (in_array($user->user_type, $types)) {
                return $next($request);
            }

            return response()->json([
                'error' => 'Invalid user type for user : ' . $user->name . ', user type : ' . $user->user_type,
            ], 404);
        }

        return response()->json([
            'error' => 'User type not defined',
        ], 404);
    }
}
