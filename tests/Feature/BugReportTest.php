<?php

namespace Tests\Feature;

use App\Livewire\BugReportWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BugReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_bug_report(): void
    {
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('student');

        $this->actingAs($user);

        Livewire::test(BugReportWidget::class)
            ->call('openModal')
            ->set('category', 'bug')
            ->set('subject', 'Test upload issue on mobile')
            ->set('description', 'The upload dropzone button did not trigger file selection on Safari iOS.')
            ->call('submitReport')
            ->assertSet('submitted', true);

        $this->assertDatabaseHas('bug_reports', [
            'user_id' => $user->id,
            'category' => 'bug',
            'subject' => 'Test upload issue on mobile',
        ]);
    }
}
