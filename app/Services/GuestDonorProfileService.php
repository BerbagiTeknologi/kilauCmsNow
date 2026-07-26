<?php

namespace App\Services;

use App\Models\CmsGuestDonor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuestDonorProfileService
{
    private const ANONYMIZED_NAME = 'Donatur Anonim';

    public function __construct(private readonly IntegrationOutboxService $outboxService) {}

    public function update(CmsGuestDonor $guest, array $attributes): CmsGuestDonor
    {
        return DB::transaction(function () use ($guest, $attributes): CmsGuestDonor {
            $locked = CmsGuestDonor::query()->lockForUpdate()->findOrFail($guest->getKey());
            if (! $locked->is_active || $locked->anonymized_at !== null) {
                throw ValidationException::withMessages([
                    'profile' => ['Profil guest sudah tidak aktif.'],
                ]);
            }

            $locked->fill($attributes);
            if (! $locked->isDirty(['name', 'email', 'no_hp'])) {
                return $locked;
            }

            $locked->profile_version++;
            $locked->save();
            $this->record($locked, 'donor.profile_updated');

            return $locked->refresh();
        });
    }

    public function anonymize(CmsGuestDonor $guest): CmsGuestDonor
    {
        return DB::transaction(function () use ($guest): CmsGuestDonor {
            $locked = CmsGuestDonor::query()->lockForUpdate()->findOrFail($guest->getKey());
            if ($locked->anonymized_at !== null) {
                return $locked;
            }

            $locked->forceFill([
                'name' => self::ANONYMIZED_NAME,
                'email' => null,
                'no_hp' => null,
                'is_active' => false,
                'profile_version' => $locked->profile_version + 1,
                'anonymized_at' => now(),
            ])->save();
            $this->record($locked, 'donor.anonymized');

            return $locked->refresh();
        });
    }

    private function record(CmsGuestDonor $guest, string $eventType): void
    {
        $this->outboxService->record(
            $eventType,
            'guest_donor_profile',
            $guest->getKey().':'.$guest->profile_version,
            [
                'event_type' => $eventType,
                'source' => 'kilau_cms',
                'external_donor_id' => $guest->getKey(),
                'profile_version' => $guest->profile_version,
                'occurred_at' => now()->toIso8601String(),
                'profile' => [
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'phone' => $guest->no_hp,
                    'is_active' => $guest->is_active,
                    'anonymized' => $guest->anonymized_at !== null,
                ],
            ],
        );
    }
}
