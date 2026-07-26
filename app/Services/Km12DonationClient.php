<?php

namespace App\Services;

use App\Exceptions\IntegrationDeliveryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Km12DonationClient
{
    public function send(array $payload): array
    {
        $secret = trim((string) config('km12_service.integration_secret'));
        $pathConfig = str_starts_with((string) ($payload['event_type'] ?? ''), 'donor.')
            ? 'km12_service.profile_path'
            : 'km12_service.donation_path';
        $defaultPath = $pathConfig === 'km12_service.profile_path'
            ? '/api/internal/cms/donor-profiles'
            : '/api/internal/cms/donations';
        $url = rtrim((string) config('km12_service.url'), '/')
            .(string) config($pathConfig, $defaultPath);

        if ($secret === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new IntegrationDeliveryException('CONFIGURATION_ERROR', false);
        }

        $body = json_encode(
            $payload,
            JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES,
        );
        $timestamp = (string) now()->timestamp;
        $nonce = (string) Str::uuid();
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $signature = hash_hmac('sha256', implode("\n", [
            'POST',
            $path,
            $timestamp,
            $nonce,
            hash('sha256', $body),
        ]), $secret);

        try {
            $response = Http::acceptJson()
                ->withBody($body, 'application/json')
                ->withHeaders([
                    'X-Integration-Timestamp' => $timestamp,
                    'X-Integration-Nonce' => $nonce,
                    'X-Integration-Signature' => $signature,
                ])
                ->withoutRedirecting()
                ->timeout((int) config('km12_service.timeout', 10))
                ->post($url);
        } catch (ConnectionException) {
            throw new IntegrationDeliveryException('CONNECTION_ERROR', true);
        }

        if ($response->failed()) {
            $status = $response->status();

            throw new IntegrationDeliveryException(
                $this->httpErrorCode($status),
                $status === 429 || $status >= 500,
                $status,
            );
        }

        $validResponse = str_starts_with((string) ($payload['event_type'] ?? ''), 'donor.')
            ? $response->json('data.event_id') === ($payload['event_id'] ?? null)
            : is_numeric($response->json('data.transaksi_id'));

        if (! $response->successful() || ! $validResponse) {
            throw new IntegrationDeliveryException('INVALID_RESPONSE', true, $response->status());
        }

        return [
            'http_status' => $response->status(),
            'data' => $response->json('data', []),
        ];
    }

    private function httpErrorCode(int $status): string
    {
        return match ($status) {
            401, 403 => 'AUTHENTICATION_ERROR',
            409 => 'IDEMPOTENCY_CONFLICT',
            422 => 'VALIDATION_ERROR',
            429 => 'RATE_LIMITED',
            default => $status >= 500 ? 'UPSTREAM_ERROR' : 'HTTP_ERROR',
        };
    }
}
