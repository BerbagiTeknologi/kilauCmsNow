<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CmsAuthFeedbackTest extends TestCase
{
    private const GLOBAL_USER_ID = '11111111-1111-4111-8111-111111111111';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'auth_service.url' => 'https://kilau-auth.test',
            'auth_service.internal_key' => 'test-internal-key',
            'auth_service.local_database' => 'test_cms',
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);
        DB::purge('mysql');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->uuid('global_user_id')->nullable()->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('sso_sub')->nullable();
            $table->string('role')->default('user');
            $table->json('sso_payload')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Auth::logout();
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_login_shows_an_indonesian_error_when_sso_cannot_be_reached(): void
    {
        Http::fake([
            'https://kilau-auth.test/api/login' => Http::failedConnection(),
        ]);

        $this->from('/login')
            ->post('/loginprosess', [
                'email' => 'donor@example.test',
                'password' => 'secure-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'auth' => 'Tidak dapat terhubung ke layanan SSO. Coba lagi beberapa saat.',
            ]);

        $this->assertGuest();
    }

    public function test_login_translates_invalid_credentials_from_sso(): void
    {
        Http::fake([
            'https://kilau-auth.test/api/login' => Http::response([
                'errors' => ['email' => ['The provided credentials are incorrect.']],
            ], 422),
        ]);

        $this->from('/login')
            ->post('/loginprosess', [
                'email' => 'donor@example.test',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors(['email' => 'Email atau password salah.']);
    }

    public function test_registration_explains_invalid_internal_key_without_creating_a_local_account(): void
    {
        Http::fake([
            'https://kilau-auth.test/api/internal/register' => Http::response([
                'message' => 'Invalid internal service key.',
            ], 401),
        ]);

        $this->from('/register')
            ->post('/register', $this->registrationData())
            ->assertRedirect('/register')
            ->assertSessionHasErrors([
                'auth' => 'Registrasi belum tersedia karena konfigurasi SSO bermasalah. Hubungi admin.',
            ]);

        $this->assertDatabaseCount('users', 0);
        Http::assertSentCount(1);
    }

    public function test_registration_shows_a_connection_error_without_creating_a_local_account(): void
    {
        Http::fake([
            'https://kilau-auth.test/api/internal/register' => Http::failedConnection(),
        ]);

        $this->from('/register')
            ->post('/register', $this->registrationData())
            ->assertRedirect('/register')
            ->assertSessionHasErrors([
                'auth' => 'Tidak dapat terhubung ke layanan SSO. Coba lagi beberapa saat.',
            ]);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_translates_an_email_already_registered_in_sso(): void
    {
        Http::fake([
            'https://kilau-auth.test/api/internal/register' => Http::response([
                'errors' => ['email' => ['The email has already been taken.']],
            ], 422),
        ]);

        $this->from('/register')
            ->post('/register', $this->registrationData())
            ->assertRedirect('/register')
            ->assertSessionHasErrors([
                'email' => 'Email sudah terdaftar di SSO. Silakan login.',
            ]);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_rejects_mismatched_password_before_contacting_sso(): void
    {
        Http::fake();

        $data = $this->registrationData();
        $data['password_confirmation'] = 'different-password';

        $this->from('/register')
            ->post('/register', $data)
            ->assertRedirect('/register')
            ->assertSessionHasErrors(['password' => 'Konfirmasi password tidak cocok.']);

        Http::assertNothingSent();
    }

    public function test_registration_redirects_to_login_if_sso_account_was_created_but_mapping_failed(): void
    {
        Http::fake([
            'https://kilau-auth.test/api/internal/register' => Http::response([
                'data' => $this->authData(),
            ], 201),
            'https://kilau-auth.test/api/internal/user-mappings' => Http::response([
                'message' => 'Service unavailable.',
            ], 503),
        ]);

        $this->from('/register')
            ->post('/register', $this->registrationData())
            ->assertRedirect(route('login'))
            ->assertSessionHas('warning',
                'Akun SSO sudah dibuat, tetapi sinkronisasi CMS belum selesai. Silakan login dengan email dan password yang baru didaftarkan.'
            );

        $this->assertDatabaseHas('users', [
            'global_user_id' => self::GLOBAL_USER_ID,
            'email' => 'donor@example.test',
        ]);
        $this->assertGuest();
    }

    public function test_login_repairs_missing_cms_mapping_after_partial_registration(): void
    {
        User::query()->create([
            'global_user_id' => self::GLOBAL_USER_ID,
            'name' => 'Donor',
            'email' => 'donor@example.test',
            'password' => Hash::make('local-random-password'),
            'role' => 'user',
        ]);

        Http::fake([
            'https://kilau-auth.test/api/login' => Http::response([
                'data' => $this->authData(),
            ]),
            'https://kilau-auth.test/api/internal/user-mappings/lookup*' => Http::response([
                'data' => ['mappings' => []],
            ]),
            'https://kilau-auth.test/api/internal/user-mappings' => Http::response([
                'data' => ['mappings' => [[
                    'app_name' => 'kilauCms',
                    'metadata' => ['role' => 'user'],
                ]]],
            ]),
        ]);

        $this->from('/login')
            ->post('/loginprosess', [
                'email' => 'donor@example.test',
                'password' => 'secure-password',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHas('auth_service_access_token', 'test-token')
            ->assertSessionHas('global_user_id', self::GLOBAL_USER_ID);

        $this->assertAuthenticated();
        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST'
                && $request->url() === 'https://kilau-auth.test/api/internal/user-mappings'
                && $request['mapping_source'] === 'login'
                && $request['local_user_id'] === '1';
        });
    }

    private function registrationData(): array
    {
        return [
            'name' => 'Donor',
            'email' => 'donor@example.test',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ];
    }

    private function authData(): array
    {
        return [
            'access_token' => 'test-token',
            'user' => [
                'id' => self::GLOBAL_USER_ID,
                'global_user_id' => self::GLOBAL_USER_ID,
                'name' => 'Donor',
                'email' => 'donor@example.test',
            ],
        ];
    }
}
