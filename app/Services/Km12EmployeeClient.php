<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Km12EmployeeClient
{
    public function resolve(?string $globalUserId, ?string $email): ?array
    {
        $query = array_filter([
            'global_user_id' => $globalUserId,
            'email' => $email,
        ], fn ($value) => filled($value));

        if ($query === [] || ! $this->hasInternalKey()) {
            return null;
        }

        try {
            $response = $this->request()
                ->withHeader(
                    'X-Internal-Service-Key',
                    (string) config('km12_service.internal_key')
                )
                ->get($this->url('/api/internal/employees/resolve'), $query);
        } catch (\Throwable $exception) {
            Log::warning('KM12 employee resolver unavailable.', [
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('KM12 employee resolver returned failed response.', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return null;
        }

        return $response->json('data');
    }

    private function hasInternalKey(): bool
    {
        return (string) config('km12_service.internal_key') !== '';
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout((int) config('km12_service.timeout', 10));
    }

    private function url(string $path): string
    {
        return rtrim((string) config('km12_service.url'), '/').$path;
    }
}
