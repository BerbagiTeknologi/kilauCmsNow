<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login() {
        return view('Auth.login');
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

    public function loginProses(Request $request)
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
                    'user_name'  => $data['berhasil']['nama'],
                    'user_email' => $data['berhasil']['email'],
                    'user_role'  => $data['berhasil']['cms'] ?? null,  // bisa null
                    'user_token' => $data['token'],
                    'user_level' => $data['berhasil']['level'],
                    'user_referral_code' => $data['berhasil']['referral_code'] ?? null,
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
                        'name' => session('user_name'),
                        'level' => session('user_level'),
                        'referral_code' => session('user_referral_code'),
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