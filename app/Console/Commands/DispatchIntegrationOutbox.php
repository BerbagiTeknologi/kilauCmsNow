<?php

namespace App\Console\Commands;

use App\Services\IntegrationOutboxService;
use Illuminate\Console\Command;

class DispatchIntegrationOutbox extends Command
{
    protected $signature = 'integration:outbox-dispatch {--limit=100}';

    protected $description = 'Kirim pesan outbox integrasi yang siap diproses';

    public function handle(IntegrationOutboxService $outboxService): int
    {
        if (! config('km12_service.donation_sync_enabled', false)) {
            $this->warn('Pengiriman donasi KM12 masih dinonaktifkan.');

            return self::SUCCESS;
        }

        $limit = max(1, min((int) $this->option('limit'), 1000));
        $count = $outboxService->dispatchPending($limit);
        $this->info("{$count} pesan outbox dijadwalkan.");

        return self::SUCCESS;
    }
}
