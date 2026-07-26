<?php

namespace App\Http\Middleware;

use App\Services\LocalUserSession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateLocalSession
{
    public function __construct(
        private readonly LocalUserSession $localSession
    ) {
    }

    /**
     * @param  array<string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $this->unauthorized($request, 'Silakan login terlebih dahulu.');
        }

        $role = $user->role ?: 'user';

        if (!empty($roles) && !in_array($role, $roles, true)) {
            return $this->forbidden($request, 'Akses ditolak untuk role ini.');
        }

        $this->localSession->put($request, $user);
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('user_role', $role);

        return $next($request);
    }

    protected function unauthorized(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()->guest(route('login'))->with('error', $message);
    }

    protected function forbidden(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->route('home')->with('error', $message);
    }
}
