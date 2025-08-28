<?php

namespace App\Http\Controllers\Auth;

use App\Models\DonasiKilau;
use Illuminate\Http\Request;
use App\Models\DonasiHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

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

    /* public function loginProses(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $response = $this->makeApiRequest([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->status() == 200) {
            $data = $response->json();

            // Debug response dari API eksternal
            // dd($data);

            if (isset($data['token'])) {
                // Simpan data user ke session
                session([
                    // 'user_id' => $data['berhasil']['id'],
                    'user_name' => $data['berhasil']['nama'],
                    'user_email' => $data['berhasil']['email'],
                    'user_role' => $data['berhasil']['cms'] ?? 'admin',
                    'user_token' => $data['token'],
                ]);

                // Redirect ke dashboard
                return response()->json([
                    'message' => 'Login berhasil!',
                    'redirect_url' => route('dashboardlogin'),
                ]);
            }

            return response()->json([
                'error' => 'Akun Anda tidak memiliki token.',
            ], 400);
        }

        return response()->json([
            'error' => $response->json()['message'] ?? 'Login gagal.',
        ], $response->status());
    } */

     /* private function makeApiRequest(array $data)
    {
        try {
            return Http::post('https://kilauindonesia.org/api/login_sso', $data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghubungi server eksternal.'], 500);
        }
    } */

    /* public function loginProses(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
    
        $response = $this->makeApiRequest([
            'email' => $request->email,
            'password' => $request->password,
        ]);
    
        if ($response->status() == 200) {
            $data = $response->json();
    
            if (isset($data['token'])) {
                // Simpan data ke session
                session([
                    'user_id'    => $data['berhasil']['id'], 
                    'user_name'  => $data['berhasil']['nama'],
                    'user_email' => $data['berhasil']['email'],
                    'user_role'  => $data['berhasil']['cms'] ?? null,  // bisa null
                    'user_token' => $data['token'],
                    'user_level' => $data['berhasil']['level'],
                    'user_referral_code' => $data['berhasil']['referral_code'] ?? null,
                    'user_photo'  => $data['berhasil']['foto'] ?? null,
                ]);
    
                // Hanya user dengan CMS = admin yang boleh ke dashboard
                $redirectUrl = ($data['berhasil']['cms'] === 'admin')
                    ? route('dashboard')
                    : route('home');
    
                return response()->json([
                    'message' => 'Login berhasil!',
                    'redirect_url' => $redirectUrl,
                    'token' => session('user_token'),
                    'user' => [
                         'id'            => $data['berhasil']['id'],      // NEW
                        'name'          => session('user_name'),
                        'email'         => session('user_email'),
                        'name' => session('user_name'),
                        'level' => session('user_level'),
                        'referral_code' => session('user_referral_code'),
                        'photo' => session('user_photo'), 
                    ],
                ]);
            }
    
            return response()->json([
                'error' => 'Your account does not contain a token.',
            ], 400);
        }
    
        return response()->json([
            'error' => $response->json()['message'] ?? 'Login failed.',
        ], $response->status());
    } */

    public function loginProses(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $response = $this->makeApiRequest([
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        if ($response->status() == 200) {
            $data = $response->json();

            if (isset($data['token'])) {
                $ok       = $data['berhasil'] ?? [];
                $fotoUmum = $ok['foto_users_umum'] ?? null;  // ← URL penuh dari API showUser
                $fotoOld  = $ok['foto'] ?? null;             // ← URL lama (upload)
                $chosen   = $fotoUmum ?: $fotoOld;           // ← prioritas usersumum

                // Simpan data ke session
                session([
                    'user_id'            => $ok['id'] ?? null,
                    'user_name'          => $ok['nama'] ?? null,
                    'user_email'         => $ok['email'] ?? null,
                    'user_role'          => $ok['cms'] ?? null,
                    'user_token'         => $data['token'],
                    'user_level'         => $ok['level'] ?? null,
                    'user_referral_code' => $ok['referral_code'] ?? null,
                    'user_photo'         => $chosen,          // ← yang dipakai navbar/avatar
                    'user_photo_umum'    => $fotoUmum,        // ← simpan juga kalau perlu
                    'user_photo_legacy'  => $fotoOld,
                ]);

                // Hanya CMS admin boleh ke dashboard
                $redirectUrl = ($ok['cms'] ?? null) === 'admin'
                    ? route('dashboard')
                    : route('home');

                return response()->json([
                    'message'      => 'Login berhasil!',
                    'redirect_url' => $redirectUrl,
                    'token'        => session('user_token'),
                    'user'         => [
                        'id'             => session('user_id'),
                        'name'           => session('user_name'),
                        'email'          => session('user_email'),
                        'level'          => session('user_level'),
                        'referral_code'  => session('user_referral_code'),
                        // kirim dua-duanya + satu pilihan final
                        'photo'              => $chosen,
                        'photo_users_umum'   => $fotoUmum,
                        'photo_legacy'       => $fotoOld,
                    ],
                ]);
            }

            return response()->json([
                'error' => 'Your account does not contain a token.',
            ], 400);
        }

        return response()->json([
            'error' => $response->json()['message'] ?? 'Login failed.',
        ], $response->status());
    }

    private function makeApiRequest(array $data)
    {
        try {
            return Http::post('https://kilauindonesia.org/api/login_sso', $data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghubungi server eksternal.'], 500);
        }
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