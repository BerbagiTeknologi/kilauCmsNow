<?php

namespace App\Services;

use App\Exceptions\IntegrationOutboxConflictException;
use App\Jobs\DeliverIntegrationOutboxMessage;
use App\Models\IntegrationOutboxMessage;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class IntegrationOutboxService
{
    public function record(
        string $eventType,
        string $aggregateType,
        string|int $aggregateId,
        array $payload,
    ): IntegrationOutboxMessage {
        $aggregateId = (string) $aggregateId;
        $created = false;

        try {
            $message = DB::transaction(function () use (
                $eventType,
                $aggregateType,
                $aggregateId,
                $payload,
                &$created,
            ): IntegrationOutboxMessage {
                $existing = IntegrationOutboxMessage::query()
                    ->where('event_type', $eventType)
                    ->where('aggregate_type', $aggregateType)
                    ->where('aggregate_id', $aggregateId)
                    ->lockForUpdate()
                    ->first();

                $eventId = $existing?->id ?: (string) Str::uuid();
                $payload['event_id'] = $eventId;
                $payloadHash = $this->payloadHash($payload);

                if ($existing) {
                    $this->assertPayloadHash($existing, $payloadHash);

                    return $existing;
                }

                $created = true;

                return IntegrationOutboxMessage::query()->create([
                    'id' => $eventId,
                    'event_type' => $eventType,
                    'aggregate_type' => $aggregateType,
                    'aggregate_id' => $aggregateId,
                    'payload' => $payload,
                    'payload_hash' => $payloadHash,
                    'status' => IntegrationOutboxMessage::STATUS_PENDING,
                    'attempts' => 0,
                    'available_at' => now(),
                ]);
            });
        } catch (QueryException $exception) {
            $created = false;
            $message = IntegrationOutboxMessage::query()
                ->where('event_type', $eventType)
                ->where('aggregate_type', $aggregateType)
                ->where('aggregate_id', $aggregateId)
                ->first();

            if (! $message) {
                throw $exception;
            }

            $payload['event_id'] = $message->id;
            $this->assertPayloadHash($message, $this->payloadHash($payload));
        }

        if ($created && config('km12_service.donation_sync_enabled', false)) {
            DB::afterCommit(function () use ($message): void {
                try {
                    $this->dispatch((string) $message->id);
                } catch (Throwable $exception) {
                    Log::warning('Dispatch awal outbox integrasi ditunda ke scheduler.', [
                        'outbox_id' => $message->id,
                        'event_type' => $message->event_type,
                        'exception_class' => $exception::class,
                    ]);
                }
            });
        }

        return $message;
    }

    public function dispatchPending(int $limit = 100): int
    {
        if (! config('km12_service.donation_sync_enabled', false)) {
            return 0;
        }

        $staleBefore = now()->subSeconds((int) config('km12_service.outbox_lock_seconds', 600));
        $ids = IntegrationOutboxMessage::query()
            ->where(function ($query) use ($staleBefore) {
                $query->where(function ($pending) {
                    $pending->where('status', IntegrationOutboxMessage::STATUS_PENDING)
                        ->where(function ($available) {
                            $available->whereNull('available_at')
                                ->orWhere('available_at', '<=', now());
                        });
                })->orWhere(function ($processing) use ($staleBefore) {
                    $processing->where('status', IntegrationOutboxMessage::STATUS_PROCESSING)
                        ->where('locked_at', '<=', $staleBefore);
                });
            })
            ->orderBy('created_at')
            ->limit(max(1, min($limit, 1000)))
            ->pluck('id');

        foreach ($ids as $id) {
            $this->dispatch((string) $id);
        }

        return $ids->count();
    }

    public function retry(string $id): bool
    {
        $retried = DB::transaction(function () use ($id): bool {
            $message = IntegrationOutboxMessage::query()->lockForUpdate()->find($id);

            if (! $message || in_array($message->status, [
                IntegrationOutboxMessage::STATUS_PROCESSING,
                IntegrationOutboxMessage::STATUS_DELIVERED,
            ], true)) {
                return false;
            }

            $message->forceFill([
                'status' => IntegrationOutboxMessage::STATUS_PENDING,
                'attempts' => 0,
                'available_at' => now(),
                'locked_at' => null,
                'lock_token' => null,
                'dead_lettered_at' => null,
                'last_error_code' => null,
                'last_http_status' => null,
            ])->save();

            return true;
        });

        if ($retried && config('km12_service.donation_sync_enabled', false)) {
            $this->dispatch($id);
        }

        return $retried;
    }

    public function retryAggregate(
        string $eventType,
        string $aggregateType,
        string|int $aggregateId,
    ): bool {
        $id = IntegrationOutboxMessage::query()
            ->where('event_type', $eventType)
            ->where('aggregate_type', $aggregateType)
            ->where('aggregate_id', (string) $aggregateId)
            ->value('id');

        return $id ? $this->retry((string) $id) : false;
    }

    private function dispatch(string $id): void
    {
        DeliverIntegrationOutboxMessage::dispatch($id)
            ->onConnection((string) config('km12_service.outbox_connection', 'database'))
            ->onQueue((string) config('km12_service.outbox_queue', 'integrations'));
    }

    private function assertPayloadHash(
        IntegrationOutboxMessage $message,
        string $payloadHash,
    ): void {
        if (! hash_equals((string) $message->payload_hash, $payloadHash)) {
            throw new IntegrationOutboxConflictException;
        }
    }

    private function payloadHash(array $payload): string
    {
        return hash('sha256', json_encode(
            $this->canonicalize($payload),
            JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES,
        ));
    }

    private function canonicalize(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (array_is_list($value)) {
            return array_map(fn (mixed $item): mixed => $this->canonicalize($item), $value);
        }

        ksort($value, SORT_STRING);

        return array_map(fn (mixed $item): mixed => $this->canonicalize($item), $value);
    }
}
