<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_error_page_renders_custom_pwu_layout(): void
    {
        $response = $this->get('/non-existent-route-for-testing-404');

        $response->assertStatus(404);
        $response->assertSeeText('Page or Document Not Found');
        $response->assertSeeText('Error 404');
        $response->assertSeeText('Return to Dashboard');
        $response->assertDontSee('Sentry');
        $response->assertDontSee('Exception');
        $response->assertDontSee('Stack trace');
    }

    public function test_403_error_view_renders_clean_message(): void
    {
        $student = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $student->assignRole('student');

        // Student accessing developer panel returns 403 / 404
        $response = $this->actingAs($student)->get('/admin/users');

        $response->assertStatus(403);
        $response->assertSeeText('Access Restricted');
        $response->assertSeeText('Error 403');
    }

    public function test_custom_error_pages_contain_no_emojis_and_use_montserrat(): void
    {
        $views = ['errors.403', 'errors.404', 'errors.419', 'errors.500', 'errors.503'];

        foreach ($views as $view) {
            $html = view($view)->render();

            $this->assertStringContainsString('font-heading', $html);
            $this->assertStringContainsString('Example Research Institution', $html);
            $this->assertStringNotContainsString('Sentry', $html);

            // Strict zero-emoji check
            $hasEmoji = preg_match('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{1F1E0}-\x{1F1FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', $html);
            $this->assertEquals(0, $hasEmoji, "Error view {$view} must not contain emojis.");
        }
    }
}
