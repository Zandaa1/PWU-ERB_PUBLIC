<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CloudflareTunnelSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'student']);
    }

    public function test_cloudflare_tunnel_headers_are_trusted_and_detected_as_https(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('student');

        $response = $this->withHeaders([
            'X-Forwarded-For' => '203.0.113.195',
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Host' => 'pwu-erb-tunnel.trycloudflare.com',
            'X-Forwarded-Port' => '443',
            'CF-Connecting-IP' => '203.0.113.195',
            'CF-Ray' => '8b9c1d2e3f4a5b6c-MNL',
        ])->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $this->assertTrue(request()->isSecure());
        $this->assertEquals('pwu-erb-tunnel.trycloudflare.com', request()->getHost());
        $this->assertEquals('203.0.113.195', request()->getClientIp());
    }

    public function test_cloudflare_tunnel_visitor_header_forces_https(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole('student');

        $response = $this->withHeaders([
            'CF-Visitor' => '{"scheme":"https"}',
            'X-Forwarded-Host' => 'pwu-erb.yourdomain.com',
        ])->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }
}
