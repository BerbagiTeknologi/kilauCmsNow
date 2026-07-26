<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CmsProfileUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('k', 32)),
            'app.maintenance.driver' => 'file',
            'auth_service.url' => 'https://kilau-auth.test',
            'auth_service.timeout' => 1,
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
    }

    protected function tearDown(): void
    {
        Auth::logout();
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_authenticated_account_updates_auth_then_refreshes_local_profile_and_session(): void
    {
        $user = $this->createUser('admin');
        $authProfile = [
            'id' => $user->global_user_id,
            'global_user_id' => $user->global_user_id,
            'name' => 'Profil Diperbarui',
            'email' => 'profil-baru@example.test',
            'phone' => '0800000000',
            'is_active' => true,
            'profile_version' => 2,
        ];
        Http::fake([
            'https://kilau-auth.test/api/me' => Http::response(['data' => $authProfile], 200),
        ]);

        $this->actingAs($user)
            ->withSession($this->authSession($user))
            ->patch('/get-users', [
                'name' => $authProfile['name'],
                'email' => $authProfile['email'],
                'phone' => $authProfile['phone'],
            ])
            ->assertRedirect(route('getDataUsersProfile'))
            ->assertSessionHas('success', 'Profil berhasil diperbarui.')
            ->assertSessionHas('user_name', $authProfile['name'])
            ->assertSessionHas('user_email', $authProfile['email'])
            ->assertSessionHas('auth_service_user.phone', $authProfile['phone']);

        $user->refresh();
        $this->assertSame($authProfile['name'], $user->name);
        $this->assertSame($authProfile['email'], $user->email);
        $this->assertSame($authProfile['phone'], $user->sso_payload['global_user']['phone']);
        $this->assertSame('admin', $user->role);

        Http::assertSent(function (Request $request) use ($authProfile): bool {
            return $request->method() === 'PATCH'
                && $request->url() === 'https://kilau-auth.test/api/me'
                && $request->hasHeader('Authorization', 'Bearer test-access-token')
                && $request['name'] === $authProfile['name']
                && $request['email'] === $authProfile['email']
                && $request['phone'] === $authProfile['phone'];
        });
    }

    public function test_expired_auth_token_does_not_change_local_profile(): void
    {
        $user = $this->createUser();
        Http::fake([
            'https://kilau-auth.test/api/me' => Http::response(['message' => 'Unauthenticated.'], 401),
        ]);

        $this->actingAs($user)
            ->withSession($this->authSession($user))
            ->from('/get-users')
            ->patch('/get-users', [
                'name' => 'Tidak Boleh Tersimpan',
                'email' => 'gagal@example.test',
                'phone' => '',
            ])
            ->assertRedirect('/get-users')
            ->assertSessionHasErrors('profile');

        $user->refresh();
        $this->assertSame('Profil Awal', $user->name);
        $this->assertSame('profil-awal@example.test', $user->email);
    }

    public function test_local_email_conflict_is_rejected_before_calling_auth(): void
    {
        $user = $this->createUser();
        User::query()->create([
            'global_user_id' => '20000000-0000-4000-8000-000000000002',
            'name' => 'Akun Lain',
            'email' => 'dipakai@example.test',
            'password' => Hash::make('password-test'),
            'role' => 'user',
        ]);
        Http::fake();

        $this->actingAs($user)
            ->withSession($this->authSession($user))
            ->from('/get-users')
            ->patch('/get-users', [
                'name' => 'Profil Awal',
                'email' => 'dipakai@example.test',
                'phone' => '',
            ])
            ->assertRedirect('/get-users')
            ->assertSessionHasErrors('email');

        Http::assertNothingSent();
        $this->assertSame('profil-awal@example.test', $user->fresh()->email);
    }

    private function createUser(string $role = 'user'): User
    {
        return User::query()->create([
            'global_user_id' => '10000000-0000-4000-8000-000000000001',
            'name' => 'Profil Awal',
            'email' => 'profil-awal@example.test',
            'password' => Hash::make('password-test'),
            'role' => $role,
            'sso_payload' => [
                'provider' => 'kilau_auth',
                'global_user' => [
                    'global_user_id' => '10000000-0000-4000-8000-000000000001',
                    'name' => 'Profil Awal',
                    'email' => 'profil-awal@example.test',
                    'phone' => null,
                ],
            ],
        ]);
    }

    private function authSession(User $user): array
    {
        return [
            'auth_service_access_token' => 'test-access-token',
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
