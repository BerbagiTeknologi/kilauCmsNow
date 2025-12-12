<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DonasiHistory;
use App\Models\DonasiKilau;
use App\Models\User;
use App\Services\SsoUserInfoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class LoginController extends Controller
{
    public function login() {
        return view('Auth.login');
    }

    public function getDataUsersProfile(Request $request)
    {
        // Ambil user_id dari session (diset saat loginProses)
        $externalUserId = session('user_id');

        // Kalau belum login, arahkan ke halaman login (opsional)
        if (!$externalUserId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil histori donasi user + relasi donasi & program
        $histories = DonasiHistory::with([
                'donasikilau' => function ($q) {
                    $q->select('id','type_donasi','opsional_umum','id_program','nama','total_donasi','status_donasi','created_at')
                      ->with(['program:id,judul']);
                }
            ])
            ->where('external_user_id', $externalUserId)
            ->orderByDesc('created_at')
            ->paginate(10); // ganti ->get() jika tak mau paginate

        // Data profil dasar dari session (untuk render cepat di blade)
        $user = [
            'id'            => session('user_id'),
            'nama'          => session('user_name'),
            'email'         => session('user_email'),
            'level'         => session('user_level'),
            'referral_code' => session('user_referral_code'),
            'foto'          => session('user_photo'),
        ];

        // Peta label status donasi
        $statusMap = [
            DonasiKilau::DONASI_PENDING => 'Pending',
            DonasiKilau::DONASI_AKTIVE  => 'Aktif',
        ];

        // Peta label opsional umum (berdasarkan konstanta di model)
        $opsionalUmumMap = [
            DonasiKilau::OPSIONAL_UMUM_ZAKAT => 'Zakat',
            DonasiKilau::OPSIONAL_UMUM_INFAQ => 'Infaq',
        ];

        return view('Auth.profile', compact('user','histories','statusMap','opsionalUmumMap'));
    }

  

    public function loginProses(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        $driver = config('kilau.auth_driver', 'remote');

        $response = $driver === 'local'
            ? $this->authenticateLocally($credentials)
            : $this->makeApiRequest($credentials);

        if ($response->status() !== 200) {
            $request->session()->flush();
            return response()->json([
                'error' => $response->json()['message'] ?? 'Login gagal.',
            ], $response->status());
        }

        $data = $response->json();
        $accessToken = $data['access_token'] ?? $data['token'] ?? null;

        if (!$accessToken) {
            $request->session()->flush();
            return response()->json([
                'error' => 'Token SSO tidak ditemukan pada respons.',
            ], 400);
        }

        if ($driver === 'local') {
            $payload = $this->buildLocalPayload($data);
        } else {
            try {
                $payload = app(SsoUserInfoClient::class)->fetch($accessToken);
            } catch (UnauthorizedHttpException $e) {
                $request->session()->flush();
                return response()->json([
                    'error' => $e->getMessage(),
                ], 401);
            }
        }

        $appSlug = config('sso.app_slug');
        $apps = $payload['apps_allowed'] ?? [];
        if ($appSlug && (!is_array($apps) || !in_array($appSlug, $apps, true))) {
            $request->session()->flush();
            return response()->json([
                'error' => 'Aplikasi tidak diizinkan untuk token ini.',
            ], 403);
        }

        $role = $this->resolveLocalRole($payload);
        $photo = $payload['picture'] ?? null;

        $localUser = $this->syncLocalUser($payload, $role);
        $role = $localUser->role ?? $role;

        session([
            'user_id'    => $payload['sub'] ?? null,
            'user_name'  => $payload['name'] ?? null,
            'user_email' => $payload['email'] ?? null,
            'user_role'  => $role,
            'user_token' => $accessToken,
            'user_photo' => $photo,
            'user_payload' => $payload,
        ]);

        $this->claimPastDonations(
            userId: session('user_id'),
            email: session('user_email'),
            phone: $payload['phone'] ?? null,
            token: $accessToken
        );

        $redirectUrl = $role === 'admin'
            ? route('dashboard')
            : route('home');

        return response()->json([
            'message'      => 'Login berhasil!',
            'redirect_url' => $redirectUrl,
            'token'        => $accessToken,
            'user'         => [
                'id'    => session('user_id'),
                'name'  => session('user_name'),
                'email' => session('user_email'),
                'role'  => $role,
                'photo' => $photo,
            ],
        ]);
    }

    private function makeApiRequest(array $data)
    {
        $url = rtrim(config('sso.management_base_url'), '/').'/api/auth/login';
        $request = Http::timeout(config('sso.timeout', 5));

        $accept = config('sso.accept_header');
        $request = $accept
            ? $request->withHeaders(['Accept' => $accept])
            : $request->acceptJson();

        try {
            return $request->post($url, $data);
        } catch (\Exception $e) {
            return Http::response([
                'message' => 'Gagal menghubungi server SSO.',
            ], 500);
        }
    }

    private function authenticateLocally(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return Http::response([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = hash('sha256', $user->id.'|'.Str::random(60));

        return Http::response([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
            'user' => [
                'sub'    => $user->sso_sub ?? $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role ?? 'admin',
            ],
        ], 200);
    }

    private function buildLocalPayload(array $data): array
    {
        $user = $data['user'] ?? [];
        $appSlug = config('sso.app_slug');

        return [
            'sub' => $user['sub'] ?? null,
            'email' => $user['email'] ?? null,
            'name' => $user['name'] ?? null,
            'apps_allowed' => $appSlug ? [$appSlug] : [],
        ];
    }

    private function resolveLocalRole(array $payload): string
    {
        $default = config('sso.default_role', 'user');
        $sub = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;

        $user = User::query()
            ->when($sub, fn ($q) => $q->where('sso_sub', $sub))
            ->when(!$sub && $email, fn ($q) => $q->orWhere('email', $email))
            ->first();

        return $user?->role ?? $default;
    }

    private function syncLocalUser(array $payload, string $role): User
    {
        $sub = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;

        $user = User::query()
            ->when($sub, fn ($q) => $q->where('sso_sub', $sub))
            ->when(!$sub && $email, fn ($q) => $q->orWhere('email', $email))
            ->first();

        if (!$user) {
            $user = new User();
            $user->sso_sub = $sub;
            $user->email = $email;
            $user->password = Hash::make(Str::random(32));
        }

        $user->name = $payload['name'] ?? $user->name;
        $user->sso_payload = $payload;
        $user->role = $user->role ?? $role;

        $user->save();

        return $user;
    }

    private function claimPastDonations(?int $userId, ?string $email, ?string $phone, ?string $token): void
    {
        if (!$userId || (!$email && !$phone)) {
            return;
        }

        // Cocokkan donasi yang belum diklaim berdasarkan email atau no_hp, tanpa menimpa klaim yang sudah ada.
        $candidateDonasiIds = DonasiKilau::query()
            ->where(function ($q) use ($email, $phone) {
                if ($email) {
                    $q->orWhere('email', $email);
                }

                if ($phone) {
                    $q->orWhere('no_hp', $phone);
                }
            })
            ->pluck('id');

        if ($candidateDonasiIds->isEmpty()) {
            return;
        }

        // Update histori yang belum punya external_user_id
        DonasiHistory::whereNull('external_user_id')
            ->whereIn('donasikilau_id', $candidateDonasiIds)
            ->update([
                'external_user_id' => $userId,
                'token' => $token,
            ]);
    }

    public function register() {
        return view('Auth.register');
    }

    public function logout(Request $request)
    {
        // Hapus semua data dari session
        $request->session()->flush();

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Logout berhasil!');
    }
    
}
