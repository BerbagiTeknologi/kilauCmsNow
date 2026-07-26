<?php

namespace App\Console\Commands;

use App\Services\IntegrationOutboxService;
use Illuminate\Console\Command;

class RetryIntegrationOutbox extends Command
{
    protected $signature = 'integration:outbox-retry {id}';

    protected $description = 'Ulangi pengiriman pesan outbox integrasi secara manual';

    public function handle(IntegrationOutboxService $outboxService): int
    {
        if (! config('km12_service.donation_sync_enabled', false)) {
            $this->error('Pengiriman donasi KM12 masih dinonaktifkan.');

            return self::FAILURE;
        }

        if (! $outboxService->retry((string) $this->argument('id'))) {
            $this->error('Pesan outbox tidak ditemukan atau sudah terkirim.');

            return self::FAILURE;
        }

        $this->info('Pesan outbox dijadwalkan ulang.');

        return self::SUCCESS;
    }
}
