<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    public function createApplication(): Application
    {
        $app = parent::createApplication();

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('k', 32)));
        $app['config']->set('app.maintenance.driver', 'file');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('view.compiled', sys_get_temp_dir());

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $connection = DB::connection();

        if ($connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new \RuntimeException('Test navigasi admin hanya boleh dijalankan dengan SQLite in-memory.');
        }

        Schema::create('article_notifications', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Auth::forgetUser();

        parent::tearDown();
    }

    public function test_admin_sees_two_way_navigation(): void
    {
        $this->actingAs($this->user('admin'));

        $homeNavbar = view('App.navbar')->render();
        $adminNavbar = view('AdminPage.App.navbar')->render();

        $this->assertStringContainsString('Dashboard Admin', $homeNavbar);
        $this->assertStringContainsString(route('dashboard'), $homeNavbar);
        $this->assertStringContainsString('Home CMS', $adminNavbar);
        $this->assertStringContainsString(route('home'), $adminNavbar);
    }

    public function test_regular_user_does_not_see_admin_navigation(): void
    {
        $this->actingAs($this->user('user'));

        $homeNavbar = view('App.navbar')->render();
        $adminNavbar = view('AdminPage.App.navbar')->render();

        $this->assertStringNotContainsString('Dashboard Admin', $homeNavbar);
        $this->assertStringNotContainsString('Home CMS', $adminNavbar);
    }

    public function test_regular_user_is_rejected_from_admin_dashboard(): void
    {
        $route = Route::getRoutes()->getByName('dashboard');

        $this->assertNotNull($route);
        $this->assertContains('auth.local:admin', $route->gatherMiddleware());

        $this->actingAs($this->user('user'))
            ->get(route('dashboard'))
            ->assertRedirect(route('home'))
            ->assertSessionHas('error', 'Akses ditolak untuk role ini.');
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Silakan login terlebih dahulu.');
    }

    public function test_admin_upload_only_uses_protected_route(): void
    {
        $route = Route::getRoutes()->getByName('upload.image');

        $this->assertNotNull($route);
        $this->assertSame('admin/upload-image', $route->uri());
        $this->assertContains('auth.local:admin', $route->gatherMiddleware());
        $this->post('/upload-image')->assertNotFound();

        $this->actingAs($this->user('user'))
            ->post(route('upload.image'))
            ->assertRedirect(route('home'));
    }

    private function user(string $role): User
    {
        $user = new User([
            'name' => 'Pengguna Test',
            'email' => $role.'@example.test',
            'role' => $role,
        ]);
        $user->forceFill(['id' => $role === 'admin' ? 1 : 2]);
        $user->exists = true;

        return $user;
    }
}
