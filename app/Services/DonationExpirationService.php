<?php

namespace App\Services;

use App\Models\DonasiKilau;

class DonationExpirationService
{
    public function expirePendingOlderThanOneHour(): int
    {
        return DonasiKilau::where('status_donasi', DonasiKilau::DONASI_PENDING)
            ->where('created_at', '<', now()->subHour())
            ->update(['status_donasi' => DonasiKilau::DONASI_EXPIRED]);
    }
}
