<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGuestDonorProfileRequest;
use App\Models\CmsGuestDonor;
use App\Services\DonationIdentityService;
use App\Services\GuestDonorProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class GuestDonorProfileController extends Controller
{
    public function update(
        UpdateGuestDonorProfileRequest $request,
        GuestDonorProfileService $profileService,
    ): JsonResponse {
        $guest = $this->guest($request);
        $guest = $profileService->update($guest, $request->validated());

        return response()->json(['data' => $this->format($guest)]);
    }

    public function destroy(
        Request $request,
        GuestDonorProfileService $profileService,
    ): JsonResponse {
        $profileService->anonymize($this->guest($request));
        Cookie::queue(Cookie::forget(DonationIdentityService::COOKIE_NAME, '/'));

        return response()->json(['message' => 'Guest donor profile anonymized.']);
    }

    private function guest(Request $request): CmsGuestDonor
    {
        $id = trim((string) $request->cookie(DonationIdentityService::COOKIE_NAME));
        if (! Str::isUuid($id)) {
            abort(404, 'Guest donor profile not found.');
        }

        $guest = CmsGuestDonor::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->whereNull('anonymized_at')
            ->first();

        return $guest ?: abort(404, 'Guest donor profile not found.');
    }

    private function format(CmsGuestDonor $guest): array
    {
        return [
            'id' => $guest->getKey(),
            'name' => $guest->name,
            'email' => $guest->email,
            'phone' => $guest->no_hp,
            'profile_version' => $guest->profile_version,
        ];
    }
}
