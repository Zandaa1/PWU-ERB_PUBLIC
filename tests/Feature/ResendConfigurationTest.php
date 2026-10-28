<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResendConfigurationTest extends TestCase
{
    public function test_resend_mailer_is_configured_with_symfony_transport(): void
    {
        $this->assertEquals('resend', Config::get('mail.mailers.resend.transport'));
        $this->assertArrayHasKey('resend', Config::get('services'));
        $this->assertArrayHasKey('key', Config::get('services.resend'));
    }

    public function test_resend_transport_can_be_resolved_by_mail_manager(): void
    {
        Config::set('services.resend.key', 'synthetic-local-fixture');
        Config::set('mail.default', 'resend');

        $driver = Mail::mailer('resend')->getSymfonyTransport();
        $this->assertNotNull($driver);
    }
}
