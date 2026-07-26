<?php

namespace App\Services;

use App\Models\DonasiKilau;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MidtransNotificationService
{
    public function __construct(
        private readonly DonationPaidPayloadFactory $payloadFactory,
        private readonly IntegrationOutboxService $outboxService,
    ) {}

    public function process(array $notification): array
    {
        $serverKey = $this->serverKey();

        $this->verifySignature($notification, $serverKey, 403);

        $donasiId = (int) substr($notification['order_id'], strlen('donasi-'));
        $donasi = DonasiKilau::query()->find($donasiId);

        if (! $donasi) {
            throw new HttpException(404, 'Donation not found.');
        }

        $status = $this->fetchStatus($notification['order_id'], $serverKey, $donasiId);
        $this->verifyStatusResponse($notification, $status, $serverKey);

        return $this->applyVerifiedStatus($donasi, $status);
    }

    public function verifyDonation(DonasiKilau $donasi): array
    {
        $serverKey = $this->serverKey();
        $orderId = 'donasi-'.$donasi->getKey();
        $status = $this->fetchStatus($orderId, $serverKey, (int) $donasi->getKey());

        $this->verifyQueriedStatusResponse($status, $orderId, $serverKey);

        return $this->applyVerifiedStatus($donasi, $status);
    }

    private function serverKey(): string
    {
        $serverKey = trim((string) config('services.midtrans.server_key'));

        if ($serverKey === '') {
            throw new HttpException(503, 'Midtrans server key is not configured.');
        }

        return $serverKey;
    }

    private function applyVerifiedStatus(DonasiKilau $donasi, array $status): array
    {
        if (! $this->sameAmount($donasi->total_donasi, $status['gross_amount'])) {
            throw new HttpException(422, 'Payment amount does not match the donation.');
        }

        $donasiId = (int) $donasi->getKey();
        $targetStatus = $this->donationStatus($status);
        $donasi = DB::transaction(function () use ($donasiId, $targetStatus): DonasiKilau {
            $lockedDonation = DonasiKilau::query()->lockForUpdate()->findOrFail($donasiId);
            $wasPaid = (int) $lockedDonation->status_donasi === DonasiKilau::DONASI_AKTIVE;

            if (
                $wasPaid
                && $targetStatus !== DonasiKilau::DONASI_AKTIVE
            ) {
                return $lockedDonation;
            }

            if ($targetStatus !== null && (int) $lockedDonation->status_donasi !== $targetStatus) {
                $lockedDonation->status_donasi = $targetStatus;
                $lockedDonation->save();
            }

            if (
                ! $wasPaid
                && $targetStatus === DonasiKilau::DONASI_AKTIVE
                && config('km12_service.donation_sync_enabled', false)
            ) {
                $this->outboxService->record(
                    'donation.paid',
                    'donation',
                    $lockedDonation->getKey(),
                    $this->payloadFactory->make($lockedDonation),
                );

                $lockedDonation->forceFill([
                    'km12_sync_status' => 'pending',
                    'km12_sync_error' => null,
                ])->save();
            }

            return $lockedDonation;
        });

        return [
            'donasi' => $donasi,
            'is_paid' => $targetStatus === DonasiKilau::DONASI_AKTIVE,
        ];
    }

    private function fetchStatus(string $orderId, string $serverKey, int $donasiId): array
    {
        $url = rtrim((string) config('services.midtrans.api_url'), '/')
            .'/v2/'.rawurlencode($orderId).'/status';

        try {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout((int) config('services.midtrans.timeout', 4))
                ->get($url);
        } catch (ConnectionException) {
            Log::warning('Verifikasi status Midtrans tidak tersedia.', [
                'donasi_id' => $donasiId,
            ]);

            throw new HttpException(503, 'Midtrans status verification is unavailable.');
        }

        if ($response->failed() || ! is_array($response->json())) {
            Log::warning('Verifikasi status Midtrans gagal.', [
                'donasi_id' => $donasiId,
                'http_status' => $response->status(),
            ]);

            throw new HttpException(503, 'Midtrans status verification failed.');
        }

        return $response->json();
    }

    private function verifyStatusResponse(array $notification, array $status, string $serverKey): void
    {
        $this->verifyQueriedStatusResponse($status, $notification['order_id'], $serverKey);

        if (! $this->sameAmount($status['gross_amount'], $notification['gross_amount'])) {
            throw new HttpException(422, 'Midtrans transaction does not match the notification.');
        }
    }

    private function verifyQueriedStatusResponse(array $status, string $orderId, string $serverKey): void
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'] as $field) {
            if (! isset($status[$field]) || ! is_string($status[$field])) {
                throw new HttpException(503, 'Midtrans status response is incomplete.');
            }
        }

        if ($status['order_id'] !== $orderId) {
            throw new HttpException(422, 'Midtrans transaction does not match the donation.');
        }

        $this->verifySignature($status, $serverKey, 503);
    }

    private function verifySignature(array $payload, string $serverKey, int $failureStatus): void
    {
        $expected = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].$serverKey
        );

        if (! hash_equals($expected, strtolower((string) $payload['signature_key']))) {
            throw new HttpException($failureStatus, 'Invalid Midtrans signature.');
        }
    }

    private function donationStatus(array $status): ?int
    {
        $transactionStatus = strtolower($status['transaction_status']);
        $fraudStatus = isset($status['fraud_status'])
            ? strtolower((string) $status['fraud_status'])
            : null;

        if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
            if ($status['status_code'] === '200' && in_array($fraudStatus, [null, 'accept'], true)) {
                return DonasiKilau::DONASI_AKTIVE;
            }

            return $fraudStatus === 'deny'
                ? DonasiKilau::DONASI_EXPIRED
                : DonasiKilau::DONASI_PENDING;
        }

        if (in_array($transactionStatus, ['pending', 'authorize'], true)) {
            return DonasiKilau::DONASI_PENDING;
        }

        if (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true)) {
            return DonasiKilau::DONASI_EXPIRED;
        }

        return null;
    }

    private function sameAmount(mixed $left, mixed $right): bool
    {
        $leftAmount = $this->amountInCents($left);
        $rightAmount = $this->amountInCents($right);

        return $leftAmount !== null && $leftAmount === $rightAmount;
    }

    private function amountInCents(mixed $amount): ?int
    {
        $amount = trim((string) $amount);

        if (! preg_match('/\A([0-9]{1,15})(?:\.([0-9]{1,2}))?\z/', $amount, $matches)) {
            return null;
        }

        $fraction = str_pad($matches[2] ?? '', 2, '0');

        return ((int) $matches[1] * 100) + (int) $fraction;
    }
}
