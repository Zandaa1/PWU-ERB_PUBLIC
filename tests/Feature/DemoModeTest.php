<?php

namespace Tests\Feature;

use App\Enums\RequestStatus;
use App\Livewire\StudentDashboard;
use App\Models\Department;
use App\Models\EthicsRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            RoleSeeder::class,
            DepartmentSeeder::class,
            DocumentTypeSeeder::class,
            UserSeeder::class,
        ]);
    }

    public function test_dev_login_works_when_demo_mode_is_enabled(): void
    {
        config(['app.demo_mode' => true]);

        $response = $this->get('/auth/dev-login/student');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('student@example.test', auth()->user()->email);
    }

    public function test_dev_login_returns_404_when_demo_mode_is_disabled(): void
    {
        config(['app.demo_mode' => false]);

        $response = $this->get('/auth/dev-login/student');
        $response->assertStatus(404);
        $this->assertGuest();
    }

    public function test_dev_login_works_for_secretary_when_demo_mode_is_enabled(): void
    {
        config(['app.demo_mode' => true]);

        $response = $this->get('/auth/dev-login/secretary');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('secretary@example.test', auth()->user()->email);
        $this->assertTrue(auth()->user()->hasRole('secretary'));
    }

    public function test_developer_page_shows_quick_logins_only_when_demo_mode_is_enabled(): void
    {
        config(['app.demo_mode' => true]);
        $response = $this->get('/developer');
        $response->assertStatus(200);
        $response->assertSee('Developer Console');
        $response->assertSee('Demo Student');
        $response->assertSee('/auth/dev-login/student');
        $response->assertSee('Demo Secretary');
        $response->assertSee('/auth/dev-login/secretary');

        config(['app.demo_mode' => false]);
        $response = $this->get('/developer');
        $response->assertStatus(404);

        // Public / login page never shows dev logins
        $publicResponse = $this->get('/');
        $publicResponse->assertDontSee('/auth/dev-login/student');
    }

    public function test_authenticated_user_is_redirected_away_from_developer_page(): void
    {
        config(['app.demo_mode' => true]);
        $student = User::where('email', 'student@example.test')->first();

        $response = $this->actingAs($student)->get('/developer');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('info');
    }

    public function test_active_google_user_cannot_switch_to_dev_persona_without_signing_out(): void
    {
        config(['app.demo_mode' => true]);
        $googleUser = User::factory()->create([
            'email' => 'real.student@example.test',
            'google_id' => 'google-oauth-unique-id-9988',
        ]);
        $googleUser->assignRole('student');

        $response = $this->actingAs($googleUser)->get('/auth/dev-login/admin');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
        $this->assertEquals($googleUser->id, auth()->id());
    }

    public function test_google_redirect_fallback_behavior(): void
    {
        config(['services.google.client_id' => 'mock-google-client-id']);

        // Demo mode enabled -> redirects to dev-login
        config(['app.demo_mode' => true]);
        $response = $this->get('/auth/google');
        $response->assertRedirect('/auth/dev-login/student');

        // Demo mode disabled -> redirects to login with error message
        config(['app.demo_mode' => false]);
        $response = $this->get('/auth/google');
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_student_claim_sample_request_action_guarded(): void
    {
        $student = User::where('email', 'student@example.test')->first();
        $dept = Department::first();

        $request = EthicsRequest::create([
            'title' => 'Test Thesis Study',
            'department_id' => $dept->id,
            'student_id' => $student->id,
            'status' => RequestStatus::UnderReview,
        ]);

        $otherStudent = User::factory()->create(['email' => 'other@example.test']);
        $otherStudent->assignRole('student');

        // Demo mode enabled -> can claim
        config(['app.demo_mode' => true]);
        Livewire::actingAs($otherStudent)
            ->test(StudentDashboard::class)
            ->call('claimSampleRequest')
            ->assertStatus(200);

        $this->assertEquals($otherStudent->id, $request->fresh()->student_id);

        // Demo mode disabled -> throws 403
        config(['app.demo_mode' => false]);
        Livewire::actingAs($student)
            ->test(StudentDashboard::class)
            ->call('claimSampleRequest')
            ->assertForbidden();
    }

    public function test_student_dashboard_renders_claim_button_only_when_demo_mode_enabled(): void
    {
        $student = User::where('email', 'student@example.test')->first();

        config(['app.demo_mode' => true]);
        Livewire::actingAs($student)
            ->test(StudentDashboard::class)
            ->assertSee('Load Sample Research Protocol');

        config(['app.demo_mode' => false]);
        Livewire::actingAs($student)
            ->test(StudentDashboard::class)
            ->assertDontSee('Load Sample Research Protocol');
    }

    public function test_database_seeder_respects_demo_mode(): void
    {
        // When demo_mode is false, SampleApprovableRequestSeeder is not called
        config(['app.demo_mode' => false]);
        EthicsRequest::query()->delete();
        $this->seed(DatabaseSeeder::class);
        $this->assertEquals(0, EthicsRequest::count());

        // When demo_mode is true, SampleApprovableRequestSeeder is called
        config(['app.demo_mode' => true]);
        EthicsRequest::query()->delete();
        $this->seed(DatabaseSeeder::class);
        $this->assertGreaterThan(0, EthicsRequest::count());
    }
}
