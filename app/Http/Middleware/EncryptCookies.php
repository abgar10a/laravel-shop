<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class EncryptCookies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Ensure cookies are encrypted or decrypted correctly
        $response = $next($request);

        // If you need to encrypt cookies here, you can use Cookie facade
        // This example just encrypts all cookies for illustration
        foreach (Cookie::getCookies() as $cookie) {
            $response->headers->setCookie(Cookie::make(
                $cookie->getName(),
                encrypt($cookie->getValue())
            ));
        }

        return $response;
    }
}
