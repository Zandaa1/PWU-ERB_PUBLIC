<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GoogleAuthRoleProvisioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_auto_provisions_student_role_when_roles_table_empty(): void
    {
        // Truncate roles table to simulate an unseeded database
        Role::query()->delete();
        $this->assertDatabaseCount('roles', 0);

        // Mock Socialite Google User
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-123456');
        $socialiteUser->shouldReceive('getName')->andReturn('Maria Clara');
        $socialiteUser->shouldReceive('getEmail')->andReturn('student001@example.test');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/test');

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect('/dashboard');

        // Verify user was created
        $user = User::where('email', 'student001@example.test')->first();
        $this->assertNotNull($user);

        // Verify baseline roles were provisioned in database
        $this->assertTrue(Role::where('name', 'student')->exists());
        $this->assertTrue(Role::where('name', 'committee')->exists());
        $this->assertTrue(Role::where('name', 'admin')->exists());

        // Verify user was assigned student role
        $this->assertTrue($user->hasRole('student'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_existing_admin_user_retains_admin_role_on_login(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);

        $admin = User::factory()->create([
            'email' => 'dean@example.test',
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-admin-123');
        $socialiteUser->shouldReceive('getName')->andReturn('Demo Chair');
        $socialiteUser->shouldReceive('getEmail')->andReturn('dean@example.test');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');
        $response->assertRedirect('/dashboard');

        $admin->refresh();
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($admin->hasRole('student'));
    }
}
