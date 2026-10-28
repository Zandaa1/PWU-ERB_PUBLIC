<?php

namespace Tests\Feature;

use App\Livewire\Developer\DevPanel;
use App\Mail\EthicsRequestSubmittedMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DevPanelEmailDiagnosticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'committee', 'guard_name' => 'web']);
    }

    public function test_developer_can_trigger_test_email_from_dev_panel(): void
    {
        Mail::fake();

        $dev = User::factory()->create([
            'email' => 'dev@example.test',
            'is_developer' => true,
            'is_active' => true,
        ]);
        $dev->assignRole('admin');

        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->set('testEmailRecipient', 'test.recipient@example.test')
            ->call('sendTestResendEmail')
            ->assertSeeHtml('Test email successfully dispatched');

        Mail::assertSent(EthicsRequestSubmittedMail::class, function ($mail) {
            return $mail->hasTo('test.recipient@example.test');
        });
    }

    public function test_recipient_email_format_is_validated(): void
    {
        Mail::fake();

        $dev = User::factory()->create([
            'email' => 'dev@example.test',
            'is_developer' => true,
            'is_active' => true,
        ]);
        $dev->assignRole('admin');

        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->set('testEmailRecipient', 'invalid-email-address')
            ->call('sendTestResendEmail')
            ->assertHasErrors(['testEmailRecipient']);

        Mail::assertNothingSent();
    }

    public function test_recipient_email_is_required(): void
    {
        Mail::fake();

        $dev = User::factory()->create([
            'email' => 'dev@example.test',
            'is_developer' => true,
            'is_active' => true,
        ]);
        $dev->assignRole('admin');

        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->set('testEmailRecipient', '')
            ->call('sendTestResendEmail')
            ->assertHasErrors(['testEmailRecipient']);

        Mail::assertNothingSent();
    }

    public function test_non_developer_cannot_access_dev_panel(): void
    {
        $student = User::factory()->create([
            'email' => 'student@example.test',
            'is_developer' => false,
            'is_active' => true,
        ]);
        $student->assignRole('student');

        Livewire::actingAs($student)
            ->test(DevPanel::class)
            ->assertForbidden();

        $this->actingAs($student)
            ->get('/admin/dev')
            ->assertForbidden();
    }

    public function test_dev_panel_view_renders_email_diagnostics_ui_card_and_loading_states(): void
    {
        $dev = User::factory()->create([
            'email' => 'dev@example.test',
            'is_developer' => true,
            'is_active' => true,
        ]);
        $dev->assignRole('admin');

        Livewire::actingAs($dev)
            ->test(DevPanel::class)
            ->assertSeeHtml('Resend Transactional Email Diagnostics')
            ->assertSeeHtml('wire:target="sendTestResendEmail"')
            ->assertSeeHtml('wire:loading');
    }
}
