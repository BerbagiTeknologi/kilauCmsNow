<?php

namespace Tests\Feature;

use App\Models\DonasiKilau;
use App\Models\User;
use App\Services\Auth\LocalAuthUserService;
use App\Services\DonationIdentityService;
use App\Models\IntegrationOutboxMessage;
use App\Services\ReferralCodeService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class DonationIdentityStageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'app.maintenance.driver' => 'file',
            'km12_service.donation_sync_enabled' => false,
        ]);

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

        Schema::create('cms_guest_donors', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->unsignedBigInteger('profile_version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamp('anonymized_at')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_outbox_messages', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('event_type');
            $table->string('aggregate_type');
            $table->string('aggregate_id');
            $table->longText('payload');
            $table->char('payload_hash', 64);
            $table->string('status');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->timestamp('available_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->uuid('lock_token')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('dead_lettered_at')->nullable();
            $table->string('last_error_code')->nullable();
            $table->unsignedSmallInteger('last_http_status')->nullable();
            $table->timestamps();

            $table->unique(['event_type', 'aggregate_type', 'aggregate_id']);
        });

        Schema::create('donasikilau', function (Blueprint $table): void {
            $table->id();
            $table->string('type_donasi')->nullable();
            $table->string('opsional_umum')->nullable();
            $table->unsignedBigInteger('id_program')->nullable();
            $table->string('nama')->nullable();
            $table->decimal('total_donasi', 15, 2)->nullable();
            $table->string('status_donasi')->nullable();
            $table->text('feedback')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('donor_source', 32)->nullable();
            $table->char('external_donor_id', 36)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
        });

        Schema::create('donasi_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('donasikilau_id');
            $table->char('external_user_id', 36)->nullable()->index();
            $table->string('status_donasi')->nullable();
            $table->decimal('total_donasi', 15, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->text('token')->nullable();
            $table->timestamps();
        });

        $referralCodeService = Mockery::mock(ReferralCodeService::class);
        $referralCodeService->shouldNotReceive('applyToDonation');
        $this->app->instance(ReferralCodeService::class, $referralCodeService);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('donasi_histories');
        Schema::dropIfExists('donasikilau');
        Schema::dropIfExists('integration_outbox_messages');
        Schema::dropIfExists('cms_guest_donors');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_guest_uuid_is_stable_without_matching_pii(): void
    {
        $firstResponse = $this->postJson('/donasi', $this->payload([
            'nama' => 'Guest Pertama',
            'email' => 'guest-first@example.test',
        ]))->assertOk()->assertCookie(DonationIdentityService::COOKIE_NAME);

        $firstDonationId = $firstResponse->json('donasi_id');
        $guestId = DB::table('donasikilau')->where('id', $firstDonationId)->value('external_donor_id');

        $this->assertTrue(Str::isUuid($guestId));
        $this->assertDatabaseHas('cms_guest_donors', [
            'id' => $guestId,
            'name' => 'Guest Pertama',
        ]);

        $secondResponse = $this
            ->withCredentials()
            ->withCookie(DonationIdentityService::COOKIE_NAME, $guestId)
            ->postJson('/donasi', $this->payload([
                'nama' => 'Guest Terbaru',
                'email' => null,
            ]))
            ->assertOk();

        $this->assertDatabaseHas('donasikilau', [
            'id' => $secondResponse->json('donasi_id'),
            'donor_source' => DonasiKilau::DONOR_SOURCE_KILAU_CMS,
            'external_donor_id' => $guestId,
            'is_anonymous' => false,
        ]);
        $this->assertDatabaseHas('cms_guest_donors', [
            'id' => $guestId,
            'name' => 'Guest Terbaru',
            'email' => null,
            'profile_version' => 2,
        ]);
        $this->assertDatabaseHas('integration_outbox_messages', [
            'event_type' => 'donor.profile_updated',
            'aggregate_id' => $guestId.':2',
        ]);
        $this->assertSame(1, DB::table('cms_guest_donors')->count());
        $this->assertDatabaseHas('donasi_histories', [
            'donasikilau_id' => $secondResponse->json('donasi_id'),
            'external_user_id' => $guestId,
        ]);
    }

    public function test_authenticated_donation_uses_global_user_id(): void
    {
        $globalUserId = (string) Str::uuid();
        $user = $this->createUser($globalUserId);

        $response = $this->actingAs($user)
            ->postJson('/donasi', $this->payload())
            ->assertOk();

        $this->assertDatabaseHas('donasikilau', [
            'id' => $response->json('donasi_id'),
            'donor_source' => DonasiKilau::DONOR_SOURCE_KILAU_AUTH,
            'external_donor_id' => $globalUserId,
            'is_anonymous' => false,
        ]);
        $this->assertDatabaseHas('donasi_histories', [
            'donasikilau_id' => $response->json('donasi_id'),
            'external_user_id' => $globalUserId,
        ]);
        $this->assertDatabaseCount('cms_guest_donors', 0);
    }

    public function test_guest_profile_can_clear_contacts_and_be_anonymized(): void
    {
        $response = $this->postJson('/donasi', $this->payload())->assertOk();
        $guestId = DB::table('donasikilau')
            ->where('id', $response->json('donasi_id'))
            ->value('external_donor_id');

        $this->withCredentials()
            ->withCookie(DonationIdentityService::COOKIE_NAME, $guestId)
            ->patchJson('/guest-donor-profile', [
                'name' => 'Guest Lifecycle',
                'email' => null,
                'no_hp' => null,
            ])->assertOk()
            ->assertJsonPath('data.profile_version', 2);

        $this->withCredentials()
            ->withCookie(DonationIdentityService::COOKIE_NAME, $guestId)
            ->deleteJson('/guest-donor-profile')
            ->assertOk()
            ->assertCookieExpired(DonationIdentityService::COOKIE_NAME);

        $this->assertDatabaseHas('cms_guest_donors', [
            'id' => $guestId,
            'name' => 'Donatur Anonim',
            'email' => null,
            'no_hp' => null,
            'is_active' => false,
            'profile_version' => 3,
        ]);
        $this->assertDatabaseHas('integration_outbox_messages', [
            'event_type' => 'donor.anonymized',
            'aggregate_id' => $guestId.':3',
        ]);
    }

    public function test_guests_with_same_profile_are_not_merged_without_cookie(): void
    {
        $firstDonationId = $this->postJson('/donasi', $this->payload())
            ->assertOk()
            ->json('donasi_id');
        $secondDonationId = $this->postJson('/donasi', $this->payload())
            ->assertOk()
            ->json('donasi_id');

        $firstGuestId = DB::table('donasikilau')
            ->where('id', $firstDonationId)
            ->value('external_donor_id');
        $secondGuestId = DB::table('donasikilau')
            ->where('id', $secondDonationId)
            ->value('external_donor_id');

        $this->assertNotSame($firstGuestId, $secondGuestId);
        $this->assertDatabaseCount('cms_guest_donors', 2);
    }

    public function test_cms_sync_prefers_explicit_global_user_id_contract(): void
    {
        $globalUserId = (string) Str::uuid();

        $user = app(LocalAuthUserService::class)->syncFromAuthService([
            'id' => (string) Str::uuid(),
            'global_user_id' => $globalUserId,
            'name' => 'Pengguna Auth',
            'email' => 'auth-contract@example.test',
        ]);

        $this->assertSame($globalUserId, $user->global_user_id);
    }

    public function test_anonymous_donation_has_no_external_identity_or_profile(): void
    {
        $user = $this->createUser((string) Str::uuid());

        $response = $this->actingAs($user)
            ->postJson('/donasi', $this->payload(['is_anonymous' => true]))
            ->assertOk();

        $this->assertDatabaseHas('donasikilau', [
            'id' => $response->json('donasi_id'),
            'nama' => 'Hamba Allah',
            'donor_source' => null,
            'external_donor_id' => null,
            'is_anonymous' => true,
        ]);
        $this->assertDatabaseHas('donasi_histories', [
            'donasikilau_id' => $response->json('donasi_id'),
            'external_user_id' => null,
        ]);
        $this->assertDatabaseCount('cms_guest_donors', 0);
    }

    public function test_authenticated_user_without_global_id_is_not_treated_as_guest(): void
    {
        $user = $this->createUser(null);

        $this->actingAs($user)
            ->postJson('/donasi', $this->payload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('identity');

        $this->assertDatabaseCount('donasikilau', 0);
        $this->assertDatabaseCount('cms_guest_donors', 0);
    }

    private function createUser(?string $globalUserId): User
    {
        return User::query()->create([
            'global_user_id' => $globalUserId,
            'name' => 'Pengguna CMS',
            'email' => Str::uuid().'@example.test',
            'password' => 'password-test',
            'role' => 'user',
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nama' => 'Nama Transaksi',
            'type_donasi' => DonasiKilau::TYPE_DONASI_UMUM,
            'total' => 100000,
            'opsional_umum' => DonasiKilau::OPSIONAL_UMUM_INFAQ,
            'no_hp' => '0800000000',
            'email' => 'transaction@example.test',
            'feedback' => 'Dukungan pengujian',
        ], $overrides);
    }
}
