<?php

namespace App\Services;

use App\Models\CmsGuestDonor;
use App\Models\DonasiKilau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DonationIdentityService
{
    public const COOKIE_NAME = 'kilau_guest_donor_id';

    private const COOKIE_MINUTES = 60 * 24 * 365 * 5;

    public function __construct(private readonly GuestDonorProfileService $profileService) {}

    public function resolve(Request $request, array $profile): array
    {
        if ($request->boolean('is_anonymous')) {
            return [
                'source' => null,
                'external_id' => null,
                'is_anonymous' => true,
                'transaction_name' => 'Hamba Allah',
            ];
        }

        $user = $request->user();

        if ($user) {
            $globalUserId = trim((string) $user->global_user_id);

            if (! Str::isUuid($globalUserId)) {
                throw ValidationException::withMessages([
                    'identity' => ['Identitas global akun tidak valid. Silakan login ulang.'],
                ]);
            }

            return [
                'source' => DonasiKilau::DONOR_SOURCE_KILAU_AUTH,
                'external_id' => $globalUserId,
                'is_anonymous' => false,
                'transaction_name' => $profile['nama'],
            ];
        }

        $guestId = trim((string) $request->cookie(self::COOKIE_NAME));

        $guest = Str::isUuid($guestId)
            ? CmsGuestDonor::query()->find($guestId)
            : null;

        if (! $guest || ! $guest->is_active || $guest->anonymized_at !== null) {
            $guestId = (string) Str::uuid();
            $guest = CmsGuestDonor::query()->create([
                'id' => $guestId,
                'name' => $profile['nama'],
                'email' => $profile['email'] ?? null,
                'no_hp' => $profile['no_hp'] ?? null,
                'profile_version' => 1,
                'is_active' => true,
            ]);
        } else {
            $guest = $this->profileService->update($guest, [
                'name' => $profile['nama'],
                'email' => $profile['email'] ?? null,
                'no_hp' => $profile['no_hp'] ?? null,
            ]);
        }

        Cookie::queue(Cookie::make(
            self::COOKIE_NAME,
            $guestId,
            self::COOKIE_MINUTES,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax'
        ));

        return [
            'source' => DonasiKilau::DONOR_SOURCE_KILAU_CMS,
            'external_id' => $guestId,
            'is_anonymous' => false,
            'transaction_name' => $profile['nama'],
        ];
    }
}
