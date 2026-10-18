<?php

namespace Tests\Feature\Auth;

use App\Services\Auth\AuthServiceClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthServiceClientTest extends TestCase
{
    public function test_registration_uses_internal_endpoint_and_service_key(): void
    {
        config([
            'auth_service.url' => 'https://auth.example.test',
            'auth_service.internal_key' => 'test-internal-service-key',
        ]);
        Http::fake([
            'https://auth.example.test/api/internal/register' => Http::response([
                'data' => [
                    'access_token' => 'test-token',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => '00000000-0000-0000-0000-000000000001',
                        'email' => 'donor@example.test',
                    ],
                ],
            ], 201),
        ]);

        $result = app(AuthServiceClient::class)->register(
            'New Donor',
            'donor@example.test',
            'secure-password',
            'secure-password',
            'kilauCms-web',
        );

        $this->assertSame('donor@example.test', $result['user']['email']);
        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://auth.example.test/api/internal/register'
                && $request->hasHeader('X-Internal-Service-Key', 'test-internal-service-key')
                && $request['email'] === 'donor@example.test'
                && $request['device_name'] === 'kilauCms-web';
        });
    }
}
