<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Memeriksa apakah user sudah login dengan token
        if (!Session::has('user_token')) {
            // Jika token tidak ada, redirect ke halaman login
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        return $next($request);
    }
}
