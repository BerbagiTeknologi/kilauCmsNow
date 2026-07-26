<?php

namespace App\Jobs;

use App\Services\IntegrationOutboxDeliveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverIntegrationOutboxMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 30;

    public function __construct(public readonly string $outboxId) {}

    public function handle(IntegrationOutboxDeliveryService $deliveryService): void
    {
        $result = $deliveryService->deliver($this->outboxId);

        if (($result['status'] ?? null) !== 'retry') {
            return;
        }

        self::dispatch($this->outboxId)
            ->onConnection((string) config('km12_service.outbox_connection', 'database'))
            ->onQueue((string) config('km12_service.outbox_queue', 'integrations'))
            ->delay($result['available_at']);
    }
}
