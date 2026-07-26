<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Km12ProgramClient
{
    public function options(string $search = '', int $limit = 100): array
    {
        $internalKey = (string) config('km12_service.internal_key');

        if ($internalKey === '') {
            return [];
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout((int) config('km12_service.timeout', 10))
                ->withHeader('X-Internal-Service-Key', $internalKey)
                ->get(rtrim((string) config('km12_service.url'), '/').'/api/internal/program-penerimaan/options', [
                    'search' => $search,
                    'limit' => $limit,
                ]);
        } catch (\Throwable $exception) {
            Log::warning('KM12 program options unavailable.', [
                'message' => $exception->getMessage(),
            ]);

            return [];
        }

        if ($response->failed()) {
            Log::warning('KM12 program options returned failed response.', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return [];
        }

        return $response->json('data', []);
    }
}
