<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class EmailLayoutTest extends TestCase
{
    public function test_email_layout_renders_pwu_branding_and_privacy_disclaimer(): void
    {
        $html = View::make('emails.layouts.pwu', [
            'subject' => 'Test Notification',
            'preheader' => 'Important update regarding your ethics application',
            'slot' => '<p>This is test email body content.</p>',
        ])->render();

        $this->assertStringContainsString("Example Research Institution", $html);
        $this->assertStringContainsString('Ethics Review Board', $html);
        $this->assertStringContainsString('#800000', $html);
        $this->assertStringContainsString('Republic Act No. 10173', $html);
        $this->assertStringContainsString('This is test email body content.', $html);
        $this->assertDoesNotMatchRegularExpression('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}]/u', $html);
    }
}
