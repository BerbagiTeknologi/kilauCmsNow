<?php

namespace Tests\Feature;

use App\Exceptions\IntegrationOutboxConflictException;
use App\Jobs\DeliverIntegrationOutboxMessage;
use App\Models\IntegrationOutboxMessage;
use App\Services\IntegrationOutboxDeliveryService;
use App\Services\IntegrationOutboxService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class IntegrationOutboxReliabilityTest extends TestCase
{
    private const SECRET = 'test-integration-secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'km12_service.url' => 'https://km12.test',
            'km12_service.donation_path' => '/api/internal/cms/donations',
            'km12_service.profile_path' => '/api/internal/cms/donor-profiles',
            'km12_service.integration_secret' => self::SECRET,
            'km12_service.donation_sync_enabled' => true,
            'km12_service.timeout' => 1,
            'km12_service.outbox_connection' => 'database',
            'km12_service.outbox_queue' => 'integrations',
            'km12_service.outbox_max_attempts' => 5,
            'km12_service.outbox_retry_delays' => [60, 300, 900, 3600, 21600],
            'km12_service.outbox_lock_seconds' => 600,
        ]);
        Log::spy();

        Schema::create('donasikilau', function (Blueprint $table): void {
            $table->id();
            $table->string('km12_sync_status')->nullable();
            $table->unsignedBigInteger('km12_transaksi_id')->nullable();
            $table->timestamp('km12_synced_at')->nullable();
            $table->string('km12_sync_error')->nullable();
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
        Schema::dropIfExists('donasikilau');

        parent::tearDown();
    }

    public function test_record_is_transactional_idempotent_and_encrypted(): void
    {
        Queue::fake();
        $service = app(IntegrationOutboxService::class);
        $payload = $this->payload();

        try {
            DB::transaction(function () use ($service, $payload): void {
                $service->record('donation.paid', 'donation', 10, $payload);
                throw new RuntimeException('rollback');
            });
        } catch (RuntimeException) {
            $this->assertSame(0, IntegrationOutboxMessage::query()->count());
            Queue::assertNothingPushed();
        }

        $first = $service->record('donation.paid', 'donation', 10, $payload);
        $second = $service->record('donation.paid', 'donation', 10, $payload);
        $rawPayload = (string) DB::table('integration_outbox_messages')
            ->where('id', $first->id)
            ->value('payload');

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, IntegrationOutboxMessage::query()->count());
        $this->assertStringNotContainsString('Nama Uji', $rawPayload);
        $this->assertSame('Nama Uji', $first->fresh()->payload['donor']['name']);
        Queue::assertPushed(
            DeliverIntegrationOutboxMessage::class,
            fn (DeliverIntegrationOutboxMessage $job): bool => $job->outboxId === $first->id,
        );
        Queue::assertPushed(DeliverIntegrationOutboxMessage::class, 1);
    }

    public function test_changed_payload_for_same_aggregate_is_rejected(): void
    {
        Queue::fake();
        $service = app(IntegrationOutboxService::class);
        $service->record('donation.paid', 'donation', 11, $this->payload());
        $changed = $this->payload();
        $changed['donation']['amount'] = 125000;

        $this->expectException(IntegrationOutboxConflictException::class);
        $service->record('donation.paid', 'donation', 11, $changed);
    }

    public function test_delivery_uses_hmac_and_marks_message_delivered(): void
    {
        Queue::fake();
        $this->createDonation(12);
        $statusDuringDelivery = null;
        Http::fake(function () use (&$statusDuringDelivery) {
            $statusDuringDelivery = DB::table('donasikilau')
                ->where('id', 12)
                ->value('km12_sync_status');

            return Http::response(['data' => ['transaksi_id' => 99]], 201);
        });
        $message = app(IntegrationOutboxService::class)
            ->record('donation.paid', 'donation', 12, $this->payload());

        $result = app(IntegrationOutboxDeliveryService::class)->deliver($message->id);

        $this->assertSame('delivered', $result['status']);
        $this->assertSame('syncing', $statusDuringDelivery);
        $this->assertDatabaseHas('integration_outbox_messages', [
            'id' => $message->id,
            'status' => IntegrationOutboxMessage::STATUS_DELIVERED,
            'attempts' => 1,
        ]);
        $this->assertDatabaseHas('donasikilau', [
            'id' => 12,
            'km12_sync_status' => 'synced',
            'km12_transaksi_id' => 99,
            'km12_sync_error' => null,
        ]);
        Http::assertSent(function (Request $request): bool {
            $timestamp = $request->header('X-Integration-Timestamp')[0] ?? '';
            $nonce = $request->header('X-Integration-Nonce')[0] ?? '';
            $signature = $request->header('X-Integration-Signature')[0] ?? '';
            $expected = hash_hmac('sha256', implode("\n", [
                'POST',
                '/api/internal/cms/donations',
                $timestamp,
                $nonce,
                hash('sha256', $request->body()),
            ]), self::SECRET);

            return $request->url() === 'https://km12.test/api/internal/cms/donations'
                && hash_equals($expected, $signature);
        });
    }

    public function test_retry_backoff_then_dead_letter_and_manual_retry(): void
    {
        Queue::fake();
        $this->createDonation(13);
        Http::fake(['*' => Http::response([], 500)]);
        $message = app(IntegrationOutboxService::class)
            ->record('donation.paid', 'donation', 13, $this->payload());
        $job = new DeliverIntegrationOutboxMessage($message->id);

        $job->handle(app(IntegrationOutboxDeliveryService::class));

        $retrying = $message->fresh();
        $this->assertSame(IntegrationOutboxMessage::STATUS_PENDING, $retrying->status);
        $this->assertSame(1, $retrying->attempts);
        $this->assertSame('UPSTREAM_ERROR', $retrying->last_error_code);
        $this->assertTrue($retrying->available_at->between(now()->addSeconds(55), now()->addSeconds(65)));
        $this->assertDatabaseHas('donasikilau', [
            'id' => 13,
            'km12_sync_status' => 'pending',
            'km12_sync_error' => 'UPSTREAM_ERROR',
        ]);
        Queue::assertPushed(DeliverIntegrationOutboxMessage::class, 2);

        for ($attempt = 2; $attempt <= 5; $attempt++) {
            IntegrationOutboxMessage::query()->whereKey($message->id)->update([
                'available_at' => now()->subSecond(),
            ]);
            app(IntegrationOutboxDeliveryService::class)->deliver($message->id);
        }

        $deadLetter = $message->fresh();
        $this->assertSame(IntegrationOutboxMessage::STATUS_DEAD_LETTER, $deadLetter->status);
        $this->assertSame(5, $deadLetter->attempts);
        $this->assertNotNull($deadLetter->dead_lettered_at);
        $this->assertDatabaseHas('donasikilau', [
            'id' => 13,
            'km12_sync_status' => 'failed',
            'km12_sync_error' => 'UPSTREAM_ERROR',
        ]);

        $this->assertTrue(app(IntegrationOutboxService::class)->retry($message->id));
        $this->assertDatabaseHas('integration_outbox_messages', [
            'id' => $message->id,
            'status' => IntegrationOutboxMessage::STATUS_PENDING,
            'attempts' => 0,
            'last_error_code' => null,
        ]);
    }

    public function test_non_retryable_response_goes_directly_to_dead_letter(): void
    {
        Queue::fake();
        $this->createDonation(14);
        Http::fake(['*' => Http::response([], 422)]);
        $message = app(IntegrationOutboxService::class)
            ->record('donation.paid', 'donation', 14, $this->payload());

        app(IntegrationOutboxDeliveryService::class)->deliver($message->id);

        $this->assertDatabaseHas('integration_outbox_messages', [
            'id' => $message->id,
            'status' => IntegrationOutboxMessage::STATUS_DEAD_LETTER,
            'attempts' => 1,
            'last_error_code' => 'VALIDATION_ERROR',
            'last_http_status' => 422,
        ]);
    }

    public function test_invalid_success_response_is_retried_without_logging_body(): void
    {
        Queue::fake();
        $this->createDonation(16);
        Http::fake(['*' => Http::response('not-json', 200)]);
        $message = app(IntegrationOutboxService::class)
            ->record('donation.paid', 'donation', 16, $this->payload());

        app(IntegrationOutboxDeliveryService::class)->deliver($message->id);

        $this->assertDatabaseHas('integration_outbox_messages', [
            'id' => $message->id,
            'status' => IntegrationOutboxMessage::STATUS_PENDING,
            'attempts' => 1,
            'last_error_code' => 'INVALID_RESPONSE',
            'last_http_status' => 200,
        ]);
        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $message, array $context): bool => $message === 'Pengiriman outbox integrasi gagal.'
                && ! array_key_exists('body', $context)
                && ! array_key_exists('payload', $context)
                && ! str_contains(json_encode($context), 'not-json')
        );
    }

    public function test_default_off_does_not_dispatch_messages(): void
    {
        Queue::fake();
        config(['km12_service.donation_sync_enabled' => false]);

        app(IntegrationOutboxService::class)
            ->record('donation.paid', 'donation', 15, $this->payload());

        Queue::assertNothingPushed();
        $this->assertSame(0, app(IntegrationOutboxService::class)->dispatchPending());
    }

    public function test_profile_delivery_uses_profile_path_without_transaction_response(): void
    {
        Queue::fake();
        $payload = [
            'event_type' => 'donor.profile_updated',
            'source' => 'kilau_cms',
            'external_donor_id' => '10000000-0000-4000-8000-000000000001',
            'profile_version' => 2,
            'profile' => ['name' => 'Nama Uji'],
        ];
        Http::fake(function (Request $request) {
            $eventId = json_decode($request->body(), true, flags: JSON_THROW_ON_ERROR)['event_id'];

            return Http::response(['data' => ['event_id' => $eventId]], 201);
        });
        $message = app(IntegrationOutboxService::class)->record(
            'donor.profile_updated',
            'guest_donor_profile',
            '10000000-0000-4000-8000-000000000001:2',
            $payload,
        );

        app(IntegrationOutboxDeliveryService::class)->deliver($message->id);

        $this->assertSame(IntegrationOutboxMessage::STATUS_DELIVERED, $message->fresh()->status);
        Http::assertSent(function (Request $request): bool {
            $timestamp = $request->header('X-Integration-Timestamp')[0] ?? '';
            $nonce = $request->header('X-Integration-Nonce')[0] ?? '';
            $signature = $request->header('X-Integration-Signature')[0] ?? '';
            $expected = hash_hmac('sha256', implode("\n", [
                'POST',
                '/api/internal/cms/donor-profiles',
                $timestamp,
                $nonce,
                hash('sha256', $request->body()),
            ]), self::SECRET);

            return $request->url() === 'https://km12.test/api/internal/cms/donor-profiles'
                && hash_equals($expected, $signature);
        });
    }

    private function payload(): array
    {
        return [
            'event_type' => 'donation.paid',
            'source' => 'kilau_cms',
            'source_transaction_id' => 'donation-10',
            'donor' => [
                'name' => 'Nama Uji',
            ],
            'donation' => [
                'amount' => 100000,
            ],
        ];
    }

    private function createDonation(int $id): void
    {
        DB::table('donasikilau')->insert([
            'id' => $id,
            'km12_sync_status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
