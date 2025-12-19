<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\SsoUserInfoClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticateViaSSO
{
    public function __construct(
        private readonly SsoUserInfoClient $client
    ) {
    }

    /**
     * @param  array<string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $token = $this->extractToken($request);

        if (!$token) {
            return $this->unauthorized($request, 'Authorization bearer token tidak ditemukan.');
        }

        $payload = $this->client->fetch($token);
        $this->assertAppAccess($payload);

        $user = $this->resolveLocalUser($payload);
        $role = $user?->role ?? config('sso.default_role', 'user');

        if (!empty($roles) && !in_array($role, $roles, true)) {
            return $this->forbidden($request, 'Akses ditolak untuk role ini.');
        }

        // Simpan konteks untuk dipakai downstream
        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('sso_payload', $payload);
        $request->attributes->set('sso_access_token', $token);
        $request->attributes->set('sso_role', $role);

        // Simpan ringkas ke session agar halaman Blade bisa konsumsi cepat
        $request->session()->put([
            'user_token' => $token,
            // Alias untuk kompatibilitas kode lama (banyak controller masih pakai user_id).
            'user_id' => $payload['sub'] ?? null,
            'user_sub' => $payload['sub'] ?? null,
            'user_email' => $payload['email'] ?? null,
            'user_name' => $payload['name'] ?? null,
            'user_role' => $role,
            // "level" lama digantikan role tenant KilauCMS (admin/donatur).
            'user_level' => $role === 'admin' ? 'admin' : 'donatur',
        ]);

        return $next($request);
    }

    protected function extractToken(Request $request): ?string
    {
        $authorization = $request->header('Authorization', '');

        if (preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
            return trim($matches[1]);
        }

        // fallback untuk web routes: token disimpan di session
        return $request->session()->get('user_token');
    }

    protected function assertAppAccess(array $payload): void
    {
        $appSlug = config('sso.app_slug');
        $apps = $payload['apps_allowed'] ?? [];

        if ($appSlug && is_array($apps) && !in_array($appSlug, $apps, true)) {
            throw new UnauthorizedHttpException('Bearer', 'Aplikasi ini tidak diizinkan pada token SSO.');
        }
    }

    protected function resolveLocalUser(array $payload): ?User
    {
        $sub = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;

        $userQuery = User::query();

        if ($sub) {
            $userQuery->where('sso_sub', $sub);
        }

        if ($email) {
            $userQuery->orWhere('email', $email);
        }

        $user = $userQuery->first();

        if ($user || !config('sso.auto_provision_user', true) || !$sub) {
            return $user;
        }

        // Autoprovision user lokal minimal agar Auth::user() tidak null
        return User::create([
            'name' => $payload['name'] ?? ($email ?: 'SSO User'),
            'email' => $email ?: 'sso-'.$sub.'@example.test',
            'password' => Hash::make(Str::random(32)),
            'sso_sub' => $sub,
            'role' => config('sso.default_role', 'user'),
            'sso_payload' => $payload,
        ]);
    }

    protected function unauthorized(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 401);
        }

        return redirect()->route('login')->with('error', $message);
    }

    protected function forbidden(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->route('home')->with('error', $message);
    }
}
