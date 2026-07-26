<?php

namespace App\Services;

use App\Models\CmsGeneralDonationKm12Mapping;
use App\Models\CmsGuestDonor;
use App\Models\DonasiKilau;
use App\Models\User;

class DonationPaidPayloadFactory
{
    public function make(DonasiKilau $donasi): array
    {
        if ((int) $donasi->type_donasi === DonasiKilau::TYPE_DONASI_PROGRAM) {
            $donasi->loadMissing('program.km12Mapping');
        }
        $profile = $this->profile($donasi);
        $mapping = $this->programMapping($donasi);
        $identity = 'donasi-'.$donasi->getKey();

        return [
            'event_type' => 'donation.paid',
            'source' => 'kilau_cms',
            'source_transaction_id' => $identity,
            'reference' => $identity,
            'order_id' => $identity,
            'donasi_id' => (int) $donasi->getKey(),
            'status' => 'paid',
            'paid_at' => $donasi->updated_at?->toIso8601String(),
            'created_at' => $donasi->created_at?->toIso8601String(),
            'donor' => $this->donor($donasi, $profile),
            'donation' => [
                'type' => (int) $donasi->type_donasi === DonasiKilau::TYPE_DONASI_PROGRAM
                    ? 'program'
                    : 'general',
                'opsional_umum' => $this->generalDonationType($donasi),
                'cms_program_id' => $donasi->id_program ? (int) $donasi->id_program : null,
                'cms_program_title' => $this->nullableString($donasi->program?->judul),
                'program_penerimaan_id' => $mapping['program_penerimaan_id'],
                'sumber_dana_id' => $mapping['sumber_dana_id'],
                'amount' => (string) $donasi->total_donasi,
                'feedback' => $this->nullableString($donasi->feedback),
                'donor_name_snapshot' => $this->transactionName($donasi),
            ],
            'payment' => [
                'method' => 'Midtrans',
                'type' => null,
            ],
            'referral' => [
                'type' => $this->nullableString($donasi->referral_type),
                'code' => $this->nullableString($donasi->referral_code),
                'cms_user_id' => $donasi->referral_cms_user_id
                    ? (int) $donasi->referral_cms_user_id
                    : null,
                'global_user_id' => $this->nullableString($donasi->referral_global_user_id),
                'km12_user_id' => $donasi->referral_km12_user_id
                    ? (int) $donasi->referral_km12_user_id
                    : null,
                'karyawan_id' => $donasi->referral_karyawan_id
                    ? (int) $donasi->referral_karyawan_id
                    : null,
                'name_snapshot' => $this->nullableString($donasi->referral_name_snapshot),
                'position_snapshot' => $this->nullableString($donasi->referral_position_snapshot),
            ],
        ];
    }

    private function donor(DonasiKilau $donasi, array $profile): array
    {
        if ($donasi->is_anonymous) {
            return [
                'is_anonymous' => true,
                'name' => 'Hamba Allah',
                'email' => null,
                'phone' => null,
            ];
        }

        return [
            'source' => $donasi->donor_source,
            'external_id' => $donasi->external_donor_id,
            'is_anonymous' => false,
            'profile_version' => $profile['profile_version'],
            'name' => $profile['name'],
            'email' => $profile['email'],
            'phone' => $profile['phone'],
        ];
    }

    private function profile(DonasiKilau $donasi): array
    {
        $profile = null;

        if ($donasi->donor_source === DonasiKilau::DONOR_SOURCE_KILAU_CMS) {
            $guest = CmsGuestDonor::query()->find($donasi->external_donor_id);
            if ($guest) {
                $profile = [
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'phone' => $guest->no_hp,
                    'profile_version' => $guest->profile_version,
                ];
            }
        }

        if ($donasi->donor_source === DonasiKilau::DONOR_SOURCE_KILAU_AUTH) {
            $user = User::query()
                ->where('global_user_id', $donasi->external_donor_id)
                ->first();

            if ($user) {
                $profile = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => data_get($user->sso_payload, 'global_user.no_hp')
                        ?? data_get($user->sso_payload, 'global_user.phone')
                        ?? data_get($user->sso_payload, 'global_user.phone_number'),
                    'profile_version' => data_get(
                        $user->sso_payload,
                        'global_user.profile_version',
                        1,
                    ),
                ];
            }
        }

        return [
            'name' => $this->nullableString($profile['name'] ?? null)
                ?? $this->transactionName($donasi),
            'email' => $this->nullableString($profile['email'] ?? $donasi->email),
            'phone' => $this->nullableString($profile['phone'] ?? $donasi->no_hp),
            'profile_version' => max(1, (int) ($profile['profile_version'] ?? 1)),
        ];
    }

    private function programMapping(DonasiKilau $donasi): array
    {
        if ((int) $donasi->type_donasi === DonasiKilau::TYPE_DONASI_PROGRAM) {
            $mapping = $donasi->program?->km12Mapping;
        } else {
            $type = $this->generalDonationType($donasi);
            $mapping = $type
                ? CmsGeneralDonationKm12Mapping::query()
                    ->where('donation_type', $type)
                    ->where('is_active', true)
                    ->first()
                : null;
        }

        if (! $mapping || ! $mapping->is_active) {
            return [
                'program_penerimaan_id' => null,
                'sumber_dana_id' => null,
            ];
        }

        return [
            'program_penerimaan_id' => $mapping->km12_program_penerimaan_id
                ? (int) $mapping->km12_program_penerimaan_id
                : null,
            'sumber_dana_id' => $mapping->km12_sumber_dana_id
                ? (int) $mapping->km12_sumber_dana_id
                : null,
        ];
    }

    private function generalDonationType(DonasiKilau $donasi): ?string
    {
        if ((int) $donasi->type_donasi !== DonasiKilau::TYPE_DONASI_UMUM) {
            return null;
        }

        return match ((int) $donasi->opsional_umum) {
            DonasiKilau::OPSIONAL_UMUM_ZAKAT => CmsGeneralDonationKm12Mapping::TYPE_ZAKAT,
            DonasiKilau::OPSIONAL_UMUM_INFAQ => CmsGeneralDonationKm12Mapping::TYPE_INFAQ,
            default => null,
        };
    }

    private function transactionName(DonasiKilau $donasi): string
    {
        return $this->nullableString($donasi->nama) ?? 'Hamba Allah';
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
