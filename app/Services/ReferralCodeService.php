<?php

namespace App\Services;

use App\Models\ReferralCode;
use App\Models\DonasiKilau;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ReferralCodeService
{
    public function __construct(private readonly Km12EmployeeClient $km12EmployeeClient)
    {
    }

    public function getOrCreateForUser(User $user): ?ReferralCode
    {
        if (! Schema::hasTable('referral_codes')) {
            return null;
        }

        return DB::transaction(function () use ($user): ReferralCode {
            $referral = ReferralCode::query()
                ->where('cms_user_id', $user->id)
                ->lockForUpdate()
                ->first();
            $employee = $this->km12EmployeeClient->resolve(
                $user->global_user_id,
                $user->email,
            );
            $isActiveEmployee = (bool) ($employee['is_employee'] ?? false)
                && (bool) ($employee['is_active'] ?? false);

            if (! $referral) {
                $referral = new ReferralCode([
                    'cms_user_id' => $user->id,
                    'code' => $this->generateCode(),
                ]);
            }

            $values = [
                'global_user_id' => $user->global_user_id,
                'referral_type' => $isActiveEmployee
                    ? ReferralCode::TYPE_KILAU_EMPLOYEE
                    : ReferralCode::TYPE_CMS_USER,
                'km12_user_id' => $isActiveEmployee ? ($employee['km12_user_id'] ?? null) : null,
                'karyawan_id' => $isActiveEmployee ? ($employee['karyawan_id'] ?? null) : null,
                'name_snapshot' => $isActiveEmployee
                    ? ($employee['name'] ?? $user->name)
                    : $user->name,
                'email_snapshot' => $isActiveEmployee
                    ? ($employee['email'] ?? $user->email)
                    : $user->email,
                'position_snapshot' => $isActiveEmployee
                    ? ($employee['position']['name'] ?? null)
                    : null,
                'is_active' => true,
                'employee_verified_at' => $isActiveEmployee ? now() : null,
                'synced_at' => now(),
            ];

            if (Schema::hasColumn('referral_codes', 'photo_url_snapshot')) {
                $values['photo_url_snapshot'] = $isActiveEmployee
                    ? ($employee['photo_url'] ?? null)
                    : null;
            }

            if ($employee === null && $referral->exists) {
                unset(
                    $values['referral_type'],
                    $values['km12_user_id'],
                    $values['karyawan_id'],
                    $values['name_snapshot'],
                    $values['email_snapshot'],
                    $values['position_snapshot'],
                    $values['photo_url_snapshot'],
                    $values['employee_verified_at'],
                    $values['synced_at']
                );
            }

            $referral->fill($values);

            $referral->save();

            return $referral;
        });
    }

    public function resolve(string $code): ?ReferralCode
    {
        $code = trim($code);

        if ($code === '' || ! Schema::hasTable('referral_codes')) {
            return null;
        }

        $referral = ReferralCode::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if ($referral?->user) {
            return $this->getOrCreateForUser($referral->user);
        }

        return $referral;
    }

    public function resolveInput(string $input): ?ReferralCode
    {
        $input = trim($input);

        if ($input === '' || ! Schema::hasTable('referral_codes')) {
            return null;
        }

        $referral = $this->resolve($input);

        if ($referral) {
            return $referral;
        }

        $user = User::query()
            ->where(function ($query) use ($input) {
                $query->where('global_user_id', $input)
                    ->orWhere('sso_sub', $input);

                if (ctype_digit($input)) {
                    $query->orWhere('id', (int) $input);
                }
            })
            ->first();

        return $user ? $this->getOrCreateForUser($user) : null;
    }

    public function applyToDonation(DonasiKilau $donasi, string $code): void
    {
        $code = trim($code);

        if ($code === '') {
            return;
        }

        if (Schema::hasColumn('donasikilau', 'affiliate_sub')) {
            $donasi->affiliate_sub = $code;
        }

        $referral = $this->resolveInput($code);

        if (! $referral || ! Schema::hasColumn('donasikilau', 'referral_code')) {
            return;
        }

        $values = [
            'referral_code' => $referral->code,
            'referral_type' => $referral->referral_type,
            'referral_cms_user_id' => $referral->cms_user_id,
            'referral_global_user_id' => $referral->global_user_id,
            'referral_km12_user_id' => $referral->km12_user_id,
            'referral_karyawan_id' => $referral->karyawan_id,
            'referral_name_snapshot' => $referral->name_snapshot,
            'referral_position_snapshot' => $referral->position_snapshot,
        ];

        foreach ($values as $column => $value) {
            if (Schema::hasColumn('donasikilau', $column)) {
                $donasi->{$column} = $value;
            }
        }
    }

    private function generateCode(): string
    {
        do {
            $code = 'KILAU'.Str::upper(Str::random(10));
        } while (ReferralCode::query()->where('code', $code)->exists());

        return $code;
    }
}
