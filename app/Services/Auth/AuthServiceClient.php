<?php

namespace App\Services\Auth;

use Illuminate\Auth\AuthenticationException;
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

    public function canUseInternalApi(): bool
    {
        $internalKey = (string) config('auth_service.internal_key');

        if (!$this->hasInternalKey()) {
            return false;
        }

        try {
            $response = $this->request()
                ->withHeader('X-Internal-Service-Key', $internalKey)
                ->get($this->url('/api/internal/user-mappings/lookup'), [
                    'global_user_id' => '00000000-0000-0000-0000-000000000000',
                ]);
        } catch (\Throwable) {
            return false;
        }

        return $response->successful() || $response->status() === 404;
    }

    public function register(
        string $name,
        string $email,
        string $password,
        string $passwordConfirmation,
        string $deviceName = 'kilauCms'
    ): array {
        try {
            $response = $this->request()
                ->post($this->url('/api/register'), [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'password_confirmation' => $passwordConfirmation,
                    'device_name' => $deviceName,
                ]);
        } catch (\Throwable) {
            throw new AuthenticationException('Layanan auth sedang tidak tersedia.');
        }

        if ($response->status() === 422) {
            throw ValidationException::withMessages($response->json('errors') ?: [
                'email' => [$response->json('message', 'Registrasi ditolak oleh layanan auth.')],
            ]);
        }

        if ($response->failed()) {
            throw new AuthenticationException($response->json('message', 'Registrasi ditolak oleh layanan auth.'));
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
        } catch (\Throwable) {
            throw new AuthenticationException('Layanan auth sedang tidak tersedia.');
        }

        if ($response->status() === 422) {
            throw ValidationException::withMessages($response->json('errors') ?: [
                'email' => [$response->json('message', 'Email atau password salah.')],
            ]);
        }

        if ($response->failed()) {
            throw new AuthenticationException($response->json('message', 'Login ditolak oleh layanan auth.'));
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
