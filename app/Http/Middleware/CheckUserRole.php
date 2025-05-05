<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
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

            return ResponseHelper::error(__('user.invalid_user_type'), Response::HTTP_UNAUTHORIZED);
        }

        return ResponseHelper::error('User type not defined', Response::HTTP_UNAUTHORIZED);
    }
}
