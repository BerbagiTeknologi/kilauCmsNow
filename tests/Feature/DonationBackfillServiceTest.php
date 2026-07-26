<?php

namespace Tests\Feature;

use App\Models\DonasiKilau;
use App\Models\IntegrationOutboxMessage;
use App\Services\DonationBackfillService;
use App\Services\DonationPaidPayloadFactory;
use App\Services\IntegrationOutboxService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class DonationBackfillServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'km12_service.donation_sync_enabled' => true,
        ]);
        Queue::fake();
        Schema::create('donasikilau', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('status_donasi');
            $table->string('donor_source')->nullable();
            $table->uuid('external_donor_id')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('km12_sync_status')->nullable();
            $table->text('km12_sync_error')->nullable();
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

    public function test_dry_run_classifies_only_explicit_identity_without_writes(): void
    {
        $factory = Mockery::mock(DonationPaidPayloadFactory::class);
        $factory->shouldNotReceive('make');
        $this->app->instance(DonationPaidPayloadFactory::class, $factory);
        $this->insertDonation(1, false, 'kilau_cms', '10000000-0000-4000-8000-000000000001');
        $this->insertDonation(2, true, null, null);
        $this->insertDonation(3, false, null, null, 'synced');
        $this->insertDonation(
            4,
            false,
            'kilau_auth',
            '10000000-0000-4000-8000-000000000004',
            'synced',
        );
        $this->insertDonation(
            5,
            false,
            'kilau_cms',
            '10000000-0000-4000-8000-000000000005',
            null,
            DonasiKilau::DONASI_PENDING,
        );
        $this->insertDonation(6, false, 'kilau_cms', '10000000-0000-4000-8000-000000000006');
        $outbox = app(IntegrationOutboxService::class)->record(
            'donation.paid',
            'donation',
            6,
            ['event_type' => 'donation.paid', 'source_transaction_id' => 'donasi-6'],
        );
        $outbox->forceFill(['status' => IntegrationOutboxMessage::STATUS_DEAD_LETTER])->save();
        $before = $this->tableCounts();

        $report = app(DonationBackfillService::class)->run();

        $this->assertSame(5, $report['scanned']);
        $this->assertSame(3, $report['eligible_identified']);
        $this->assertSame(1, $report['eligible_anonymous']);
        $this->assertSame(1, $report['ineligible_identity']);
        $this->assertSame(1, $report['ineligible_marked_synced']);
        $this->assertSame(1, $report['already_synced']);
        $this->assertSame(1, $report['outbox_dead_letter']);
        $this->assertSame(2, $report['candidates']);
        $this->assertSame(0, $report['enqueued']);
        $this->assertSame($before, $this->tableCounts());
    }

    public function test_apply_records_one_idempotent_outbox_message(): void
    {
        $this->insertDonation(10, false, 'kilau_cms', '10000000-0000-4000-8000-000000000010');
        $factory = Mockery::mock(DonationPaidPayloadFactory::class);
        $factory->shouldReceive('make')->once()->andReturn([
            'event_type' => 'donation.paid',
            'source' => 'kilau_cms',
            'source_transaction_id' => 'donasi-10',
        ]);
        $this->app->instance(DonationPaidPayloadFactory::class, $factory);

        $first = app(DonationBackfillService::class)->run(true);
        $second = app(DonationBackfillService::class)->run(true);

        $this->assertSame(1, $first['enqueued']);
        $this->assertSame(0, $second['enqueued']);
        $this->assertSame(1, $second['outbox_pending']);
        $this->assertDatabaseCount('integration_outbox_messages', 1);
        $this->assertDatabaseHas('donasikilau', [
            'id' => 10,
            'km12_sync_status' => 'pending',
        ]);
    }

    private function insertDonation(
        int $id,
        bool $anonymous,
        ?string $source,
        ?string $externalId,
        ?string $syncStatus = null,
        int $status = DonasiKilau::DONASI_AKTIVE,
    ): void {
        DB::table('donasikilau')->insert([
            'id' => $id,
            'status_donasi' => $status,
            'donor_source' => $source,
            'external_donor_id' => $externalId,
            'is_anonymous' => $anonymous,
            'km12_sync_status' => $syncStatus,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function tableCounts(): array
    {
        return [
            'donations' => DB::table('donasikilau')->count(),
            'outbox' => DB::table('integration_outbox_messages')->count(),
        ];
    }
}
