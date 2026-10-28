<?php

namespace Tests\Feature;

use App\Models\SystemAnnouncement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnnouncementBannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_announcement_renders_with_expand_toggle_for_long_messages(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $user = User::factory()->create(['email' => 'student@example.test', 'is_active' => true]);
        $user->assignRole('student');

        $longMessage = 'The DEMO Ethics Review Board Portal is currently undergoing scheduled system updates and database maintenance. All student thesis protocols, data collection instruments, and official clearance certificates will remain accessible, but document uploads may experience minor processing delays. Please plan your submissions accordingly.';

        SystemAnnouncement::create([
            'type' => 'maintenance',
            'title' => 'Scheduled Maintenance Warning',
            'message' => $longMessage,
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get('/faq');
        $response->assertSee('Scheduled Maintenance Warning')
            ->assertSee($longMessage);
    }

    public function test_login_page_renders_active_announcement_with_full_message_and_no_cut_off(): void
    {
        $longOutageMessage = 'Temporary System Service Outage: Our document processing service is currently experiencing an unexpected outage. PDF generation and document streaming might be temporarily unavailable while our technical team resolves the issue. We apologize for any inconvenience.';

        SystemAnnouncement::create([
            'type' => 'down',
            'title' => 'Temporary System Service Outage',
            'message' => $longOutageMessage,
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200)
            ->assertSee('Outage')
            ->assertSee('Temporary System Service Outage')
            ->assertSee($longOutageMessage)
            ->assertDontSee('truncate');
    }
}
