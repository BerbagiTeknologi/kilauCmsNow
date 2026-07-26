<?php

namespace App\Services;

use App\Models\DonasiKilau;
use App\Models\IntegrationOutboxMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DonationBackfillService
{
    public function __construct(
        private readonly DonationPaidPayloadFactory $payloadFactory,
        private readonly IntegrationOutboxService $outboxService,
    ) {}

    public function run(
        bool $apply = false,
        ?int $fromId = null,
        ?int $toId = null,
        int $limit = 1000,
    ): array {
        $donations = $this->query($fromId, $toId)
            ->limit(max(1, min($limit, 10000)))
            ->get();
        $outboxes = IntegrationOutboxMessage::query()
            ->where('event_type', 'donation.paid')
            ->where('aggregate_type', 'donation')
            ->whereIn('aggregate_id', $donations->modelKeys())
            ->get()
            ->keyBy('aggregate_id');
        $report = $this->emptyReport();
        $candidates = [];

        foreach ($donations as $donation) {
            $report['scanned']++;
            $identityType = $this->identityType($donation);

            if ($identityType === null) {
                $report['ineligible_identity']++;
                if ($donation->km12_sync_status === 'synced') {
                    $report['ineligible_marked_synced']++;
                }

                continue;
            }

            $report[$identityType === 'anonymous'
                ? 'eligible_anonymous'
                : 'eligible_identified']++;

            if ($donation->km12_sync_status === 'synced') {
                $report['already_synced']++;

                continue;
            }

            $outbox = $outboxes->get((string) $donation->getKey());
            if ($outbox) {
                $report[$this->outboxCategory($outbox)]++;

                continue;
            }

            $report['candidates']++;
            $candidates[] = (int) $donation->getKey();
        }

        if ($apply) {
            foreach ($candidates as $donationId) {
                if ($this->enqueue($donationId)) {
                    $report['enqueued']++;
                }
            }
        }

        return $report;
    }

    private function query(?int $fromId, ?int $toId): Builder
    {
        return DonasiKilau::query()
            ->where('status_donasi', DonasiKilau::DONASI_AKTIVE)
            ->when($fromId, fn (Builder $query): Builder => $query->where('id', '>=', $fromId))
            ->when($toId, fn (Builder $query): Builder => $query->where('id', '<=', $toId))
            ->orderBy('id');
    }

    private function enqueue(int $donationId): bool
    {
        return DB::transaction(function () use ($donationId): bool {
            $donation = DonasiKilau::query()->lockForUpdate()->find($donationId);
            if (! $donation
                || (int) $donation->status_donasi !== DonasiKilau::DONASI_AKTIVE
                || $this->identityType($donation) === null
                || $donation->km12_sync_status === 'synced') {
                return false;
            }

            $exists = IntegrationOutboxMessage::query()
                ->where('event_type', 'donation.paid')
                ->where('aggregate_type', 'donation')
                ->where('aggregate_id', (string) $donation->getKey())
                ->lockForUpdate()
                ->exists();
            if ($exists) {
                return false;
            }

            $this->outboxService->record(
                'donation.paid',
                'donation',
                $donation->getKey(),
                $this->payloadFactory->make($donation),
            );
            $donation->forceFill([
                'km12_sync_status' => 'pending',
                'km12_sync_error' => null,
            ])->save();

            return true;
        });
    }

    private function identityType(DonasiKilau $donation): ?string
    {
        if ($donation->is_anonymous) {
            return $donation->donor_source === null && $donation->external_donor_id === null
                ? 'anonymous'
                : null;
        }

        return in_array($donation->donor_source, [
            DonasiKilau::DONOR_SOURCE_KILAU_AUTH,
            DonasiKilau::DONOR_SOURCE_KILAU_CMS,
        ], true) && Str::isUuid($donation->external_donor_id)
            ? 'identified'
            : null;
    }

    private function outboxCategory(IntegrationOutboxMessage $outbox): string
    {
        return match ($outbox->status) {
            IntegrationOutboxMessage::STATUS_DELIVERED => 'outbox_delivered',
            IntegrationOutboxMessage::STATUS_DEAD_LETTER => 'outbox_dead_letter',
            default => 'outbox_pending',
        };
    }

    private function emptyReport(): array
    {
        return [
            'scanned' => 0,
            'eligible_identified' => 0,
            'eligible_anonymous' => 0,
            'ineligible_identity' => 0,
            'ineligible_marked_synced' => 0,
            'already_synced' => 0,
            'outbox_delivered' => 0,
            'outbox_pending' => 0,
            'outbox_dead_letter' => 0,
            'candidates' => 0,
            'enqueued' => 0,
        ];
    }
}
