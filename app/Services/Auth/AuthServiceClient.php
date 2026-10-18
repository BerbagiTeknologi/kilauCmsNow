<?php

namespace App\Services\Auth;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthServiceClient
{
    public function hasInternalKey(): bool
    {
        return (string) config('auth_service.internal_key') !== '';
    }

    public function register(
        string $name,
        string $email,
        string $password,
        string $passwordConfirmation,
        string $deviceName = 'kilauCms'
    ): array {
        $internalKey = (string) config('auth_service.internal_key');

        if ($internalKey === '') {
            throw new AuthenticationException('Registrasi belum tersedia karena konfigurasi SSO belum lengkap. Hubungi admin.');
        }

        try {
            $response = $this->request()
                ->withHeader('X-Internal-Service-Key', $internalKey)
                ->post($this->url('/api/internal/register'), [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'password_confirmation' => $passwordConfirmation,
                    'device_name' => $deviceName,
                ]);
        } catch (ConnectionException) {
            throw new AuthenticationException('Tidak dapat terhubung ke layanan SSO. Coba lagi beberapa saat.');
        }

        if ($response->status() === 422) {
            $emailErrors = $response->json('errors.email', []);

            throw ValidationException::withMessages(
                is_array($emailErrors) && in_array('The email has already been taken.', $emailErrors, true)
                    ? ['email' => ['Email sudah terdaftar di SSO. Silakan login.']]
                    : ['auth' => ['Data registrasi ditolak layanan SSO. Periksa kembali data Anda.']]
            );
        }

        if (in_array($response->status(), [401, 403], true)) {
            throw new AuthenticationException('Registrasi belum tersedia karena konfigurasi SSO bermasalah. Hubungi admin.');
        }

        if ($response->status() === 503 && $response->json('message') === 'Internal service key is not configured.') {
            throw new AuthenticationException('Registrasi belum tersedia karena konfigurasi SSO bermasalah. Hubungi admin.');
        }

        if ($response->serverError()) {
            throw new AuthenticationException('Layanan SSO sedang mengalami gangguan. Coba lagi beberapa saat.');
        }

        if ($response->failed()) {
            throw new AuthenticationException('Registrasi ditolak layanan SSO. Hubungi admin jika masalah berlanjut.');
        }

        return $response->json('data', []);
    }

    public function login(string $email, string $password, string $deviceName = 'kilauCms'): array
    {
        try {
            $response = $this->request()
                ->post($this->url('/api/login'), [
                    'email' => $email,
                    'password' => $password,
                    'device_name' => $deviceName,
                ]);
        } catch (ConnectionException) {
            throw new AuthenticationException('Tidak dapat terhubung ke layanan SSO. Coba lagi beberapa saat.');
        }

        if ($response->status() === 422) {
            $message = $response->json('errors.email.0') === 'The user account is inactive.'
                ? 'Akun tidak aktif. Hubungi admin.'
                : 'Email atau password salah.';

            throw ValidationException::withMessages(['email' => [$message]]);
        }

        if ($response->serverError()) {
            throw new AuthenticationException('Layanan SSO sedang mengalami gangguan. Coba lagi beberapa saat.');
        }

        if ($response->failed()) {
            throw new AuthenticationException('Login ditolak layanan SSO. Hubungi admin jika masalah berlanjut.');
        }

        return $response->json('data', []);
    }

    public function logout(?string $token): void
    {
        if (!$token) {
            return;
        }

        try {
            $this->request()
                ->withToken($token)
                ->post($this->url('/api/logout'));
        } catch (\Throwable) {
            Log::warning('Logout ke layanan auth gagal; session lokal tetap dibersihkan.');
        }
    }

    public function profile(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        try {
            $response = $this->request()
                ->withToken($token)
                ->get($this->url('/api/me'));
        } catch (\Throwable) {
            return null;
        }

        $profile = $response->successful() ? $response->json('data') : null;

        return is_array($profile) ? $profile : null;
    }

    public function updateProfile(string $token, array $profile): array
    {
        if ($token === '') {
            throw new AuthenticationException('Sesi layanan auth tidak tersedia. Silakan logout dan login kembali.');
        }

        try {
            $response = $this->request()
                ->withToken($token)
                ->patch($this->url('/api/me'), $profile);
        } catch (\Throwable) {
            throw new AuthenticationException('Layanan auth sedang tidak tersedia. Profil belum diubah.');
        }

        if ($response->status() === 401) {
            throw ValidationException::withMessages([
                'profile' => ['Sesi layanan auth telah berakhir. Silakan logout dan login kembali.'],
            ]);
        }

        if ($response->status() === 422) {
            throw ValidationException::withMessages($response->json('errors') ?: [
                'profile' => ['Data profil ditolak oleh layanan auth.'],
            ]);
        }

        if ($response->failed()) {
            throw new AuthenticationException('Pembaruan profil ditolak oleh layanan auth.');
        }

        $updatedProfile = $response->json('data');

        if (!is_array($updatedProfile) || empty($updatedProfile['name']) || empty($updatedProfile['email'])) {
            throw new AuthenticationException('Respons profil dari layanan auth tidak valid.');
        }

        return $updatedProfile;
    }

    public function lookupMapping(array $query): ?array
    {
        $internalKey = (string) config('auth_service.internal_key');

        if (!$this->hasInternalKey()) {
            return null;
        }

        try {
            $response = $this->request()
                ->withHeader('X-Internal-Service-Key', $internalKey)
                ->get($this->url('/api/internal/user-mappings/lookup'), $query);
        } catch (\Throwable) {
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        return $response->json('data');
    }

    public function upsertMapping(array $payload): array
    {
        $internalKey = (string) config('auth_service.internal_key');

        if (!$this->hasInternalKey()) {
            throw new AuthenticationException('Internal service key CMS belum dikonfigurasi.');
        }

        try {
            $response = $this->request()
                ->withHeader('X-Internal-Service-Key', $internalKey)
                ->post($this->url('/api/internal/user-mappings'), $payload);
        } catch (\Throwable) {
            throw new AuthenticationException('Mapping user ke layanan auth gagal.');
        }

        if ($response->status() === 422) {
            throw ValidationException::withMessages($response->json('errors') ?: [
                'email' => [$response->json('message', 'Mapping user ditolak oleh layanan auth.')],
            ]);
        }

        if ($response->failed()) {
            throw new AuthenticationException($response->json('message', 'Mapping user ditolak oleh layanan auth.'));
        }

        return $response->json('data', []);
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout((int) config('auth_service.timeout', 10));
    }

    private function url(string $path): string
    {
        return rtrim((string) config('auth_service.url'), '/').$path;
    }
}
