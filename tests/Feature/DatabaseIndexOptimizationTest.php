<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseIndexOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_indexes_exist_on_high_traffic_columns(): void
    {
        $this->assertTrue(Schema::hasIndex('ethics_requests', ['status', 'department_id']));
        $this->assertTrue(Schema::hasIndex('ethics_requests', ['student_id', 'created_at']));
        $this->assertTrue(Schema::hasIndex('notifications', ['user_id', 'is_read']));
        $this->assertTrue(Schema::hasIndex('request_documents', ['ethics_request_id', 'status']));
        $this->assertTrue(Schema::hasIndex('status_logs', ['ethics_request_id']));
    }
}
