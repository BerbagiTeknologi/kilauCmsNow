<?php

namespace App\Console\Commands;

use App\Services\DonationBackfillService;
use Illuminate\Console\Command;

class BackfillPaidDonations extends Command
{
    protected $signature = 'integration:donations-backfill
        {--apply : Catat kandidat ke transactional outbox}
        {--from-id= : ID donasi awal}
        {--to-id= : ID donasi akhir}
        {--limit=1000 : Maksimum donasi paid yang diperiksa}';

    protected $description = 'Audit atau antrekan backfill donasi paid dengan identitas eksternal eksplisit';

    public function handle(DonationBackfillService $backfillService): int
    {
        $fromId = $this->positiveOption('from-id');
        $toId = $this->positiveOption('to-id');
        $limit = $this->positiveOption('limit') ?? 1000;

        if ($this->option('from-id') !== null && $fromId === null
            || $this->option('to-id') !== null && $toId === null
            || $this->option('limit') !== null && $this->positiveOption('limit') === null
            || $fromId !== null && $toId !== null && $fromId > $toId) {
            $this->error('Rentang ID tidak valid.');

            return self::INVALID;
        }

        $apply = (bool) $this->option('apply');
        if ($apply && ! $this->confirm(
            'Mode apply akan mencatat kandidat ke transactional outbox. Lanjutkan?',
            false,
        )) {
            $this->warn('Tidak ada perubahan yang dilakukan.');

            return self::SUCCESS;
        }

        $report = $backfillService->run($apply, $fromId, $toId, $limit);
        $this->info($apply ? 'Mode: apply' : 'Mode: dry-run');
        $this->table(
            ['Kategori', 'Jumlah'],
            collect($report)->map(fn (int $count, string $key): array => [$key, $count])->values(),
        );

        if (! $apply) {
            $this->comment('Dry-run selesai tanpa menulis outbox atau data donasi.');
        }

        return self::SUCCESS;
    }

    private function positiveOption(string $name): ?int
    {
        $value = $this->option($name);
        if ($value === null || $value === '' || ! ctype_digit((string) $value)) {
            return null;
        }

        $value = (int) $value;

        return $value > 0 ? $value : null;
    }
}
