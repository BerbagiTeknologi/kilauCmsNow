<?php

namespace Tests\Feature;

use App\Models\DonasiKilau;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DonationHistoryPaginationTest extends TestCase
{
    private bool $usesIsolatedDatabase = false;

    public function createApplication(): Application
    {
        $app = parent::createApplication();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
        $app['config']->set('app.maintenance.driver', 'file');
        $app['config']->set('session.driver', 'array');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $connection = DB::connection();

        if ($connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new \RuntimeException('Test riwayat donasi hanya boleh dijalankan dengan SQLite in-memory.');
        }

        $this->usesIsolatedDatabase = true;

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

        Schema::create('programs', function (Blueprint $table): void {
            $table->id();
            $table->string('judul');
            $table->timestamps();
        });

        Schema::create('donasikilau', function (Blueprint $table): void {
            $table->id();
            $table->unsignedTinyInteger('type_donasi');
            $table->unsignedTinyInteger('opsional_umum')->nullable();
            $table->unsignedBigInteger('id_program')->nullable();
            $table->string('nama')->nullable();
            $table->decimal('total_donasi', 15, 2)->nullable();
            $table->unsignedTinyInteger('status_donasi')->nullable();
            $table->timestamps();
        });

        Schema::create('donasi_histories', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('donasikilau_id');
            $table->char('external_user_id', 36)->nullable()->index();
            $table->unsignedTinyInteger('status_donasi')->nullable();
            $table->decimal('total_donasi', 15, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->text('token')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        if ($this->usesIsolatedDatabase) {
            Auth::logout();

            Schema::dropIfExists('donasi_histories');
            Schema::dropIfExists('donasikilau');
            Schema::dropIfExists('programs');
            Schema::dropIfExists('users');
        }

        parent::tearDown();
    }

    public function test_history_uses_server_pagination_and_full_user_summary(): void
    {
        $globalUserId = '10000000-0000-4000-8000-000000000001';
        $otherUserId = '20000000-0000-4000-8000-000000000002';
        $user = $this->createUser($globalUserId);
        $oldestOwnDonationIds = [];

        foreach (range(1, 12) as $index) {
            $status = match (true) {
                $index <= 5 => DonasiKilau::DONASI_AKTIVE,
                $index <= 9 => DonasiKilau::DONASI_EXPIRED,
                default => DonasiKilau::DONASI_PENDING,
            };
            $createdAt = now()->subDays(20 - $index);
            $donationId = DB::table('donasikilau')->insertGetId([
                'type_donasi' => DonasiKilau::TYPE_DONASI_UMUM,
                'opsional_umum' => DonasiKilau::OPSIONAL_UMUM_INFAQ,
                'nama' => 'Donatur',
                'total_donasi' => $index * 1000,
                'status_donasi' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            DB::table('donasi_histories')->insert([
                'donasikilau_id' => $donationId,
                'external_user_id' => $globalUserId,
                'status_donasi' => DonasiKilau::DONASI_PENDING,
                'total_donasi' => $index * 1000,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            if ($index <= 3) {
                $oldestOwnDonationIds[] = $donationId;
            }
        }

        DB::table('donasi_histories')->insert([
            'donasikilau_id' => 999999,
            'external_user_id' => $globalUserId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
            'total_donasi' => 500,
            'created_at' => now()->subDays(30),
            'updated_at' => now()->subDays(30),
        ]);

        $foreignDonationId = DB::table('donasikilau')->insertGetId([
            'type_donasi' => DonasiKilau::TYPE_DONASI_UMUM,
            'opsional_umum' => DonasiKilau::OPSIONAL_UMUM_ZAKAT,
            'nama' => 'Donatur Lain',
            'total_donasi' => 999999,
            'status_donasi' => DonasiKilau::DONASI_AKTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('donasi_histories')->insert([
            'donasikilau_id' => $foreignDonationId,
            'external_user_id' => $otherUserId,
            'status_donasi' => DonasiKilau::DONASI_PENDING,
            'total_donasi' => 999999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->withSession($this->authSession($user))
            ->get('/get-users?page=2')
            ->assertOk()
            ->assertViewHas('histories', function ($histories) use ($oldestOwnDonationIds): bool {
                return $histories->currentPage() === 2
                    && $histories->perPage() === 10
                    && $histories->total() === 13
                    && $histories->count() === 3
                    && $histories->pluck('donasikilau_id')->values()->all()
                        === [$oldestOwnDonationIds[1], $oldestOwnDonationIds[0], 999999];
            })
            ->assertViewHas('historySummary', [
                'total_transaksi' => 13,
                'total_nominal' => 78500.0,
                'jumlah_aktif' => 5,
                'jumlah_pending' => 4,
                'jumlah_expired' => 4,
            ]);
    }

    private function createUser(string $globalUserId): User
    {
        return User::query()->create([
            'global_user_id' => $globalUserId,
            'name' => 'Profil Donatur',
            'email' => 'donatur@example.test',
            'password' => Hash::make('password-test'),
            'role' => 'user',
            'sso_payload' => [
                'provider' => 'kilau_auth',
                'global_user' => [
                    'global_user_id' => $globalUserId,
                    'name' => 'Profil Donatur',
                    'email' => 'donatur@example.test',
                    'phone' => null,
                ],
            ],
        ]);
    }

    private function authSession(User $user): array
    {
        return [
            'auth_service_user' => $user->sso_payload['global_user'],
            'global_user_id' => $user->global_user_id,
            'local_user_id' => $user->id,
            'user_id' => $user->global_user_id,
            'user_sub' => $user->global_user_id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
        ];
    }
}
