<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCmsProfileRequest;
use App\Models\DonasiHistory;
use App\Models\DonasiKilau;
use App\Models\ReferralCode;
use App\Models\User;
use App\Services\Auth\AuthServiceClient;
use App\Services\Auth\LocalAuthUserService;
use App\Services\LocalUserSession;
use App\Services\ReferralCodeService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRoute(Auth::user()));
        }

        return view('Auth.login');
    }

    public function loginProses(
        Request $request,
        AuthServiceClient $authServiceClient,
        LocalAuthUserService $localAuthUserService,
        LocalUserSession $localSession
    )
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $authData = $authServiceClient->login(
                strtolower($credentials['email']),
                $credentials['password'],
                'kilauCms-web'
            );
        } catch (AuthenticationException $e) {
            return back()
                ->withErrors(['email' => $e->getMessage()])
                ->onlyInput('email');
        }

        $authUser = $authData['user'] ?? null;
        $globalUserId = is_array($authUser) ? $this->globalUserId($authUser) : null;

        if (!is_array($authUser) || !$globalUserId || empty($authUser['email'])) {
            return back()
                ->withErrors(['email' => 'Payload user dari layanan auth tidak valid.'])
                ->onlyInput('email');
        }

        $mappingLookup = $authServiceClient->lookupMapping([
            'global_user_id' => $globalUserId,
            'app_name' => config('auth_service.app_name', 'kilauCms'),
        ]);

        $user = $localAuthUserService->syncFromAuthService($authUser, $mappingLookup);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->put([
            'auth_service_access_token' => $authData['access_token'] ?? null,
            'auth_service_user' => $authUser,
        ]);
        $localSession->put($request, $user);

        return redirect()->intended(route($this->dashboardRoute($user)));
    }

    public function getDataUsersProfile(
        Request $request,
        ReferralCodeService $referralCodeService,
        AuthServiceClient $authServiceClient,
        LocalAuthUserService $localAuthUserService,
        LocalUserSession $localSession
    ) {
        $externalUserId = $request->session()->get('global_user_id');

        if (!$externalUserId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $authUser = $authServiceClient->profile(
            $request->session()->get('auth_service_access_token')
        ) ?? $request->session()->get('auth_service_user');
        $authGlobalUserId = is_array($authUser) ? $this->globalUserId($authUser) : null;

        if ($authGlobalUserId === $externalUserId && !empty($authUser['email'])) {
            $localUser = $localAuthUserService->syncFromAuthService($authUser);
            $request->session()->put('auth_service_user', $authUser);
            $localSession->put($request, $localUser);
        } else {
            $localUser = User::query()->find($request->session()->get('local_user_id'));
        }

        $role = $request->session()->get('user_role', 'user');
        $level = $role === 'admin' ? 'admin' : 'donatur';
        $referralCode = $localUser
            ? $referralCodeService->getOrCreateForUser($localUser)
            : null;
        $referralValue = $referralCode?->code ?: (
            $request->session()->get('user_sub') ?? $request->session()->get('user_id')
        );
        $shareLink = $referralValue ? url('/?aff='.urlencode((string) $referralValue)) : null;
        $isEmployeeReferral = $referralCode?->referral_type === ReferralCode::TYPE_KILAU_EMPLOYEE;

        $historyQuery = DonasiHistory::query()
            ->where('external_user_id', $externalUserId);

        $summary = (clone $historyQuery)
            ->leftJoin('donasikilau as donasi', 'donasi.id', '=', 'donasi_histories.donasikilau_id')
            ->selectRaw('COUNT(*) as total_transaksi')
            ->selectRaw(
                'COALESCE(SUM(COALESCE(donasi_histories.total_donasi, donasi.total_donasi, 0)), 0) as total_nominal'
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN COALESCE(donasi.status_donasi, donasi_histories.status_donasi) = ? THEN 1 ELSE 0 END), 0) as jumlah_aktif',
                [DonasiKilau::DONASI_AKTIVE]
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN COALESCE(donasi.status_donasi, donasi_histories.status_donasi) = ? THEN 1 ELSE 0 END), 0) as jumlah_expired',
                [DonasiKilau::DONASI_EXPIRED]
            )
            ->first();

        $totalTransaksi = (int) ($summary->total_transaksi ?? 0);
        $jumlahAktif = (int) ($summary->jumlah_aktif ?? 0);
        $jumlahExpired = (int) ($summary->jumlah_expired ?? 0);
        $historySummary = [
            'total_transaksi' => $totalTransaksi,
            'total_nominal' => (float) ($summary->total_nominal ?? 0),
            'jumlah_aktif' => $jumlahAktif,
            'jumlah_pending' => max(0, $totalTransaksi - $jumlahAktif - $jumlahExpired),
            'jumlah_expired' => $jumlahExpired,
        ];

        $histories = $historyQuery
            ->select([
                'id',
                'donasikilau_id',
                'status_donasi',
                'total_donasi',
                'created_at',
            ])
            ->with([
                'donasikilau' => function ($q) {
                    $q->select('id', 'type_donasi', 'opsional_umum', 'id_program', 'total_donasi', 'status_donasi')
                      ->with(['program:id,judul']);
                }
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        $user = [
            'id' => $externalUserId,
            'nama' => $request->session()->get('user_name'),
            'email' => $request->session()->get('user_email'),
            'phone' => is_array($authUser) ? ($authUser['phone'] ?? null) : null,
            'level' => $level,
            'referral_code' => $referralValue,
            'referral_link' => $shareLink,
            'referral_type' => $referralCode?->referral_type ?? ReferralCode::TYPE_CMS_USER,
            'referral_type_label' => $isEmployeeReferral ? 'Karyawan Kilau' : 'User CMS',
            'jabatan' => $isEmployeeReferral ? $referralCode?->position_snapshot : null,
            'foto' => ($isEmployeeReferral ? $referralCode?->photo_url_snapshot : null)
                ?: $request->session()->get('user_photo'),
        ];

        $statusMap = [
            DonasiKilau::DONASI_PENDING => 'Pending',
            DonasiKilau::DONASI_AKTIVE => 'Aktif',
            DonasiKilau::DONASI_EXPIRED => 'Expired',
        ];

        $opsionalUmumMap = [
            DonasiKilau::OPSIONAL_UMUM_ZAKAT => 'Zakat',
            DonasiKilau::OPSIONAL_UMUM_INFAQ => 'Infaq',
        ];

        return view('Auth.profile', compact(
            'user',
            'histories',
            'historySummary',
            'statusMap',
            'opsionalUmumMap'
        ));
    }

    public function updateDataUsersProfile(
        UpdateCmsProfileRequest $request,
        AuthServiceClient $authServiceClient,
        LocalAuthUserService $localAuthUserService,
        LocalUserSession $localSession
    ) {
        try {
            $authUser = $authServiceClient->updateProfile(
                (string) $request->session()->get('auth_service_access_token'),
                $request->validated(),
            );
        } catch (AuthenticationException $exception) {
            return back()
                ->withErrors(['profile' => $exception->getMessage()])
                ->withInput();
        }

        if ($this->globalUserId($authUser) !== $request->session()->get('global_user_id')) {
            return back()
                ->withErrors(['profile' => 'Identitas profil dari layanan auth tidak sesuai.'])
                ->withInput();
        }

        $localUser = $localAuthUserService->syncFromAuthService($authUser);
        $request->session()->put('auth_service_user', $authUser);
        $localSession->put($request, $localUser);

        return redirect()
            ->route('getDataUsersProfile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function register()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRoute(Auth::user()));
        }

        return view('Auth.register');
    }

    public function registerProses(
        Request $request,
        AuthServiceClient $authServiceClient,
        LocalAuthUserService $localAuthUserService,
        LocalUserSession $localSession
    )
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!$authServiceClient->canUseInternalApi()) {
            return back()
                ->withErrors(['email' => 'Internal service key CMS belum valid atau layanan auth tidak tersedia.'])
                ->onlyInput('name', 'email');
        }

        try {
            $authData = $authServiceClient->register(
                $validated['name'],
                strtolower($validated['email']),
                $validated['password'],
                $request->string('password_confirmation')->toString(),
                'kilauCms-web'
            );
        } catch (AuthenticationException $e) {
            return back()
                ->withErrors(['email' => $e->getMessage()])
                ->onlyInput('name', 'email');
        }

        $authUser = $authData['user'] ?? null;
        $globalUserId = is_array($authUser) ? $this->globalUserId($authUser) : null;

        if (!is_array($authUser) || !$globalUserId || empty($authUser['email'])) {
            return back()
                ->withErrors(['email' => 'Payload user dari layanan auth tidak valid.'])
                ->onlyInput('name', 'email');
        }

        $user = $localAuthUserService->syncFromAuthService($authUser);

        try {
            $mappingLookup = $authServiceClient->upsertMapping([
                'global_user_id' => $globalUserId,
                'app_name' => config('auth_service.app_name', 'kilauCms'),
                'local_database' => config('auth_service.local_database'),
                'local_user_table' => config('auth_service.local_user_table', 'users'),
                'local_user_id' => (string) $user->id,
                'email' => strtolower($validated['email']),
                'mapping_source' => 'register',
                'metadata' => [
                    'role' => 'user',
                ],
            ]);
        } catch (AuthenticationException $e) {
            return back()
                ->withErrors(['email' => $e->getMessage()])
                ->onlyInput('name', 'email');
        }

        $user = $localAuthUserService->syncFromAuthService($authUser, $mappingLookup);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put([
            'auth_service_access_token' => $authData['access_token'] ?? null,
            'auth_service_user' => $authUser,
        ]);
        $localSession->put($request, $user);

        return redirect()->route('home')->with('success', 'Registrasi berhasil.');
    }

    public function logout(Request $request, AuthServiceClient $authServiceClient)
    {
        $authServiceClient->logout($request->session()->get('auth_service_access_token'));

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function globalUserId(array $authUser): ?string
    {
        $globalUserId = trim((string) ($authUser['global_user_id'] ?? $authUser['id'] ?? ''));

        return Str::isUuid($globalUserId) ? $globalUserId : null;
    }

    private function dashboardRoute(User $user): string
    {
        return $user->role === 'admin' ? 'dashboard' : 'home';
    }
}
