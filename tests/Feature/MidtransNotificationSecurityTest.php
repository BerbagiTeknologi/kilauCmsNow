<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\DonasiKilau;
use App\Models\IntegrationOutboxMessage;
use App\Services\IntegrationOutboxService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use ReflectionClass;
use RuntimeException;
use Tests\TestCase;

class MidtransNotificationSecurityTest extends TestCase
{
    private const SERVER_KEY = 'test-server-key';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.maintenance.driver' => 'file',
            'services.midtrans.server_key' => self::SERVER_KEY,
            'services.midtrans.api_url' => 'https://api.midtrans.test',
            'services.midtrans.timeout' => 1,
            'services.midtrans.local_verification_enabled' => false,
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'km12_service.donation_sync_enabled' => false,
        ]);
        Queue::fake();

        Schema::create('donasikilau', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('type_donasi')->nullable();
            $table->unsignedTinyInteger('opsional_umum')->nullable();
            $table->unsignedBigInteger('id_program')->nullable();
            $table->string('nama')->nullable();
            $table->decimal('total_donasi', 15, 2);
            $table->unsignedTinyInteger('status_donasi');
            $table->text('feedback')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('donor_source')->nullable();
            $table->uuid('external_donor_id')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('referral_code')->nullable();
            $table->string('referral_type')->nullable();
            $table->unsignedBigInteger('referral_cms_user_id')->nullable();
            $table->uuid('referral_global_user_id')->nullable();
            $table->unsignedBigInteger('referral_km12_user_id')->nullable();
            $table->unsignedBigInteger('referral_karyawan_id')->nullable();
            $table->string('referral_name_snapshot')->nullable();
            $table->string('referral_position_snapshot')->nullable();
            $table->string('km12_sync_status')->nullable();
            $table->unsignedBigInteger('km12_transaksi_id')->nullable();
            $table->timestamp('km12_synced_at')->nullable();
            $table->string('km12_sync_error')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_guest_donors', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->uuid('global_user_id')->nullable()->unique();
            $table->string('name');
            $table->string('email');
            $table->json('sso_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_general_donation_km12_mappings', function (Blueprint $table): void {
            $table->id();
            $table->string('donation_type');
            $table->unsignedBigInteger('km12_program_penerimaan_id')->nullable();
            $table->unsignedBigInteger('km12_sumber_dana_id')->nullable();
            $table->boolean('is_active')->default(true);
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
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('integration_outbox_messages');
        Schema::dropIfExists('cms_general_donation_km12_mappings');
        Schema::dropIfExists('users');
        Schema::dropIfExists('cms_guest_donors');
        Schema::dropIfExists('donasikilau');

        parent::tearDown();
    }

    public function test_verified_notification_marks_donation_as_paid(): void
    {
        $donasiId = $this->createDonation();
        $notification = $this->payload($donasiId, 'settlement');

        Http::fake([
            '*' => Http::response($this->payload($donasiId, 'settlement'), 200),
        ]);

        $this->postJson('/midtrans-notification', $notification)
            ->assertOk()
            ->assertJsonPath('message', 'Midtrans notification processed.');

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_AKTIVE,
        ]);

        Http::assertSent(function (Request $request) use ($donasiId): bool {
            return $request->url() === "https://api.midtrans.test/v2/donasi-{$donasiId}/status"
                && $request->hasHeader(
                    'Authorization',
                    'Basic '.base64_encode(self::SERVER_KEY.':')
                );
        });
    }

    public function test_new_paid_transition_records_one_forward_outbox_with_latest_profile(): void
    {
        config(['km12_service.donation_sync_enabled' => true]);
        $guestId = '10000000-0000-4000-8000-000000000001';
        DB::table('cms_guest_donors')->insert([
            'id' => $guestId,
            'name' => 'Profil Terbaru',
            'email' => 'latest@example.test',
            'no_hp' => '0800000001',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cms_general_donation_km12_mappings')->insert([
            'donation_type' => 'infaq',
            'km12_program_penerimaan_id' => 39,
            'km12_sumber_dana_id' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $donasiId = DB::table('donasikilau')->insertGetId([
            'type_donasi' => DonasiKilau::TYPE_DONASI_UMUM,
            'opsional_umum' => DonasiKilau::OPSIONAL_UMUM_INFAQ,
            'nama' => 'Nama Saat Donasi',
            'total_donasi' => 100000,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
            'donor_source' => DonasiKilau::DONOR_SOURCE_KILAU_CMS,
            'external_donor_id' => $guestId,
            'is_anonymous' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $notification = $this->payload($donasiId, 'settlement');
        Http::fake(['*' => Http::response($notification, 200)]);

        $this->postJson('/midtrans-notification', $notification)->assertOk();
        $this->postJson('/midtrans-notification', $notification)->assertOk();

        $message = IntegrationOutboxMessage::query()->sole();
        $this->assertSame('donasi-'.$donasiId, $message->payload['source_transaction_id']);
        $this->assertSame('Profil Terbaru', $message->payload['donor']['name']);
        $this->assertSame('Nama Saat Donasi', $message->payload['donation']['donor_name_snapshot']);
        $this->assertSame(39, $message->payload['donation']['program_penerimaan_id']);
        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_AKTIVE,
            'km12_sync_status' => 'pending',
        ]);
        $this->assertSame(1, IntegrationOutboxMessage::query()->count());
    }

    public function test_existing_paid_donation_is_not_added_to_forward_outbox(): void
    {
        config(['km12_service.donation_sync_enabled' => true]);
        $donasiId = $this->createDonation(DonasiKilau::DONASI_AKTIVE);
        $notification = $this->payload($donasiId, 'settlement');
        Http::fake(['*' => Http::response($notification, 200)]);

        $this->postJson('/midtrans-notification', $notification)->assertOk();

        $this->assertSame(0, IntegrationOutboxMessage::query()->count());
    }

    public function test_logged_donor_payload_uses_latest_local_auth_profile_and_transaction_snapshot(): void
    {
        config(['km12_service.donation_sync_enabled' => true]);
        $globalUserId = '20000000-0000-4000-8000-000000000001';
        DB::table('users')->insert([
            'global_user_id' => $globalUserId,
            'name' => 'Profil Auth Terbaru',
            'email' => 'auth-latest@example.test',
            'sso_payload' => json_encode([
                'global_user' => ['no_hp' => '0800000002'],
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('cms_general_donation_km12_mappings')->insert([
            'donation_type' => 'infaq',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $donasiId = DB::table('donasikilau')->insertGetId([
            'type_donasi' => DonasiKilau::TYPE_DONASI_UMUM,
            'opsional_umum' => DonasiKilau::OPSIONAL_UMUM_INFAQ,
            'nama' => 'Nama Saat Donasi Auth',
            'total_donasi' => 100000,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
            'donor_source' => DonasiKilau::DONOR_SOURCE_KILAU_AUTH,
            'external_donor_id' => $globalUserId,
            'is_anonymous' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $notification = $this->payload($donasiId, 'settlement');
        Http::fake(['*' => Http::response($notification, 200)]);

        $this->postJson('/midtrans-notification', $notification)->assertOk();

        $payload = IntegrationOutboxMessage::query()->sole()->payload;
        $this->assertSame('Profil Auth Terbaru', $payload['donor']['name']);
        $this->assertSame('Nama Saat Donasi Auth', $payload['donation']['donor_name_snapshot']);
    }

    public function test_status_change_rolls_back_when_outbox_record_fails(): void
    {
        $this->withoutExceptionHandling();
        config(['km12_service.donation_sync_enabled' => true]);
        $donasiId = $this->createDonation();
        $notification = $this->payload($donasiId, 'settlement');
        Http::fake(['*' => Http::response($notification, 200)]);
        $outbox = $this->mock(IntegrationOutboxService::class);
        $outbox->shouldReceive('record')->once()->andThrow(new RuntimeException('outbox failed'));

        try {
            $this->postJson('/midtrans-notification', $notification);
            $this->fail('Expected outbox failure.');
        } catch (RuntimeException) {
            $this->assertDatabaseHas('donasikilau', [
                'id' => $donasiId,
                'status_donasi' => DonasiKilau::DONASI_PENDING,
            ]);
            $this->assertSame(0, IntegrationOutboxMessage::query()->count());
        }
    }

    public function test_invalid_signature_is_rejected_without_status_lookup(): void
    {
        $donasiId = $this->createDonation();
        $notification = $this->payload($donasiId, 'settlement');
        $notification['signature_key'] = str_repeat('0', 128);
        Http::fake();

        $this->postJson('/midtrans-notification', $notification)
            ->assertForbidden()
            ->assertJsonPath('message', 'Invalid Midtrans signature.');

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
        ]);
        Http::assertNothingSent();
    }

    public function test_verified_amount_must_match_local_donation(): void
    {
        $donasiId = $this->createDonation();
        $notification = $this->payload($donasiId, 'settlement', '125000.00');

        Http::fake([
            '*' => Http::response($notification, 200),
        ]);

        $this->postJson('/midtrans-notification', $notification)
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Payment amount does not match the donation.');

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
        ]);
    }

    public function test_invalid_status_api_signature_is_rejected(): void
    {
        $donasiId = $this->createDonation();
        $notification = $this->payload($donasiId, 'settlement');
        $status = $notification;
        $status['signature_key'] = str_repeat('0', 128);

        Http::fake([
            '*' => Http::response($status, 200),
        ]);

        $this->postJson('/midtrans-notification', $notification)
            ->assertStatus(503)
            ->assertJsonPath('message', 'Invalid Midtrans signature.');

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
        ]);
    }

    public function test_out_of_order_pending_notification_does_not_downgrade_paid_donation(): void
    {
        $donasiId = $this->createDonation(DonasiKilau::DONASI_AKTIVE);
        $notification = $this->payload($donasiId, 'pending', '100000.00', '201');

        Http::fake([
            '*' => Http::response($notification, 200),
        ]);

        $this->postJson('/midtrans-notification', $notification)->assertOk();

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_AKTIVE,
        ]);
    }

    public function test_public_status_update_endpoint_is_removed(): void
    {
        $donasiId = $this->createDonation();

        $this->postJson("/donasi/{$donasiId}/update-status", ['status' => 2])
            ->assertNotFound();

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
        ]);
    }

    public function test_signed_local_verification_uses_midtrans_status_before_marking_paid(): void
    {
        config(['services.midtrans.local_verification_enabled' => true]);
        $donasiId = $this->createDonation();
        Http::fake([
            '*' => Http::response($this->payload($donasiId, 'settlement'), 200),
        ]);
        $url = URL::temporarySignedRoute(
            'donasi.verify-payment',
            now()->addMinute(),
            ['donasi' => $donasiId],
        );

        $this->postJson($url, ['status' => DonasiKilau::DONASI_EXPIRED])
            ->assertOk()
            ->assertJsonPath('message', 'Midtrans payment status verified.')
            ->assertJsonPath('is_paid', true)
            ->assertJsonPath('donation_status', DonasiKilau::DONASI_AKTIVE);

        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_AKTIVE,
        ]);
    }

    public function test_local_verification_rejects_unsigned_request(): void
    {
        config(['services.midtrans.local_verification_enabled' => true]);
        $donasiId = $this->createDonation();
        Http::fake();

        $this->postJson("/donasi/{$donasiId}/verify-payment", ['status' => 2])
            ->assertForbidden();

        Http::assertNothingSent();
        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
        ]);
    }

    public function test_local_verification_is_not_available_when_flag_is_disabled(): void
    {
        $donasiId = $this->createDonation();
        Http::fake();
        $url = URL::temporarySignedRoute(
            'donasi.verify-payment',
            now()->addMinute(),
            ['donasi' => $donasiId],
        );

        $this->postJson($url)->assertNotFound();

        Http::assertNothingSent();
        $this->assertDatabaseHas('donasikilau', [
            'id' => $donasiId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
        ]);
    }

    public function test_only_midtrans_notification_is_exempt_from_csrf(): void
    {
        $reflection = new ReflectionClass(VerifyCsrfToken::class);
        $property = $reflection->getProperty('except');
        $property->setAccessible(true);

        $this->assertSame([
            'midtrans-notification',
        ], $property->getValue(app(VerifyCsrfToken::class)));
    }

    private function createDonation(int $status = DonasiKilau::DONASI_PENDING): int
    {
        return DB::table('donasikilau')->insertGetId([
            'type_donasi' => DonasiKilau::TYPE_DONASI_UMUM,
            'opsional_umum' => DonasiKilau::OPSIONAL_UMUM_INFAQ,
            'nama' => 'Donatur Test',
            'total_donasi' => 100000,
            'status_donasi' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function payload(
        int $donasiId,
        string $transactionStatus,
        string $grossAmount = '100000.00',
        string $statusCode = '200'
    ): array {
        $orderId = 'donasi-'.$donasiId;

        return [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'transaction_status' => $transactionStatus,
            'fraud_status' => 'accept',
            'signature_key' => hash(
                'sha512',
                $orderId.$statusCode.$grossAmount.self::SERVER_KEY
            ),
        ];
    }
}
