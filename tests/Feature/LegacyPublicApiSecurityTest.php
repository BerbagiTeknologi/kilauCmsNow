<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegacyPublicApiSecurityTest extends TestCase
{
    public function test_legacy_donation_advertisement_api_routes_are_not_registered(): void
    {
        $registeredUris = collect(Route::getRoutes())
            ->map(fn ($route): string => $route->uri())
            ->all();

        foreach ([
            'api/iklandonasi',
            'api/iklandonasi/{id}',
            'api/iklandonasi-create',
            'api/iklandonasi-edit/{id}',
            'api/iklandonasi-delete/{id}',
        ] as $uri) {
            $this->assertNotContains($uri, $registeredUris);
        }
    }
}
