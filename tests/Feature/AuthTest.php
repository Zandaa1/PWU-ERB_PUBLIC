<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_accessible()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Ethics Review Board');
    }

    public function test_dev_login_works_in_local_environment()
    {
        config(['app.demo_mode' => true]);
        $this->seed();

        $response = $this->get('/auth/dev-login/student');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('student@example.test', $user->email);
    }

    public function test_dev_login_works_for_secretary_persona()
    {
        config(['app.demo_mode' => true]);
        $this->seed();

        $response = $this->get('/auth/dev-login/secretary');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('secretary@example.test', $user->email);
        $this->assertTrue($user->hasRole('secretary'));
    }

    public function test_dev_login_creates_and_provisions_secretary_if_not_present()
    {
        config(['app.demo_mode' => true]);

        $response = $this->get('/auth/dev-login/secretary');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('secretary@example.test', $user->email);
        $this->assertEquals('Demo Secretary', $user->name);
        $this->assertTrue($user->hasRole('secretary'));
    }

    public function test_faq_page_is_accessible()
    {
        $this->seed();
        $user = User::first();

        $response = $this->actingAs($user)->get('/faq');
        $response->assertStatus(200);
        $response->assertSeeText('FAQ & Submission Guidelines');
    }

    public function test_faq_page_is_publicly_accessible_to_guests()
    {
        $response = $this->get('/faq');
        $response->assertStatus(200);
        $response->assertSeeText('FAQ & Submission Guidelines');
        $response->assertSee('Sign In');
    }

    public function test_faq_page_does_not_leak_user_identity_across_sessions_or_guests()
    {
        $this->seed();
        $admin = User::where('email', 'dean@example.test')->first();
        $committee = User::where('email', 'reviewer@example.test')->first();

        // 1. Admin visits FAQ
        $resAdmin = $this->actingAs($admin)->get('/faq');
        $resAdmin->assertSeeText($admin->name);

        // 2. Committee member visits FAQ - must see Committee member name, not Admin
        $resCommittee = $this->actingAs($committee)->get('/faq');
        $resCommittee->assertSeeText($committee->name);
        $resCommittee->assertDontSeeText($admin->name);

        // 3. Guest visits FAQ - must see Sign In, not Admin or Committee member names
        auth()->logout();
        $resGuest = $this->get('/faq');
        $resGuest->assertSee('Sign In');
        $resGuest->assertDontSeeText($admin->name);
        $resGuest->assertDontSeeText($committee->name);
    }

    public function test_login_page_renders_hero_quick_signin_and_vertical_steps()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Ethics Review Board Portal');
        $response->assertSee('Sign in with Google');
        $response->assertSeeText('Ethics Review & Clearance Process');
        $response->assertSee('Protocol Submission');
        $response->assertSee('Committee Review');
        $response->assertSee('Document Revision');
        $response->assertSee('Ethical Clearance');
        $response->assertSee('href="#steps"', false);
    }

    public function test_login_page_is_cached_with_etags_and_bypasses_on_session_error()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $this->assertStringContainsString('public', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('max-age=120', $response->headers->get('Cache-Control'));
        $this->assertNotEmpty($response->headers->get('ETag'));

        // When a flash session error is present, cache-control should bypass with no-cache
        $responseWithError = $this->withSession(['error' => 'Invalid email domain.'])->get('/');
        $responseWithError->assertStatus(200);
        $responseWithError->assertSee('Invalid email domain.');
        $this->assertStringContainsString('no-cache', $responseWithError->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-store', $responseWithError->headers->get('Cache-Control'));
    }

    public function test_grant_access_artisan_command_provisions_roles_developer_and_chairperson()
    {
        $this->seed();

        // 1. Grant developer and admin role to an existing or new user via CLI
        $this->artisan('erb:grant-access', [
            'email' => 'newfaculty@example.test',
            '--role' => 'admin',
            '--developer' => true,
            '--chairperson' => true,
            '--activate' => true,
        ])->assertSuccessful();

        $user = User::where('email', 'newfaculty@example.test')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_active);
        $this->assertTrue($user->is_developer);
        $this->assertTrue($user->is_chairperson);
        $this->assertTrue($user->hasRole('admin'));

        // 2. Revoke chairperson and developer status via CLI
        $this->artisan('erb:grant-access', [
            'email' => 'newfaculty@example.test',
            '--no-developer' => true,
            '--no-chairperson' => true,
            '--role' => 'committee',
        ])->assertSuccessful();

        $user->refresh();
        $this->assertFalse($user->is_developer);
        $this->assertFalse($user->is_chairperson);
        $this->assertTrue($user->hasRole('committee'));
    }
}
