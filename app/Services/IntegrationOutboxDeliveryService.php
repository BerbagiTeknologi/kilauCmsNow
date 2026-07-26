<?php

namespace App\Services;

use App\Exceptions\IntegrationDeliveryException;
use App\Models\DonasiKilau;
use App\Models\IntegrationOutboxMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class IntegrationOutboxDeliveryService
{
    public function __construct(private readonly Km12DonationClient $client) {}

    public function deliver(string $id): array
    {
        if (! config('km12_service.donation_sync_enabled', false)) {
            return ['status' => 'disabled'];
        }

        $message = $this->claim($id);
        if (! $message) {
            return ['status' => 'skipped'];
        }

        try {
            $result = $this->client->send($message->payload);
        } catch (IntegrationDeliveryException $exception) {
            return $this->failClaim(
                $message,
                $exception->errorCode,
                $exception->retryable,
                $exception->httpStatus,
            );
        } catch (Throwable $exception) {
            Log::error('Pengiriman outbox integrasi mengalami kegagalan internal.', [
                'outbox_id' => $message->id,
                'event_type' => $message->event_type,
                'attempt' => $message->attempts,
                'exception_class' => $exception::class,
            ]);

            return $this->failClaim($message, 'UNEXPECTED_ERROR', true, null);
        }

        DB::transaction(function () use ($message, $result): void {
            $updated = IntegrationOutboxMessage::query()
                ->whereKey($message->id)
                ->where('lock_token', $message->lock_token)
                ->update([
                    'status' => IntegrationOutboxMessage::STATUS_DELIVERED,
                    'delivered_at' => now(),
                    'locked_at' => null,
                    'lock_token' => null,
                    'last_error_code' => null,
                    'last_http_status' => null,
                    'updated_at' => now(),
                ]);

            if ($updated === 1) {
                if ($message->event_type === 'donation.paid') {
                    $this->updateDonation($message, [
                        'km12_sync_status' => 'synced',
                        'km12_transaksi_id' => (int) $result['data']['transaksi_id'],
                        'km12_synced_at' => now(),
                        'km12_sync_error' => null,
                    ]);
                }
            }
        });

        Log::info('Outbox integrasi berhasil dikirim.', [
            'outbox_id' => $message->id,
            'event_type' => $message->event_type,
            'attempt' => $message->attempts,
            'http_status' => $result['http_status'],
        ]);

        return ['status' => 'delivered'];
    }

    private function claim(string $id): ?IntegrationOutboxMessage
    {
        return DB::transaction(function () use ($id): ?IntegrationOutboxMessage {
            $message = IntegrationOutboxMessage::query()->lockForUpdate()->find($id);
            if (! $message) {
                return null;
            }

            $staleBefore = now()->subSeconds((int) config('km12_service.outbox_lock_seconds', 600));
            $isPending = $message->status === IntegrationOutboxMessage::STATUS_PENDING
                && ($message->available_at === null || $message->available_at->lte(now()));
            $isStale = $message->status === IntegrationOutboxMessage::STATUS_PROCESSING
                && $message->locked_at?->lte($staleBefore);

            if (! $isPending && ! $isStale) {
                return null;
            }

            $message->forceFill([
                'status' => IntegrationOutboxMessage::STATUS_PROCESSING,
                'attempts' => $message->attempts + 1,
                'locked_at' => now(),
                'lock_token' => (string) Str::uuid(),
            ])->save();

            $this->updateDonation($message, [
                'km12_sync_status' => 'syncing',
                'km12_sync_error' => null,
            ]);

            return $message->fresh();
        });
    }

    private function failClaim(
        IntegrationOutboxMessage $message,
        string $errorCode,
        bool $retryable,
        ?int $httpStatus,
    ): array {
        $maxAttempts = max(1, (int) config('km12_service.outbox_max_attempts', 5));
        $deadLetter = ! $retryable || $message->attempts >= $maxAttempts;
        $availableAt = $deadLetter ? null : now()->addSeconds($this->retryDelay($message->attempts));

        DB::transaction(function () use (
            $message,
            $deadLetter,
            $availableAt,
            $errorCode,
            $httpStatus,
        ): void {
            $updated = IntegrationOutboxMessage::query()
                ->whereKey($message->id)
                ->where('lock_token', $message->lock_token)
                ->update([
                    'status' => $deadLetter
                        ? IntegrationOutboxMessage::STATUS_DEAD_LETTER
                        : IntegrationOutboxMessage::STATUS_PENDING,
                    'available_at' => $availableAt,
                    'locked_at' => null,
                    'lock_token' => null,
                    'dead_lettered_at' => $deadLetter ? now() : null,
                    'last_error_code' => $errorCode,
                    'last_http_status' => $httpStatus,
                    'updated_at' => now(),
                ]);

            if ($updated === 1) {
                $this->updateDonation($message, [
                    'km12_sync_status' => $deadLetter ? 'failed' : 'pending',
                    'km12_sync_error' => $errorCode,
                ]);
            }
        });

        Log::warning('Pengiriman outbox integrasi gagal.', [
            'outbox_id' => $message->id,
            'event_type' => $message->event_type,
            'attempt' => $message->attempts,
            'error_code' => $errorCode,
            'http_status' => $httpStatus,
            'dead_letter' => $deadLetter,
        ]);

        return [
            'status' => $deadLetter ? 'dead_letter' : 'retry',
            'available_at' => $availableAt,
        ];
    }

    private function retryDelay(int $attempt): int
    {
        $delays = array_values((array) config(
            'km12_service.outbox_retry_delays',
            [60, 300, 900, 3600, 21600],
        ));
        if ($delays === []) {
            $delays = [60, 300, 900, 3600, 21600];
        }

        return max(1, (int) ($delays[min(max($attempt - 1, 0), count($delays) - 1)] ?? 60));
    }

    private function updateDonation(IntegrationOutboxMessage $message, array $values): void
    {
        if ($message->event_type !== 'donation.paid' || $message->aggregate_type !== 'donation') {
            return;
        }

        DonasiKilau::query()->whereKey($message->aggregate_id)->update($values);
    }
}
