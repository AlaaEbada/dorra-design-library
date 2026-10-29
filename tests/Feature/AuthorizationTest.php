<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Policies\LibraryPolicy;
use Database\Seeders\SampleAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function account(Role $role): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->save();

        return $user;
    }

    public function test_guests_must_login_and_login_form_has_csrf(): void
    {
        $this->get('/library')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('name="_token"', false);
    }

    public function test_both_roles_can_login_and_logout(): void
    {
        foreach ([Role::Admin, Role::Viewer] as $role) {
            $user = $this->account($role);
            $this->post('/login', ['email' => $user->email, 'password' => 'password'])
                ->assertRedirect('/library');
            $this->assertAuthenticatedAs($user);
            $response = $this->get('/library')->assertOk();
            if ($role === Role::Admin) {
                $response->assertSee('Administrator access enabled.');
            } else {
                $response->assertDontSee('Administrator access enabled.');
            }
            $this->post('/logout')->assertRedirect('/');
            $this->assertGuest();
        }
    }

    public function test_invalid_credentials_and_validation(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->post('/login', ['email' => 'nobody@dorra.test', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_failed_logins_are_throttled(): void
    {
        $user = $this->account(Role::Viewer);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong']);
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_policy_denies_forged_viewer_writes_and_allows_admin(): void
    {
        // Test-only endpoints: actual design CRUD is introduced in Phase 06.
        foreach (['create' => 'post', 'update' => 'put', 'delete' => 'delete'] as $ability => $method) {
            Route::$method('/_test/'.$ability, fn () => response('allowed'))
                ->middleware(['web', 'auth', 'can:'.$ability.','.LibraryPolicy::class]);
        }
        foreach ([Role::Viewer, Role::Admin] as $role) {
            $user = $this->account($role);
            $this->actingAs($user);
            $this->assertTrue(Gate::forUser($user)->allows('view', LibraryPolicy::class));
            foreach (['create' => 'post', 'update' => 'put', 'delete' => 'delete'] as $ability => $method) {
                $this->$method('/_test/'.$ability)->assertStatus($role === Role::Admin ? 200 : 403);
            }
        }
    }

    public function test_public_account_management_is_unavailable(): void
    {
        foreach (['/register', '/forgot-password', '/reset-password/token', '/profile', '/verify-email', '/confirm-password'] as $path) {
            $this->get($path)->assertNotFound();
        }
        foreach (['/register', '/forgot-password', '/reset-password'] as $path) {
            $this->post($path)->assertNotFound();
        }
    }

    public function test_default_role_and_role_mass_assignment_protection(): void
    {
        $user = User::create(['name' => 'Viewer', 'email' => 'new@dorra.test', 'password' => 'password', 'role' => 'admin']);
        $this->assertSame(Role::Viewer, $user->fresh()->role);
    }

    public function test_sample_accounts_are_repeatable_and_passwords_hashed(): void
    {
        $this->seed(SampleAccountSeeder::class);
        $this->seed(SampleAccountSeeder::class);
        $this->assertSame(2, User::count());
        foreach (['admin' => Role::Admin, 'viewer' => Role::Viewer] as $name => $role) {
            $user = User::where('email', $name.'@dorra.test')->firstOrFail();
            $this->assertSame($role, $user->role);
            $this->assertTrue(Hash::check('DorraSample123!', $user->password));
        }
    }

    public function test_csrf_is_enforced_outside_test_bypass(): void
    {
        $this->app->instance('env', 'local');
        $this->post('/login', ['email' => 'sample@dorra.test', 'password' => 'wrong'])->assertStatus(419);
    }
}
